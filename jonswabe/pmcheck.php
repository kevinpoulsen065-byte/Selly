<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

class PMChecker {
    private $user_agents = [
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
        "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"
    ];

    private $payment_gateways = [
        'stripe', 'paypal', 'braintree', 'square', 'revolut', 'authorize', 'nmi',
        'ppcp-gateway', 'stripe_cc', 'paypal_express', 'woocommerce_payments',
        'paystack', 'flutterwave', 'razorpay', 'adyen', 'klarna', 'authorizenet',
        'bluepay', 'cybersource', 'first_data', 'moneris', 'worldpay', 'checkout.com',
        'mercadopago', 'sagepay', 'securepay', 'squareup', 'usaepay'
    ];

    public function check_domain($domain) {
        $start_time = microtime(true);
        
        // Clean domain
        $domain = strtolower(trim($domain));
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = explode('/', $domain)[0];

        try {
            // Step 1: Fetch product page
            $product_url = "https://{$domain}/?s=&post_type=product";
            $product_page = $this->fetch_url($product_url);
            
            if (!$product_page) {
                return $this->create_result($domain, 'DEAD', [], 'Unknown', false, false, 
                    microtime(true) - $start_time, 'Product page fetch failed');
            }

            // Step 2: Find product ID
            $product_id = $this->find_product_id($product_page);
            
            if (!$product_id) {
                return $this->create_result($domain, 'DEAD', [], 'Unknown', false, false,
                    microtime(true) - $start_time, 'No product ID found');
            }

            // Step 3: Add to cart
            $cart_url = "https://{$domain}/?add-to-cart={$product_id}";
            $cart_response = $this->fetch_url($cart_url);
            
            if (!$cart_response) {
                return $this->create_result($domain, 'DEAD', [], 'Unknown', false, false,
                    microtime(true) - $start_time, 'Add to cart failed');
            }

            // Step 4: Fetch checkout page
            $checkout_url = "https://{$domain}/checkout/";
            $checkout_page = $this->fetch_url($checkout_url);
            
            if (!$checkout_page) {
                return $this->create_result($domain, 'DEAD', [], 'Unknown', false, false,
                    microtime(true) - $start_time, 'Checkout page fetch failed');
            }

            // Step 5: Extract payment methods
            $payment_methods = $this->extract_payment_methods($checkout_page);
            
            if (empty($payment_methods)) {
                return $this->create_result($domain, 'DEAD', [], 'Unknown', false, false,
                    microtime(true) - $start_time, 'No payment methods found');
            }

            // Step 6: Detect protections
            $has_captcha = $this->detect_captcha($checkout_page);
            $has_cloudflare = $this->detect_cloudflare($checkout_page);
            $geolocation = $this->get_geolocation($domain);
            
            // Categorize
            $category = ($has_captcha || $has_cloudflare) ? 'MID' : 'HQ';
            
            $execution_time = round(microtime(true) - $start_time, 2);
            
            return $this->create_result($domain, $category, $payment_methods, $geolocation,
                $has_captcha, $has_cloudflare, $execution_time);

        } catch (Exception $e) {
            return $this->create_result($domain, 'DEAD', [], 'Unknown', false, false,
                microtime(true) - $start_time, 'Exception: ' . $e->getMessage());
        }
    }

    private function fetch_url($url) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agents[array_rand($this->user_agents)]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_COOKIEJAR, tempnam(sys_get_temp_dir(), 'cookie'));
        curl_setopt($ch, CURLOPT_COOKIEFILE, tempnam(sys_get_temp_dir(), 'cookie'));
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return ($http_code == 200 && $response) ? $response : null;
    }

    private function find_product_id($html) {
        if (!$html) return null;

        // Strategy 1: add-to-cart links
        if (preg_match('/add-to-cart=(\d+)/', $html, $matches)) {
            return $matches[1];
        }

        // Strategy 2: data attributes
        if (preg_match('/data-product[_-]?id=["\']?(\d+)/', $html, $matches)) {
            return $matches[1];
        }

        // Strategy 3: button value
        if (preg_match('/<button[^>]+name=["\']add-to-cart["\'][^>]+value=["\'](\d+)["\']/', $html, $matches)) {
            return $matches[1];
        }

        // Strategy 4: input value
        if (preg_match('/<input[^>]+name=["\']add-to-cart["\'][^>]+value=["\'](\d+)["\']/', $html, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function extract_payment_methods($html) {
        $methods = [];
        
        if (!$html) return $methods;

        // Extract from input radio buttons
        if (preg_match_all('/<input[^>]+name=["\']payment_method["\'][^>]+value=["\']([^"\']+)["\']/', $html, $matches)) {
            $methods = array_merge($methods, $matches[1]);
        }

        // Extract from select options
        if (preg_match_all('/<option[^>]+value=["\']([^"\']+)["\'][^>]*>/', $html, $matches)) {
            foreach ($matches[1] as $value) {
                if (!empty($value) && strlen($value) > 2) {
                    $methods[] = $value;
                }
            }
        }

        // Search for known gateways in text
        $html_lower = strtolower($html);
        foreach ($this->payment_gateways as $gateway) {
            if (strpos($html_lower, $gateway) !== false) {
                $methods[] = $gateway;
            }
        }

        return array_unique($methods);
    }

    private function detect_captcha($html) {
        if (!$html) return false;
        
        $captcha_keywords = ['sitekey', 'data-sitekey', 'recaptcha', 'g-recaptcha', 'hcaptcha', 'captcha', 'cf-captcha'];
        $html_lower = strtolower($html);
        
        foreach ($captcha_keywords as $keyword) {
            if (strpos($html_lower, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    private function detect_cloudflare($html) {
        if (!$html) return false;
        
        $cf_keywords = ['cloudflare', 'cf-challenge', 'cf-bm', 'cf-ray', 'cf-captcha', 'checking your browser'];
        $html_lower = strtolower($html);
        
        foreach ($cf_keywords as $keyword) {
            if (strpos($html_lower, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    private function get_geolocation($domain) {
        try {
            // Get IP from domain
            $ip = gethostbyname($domain);
            
            if ($ip === $domain) {
                return 'Unknown';
            }

            // Get geolocation from IP
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=countryCode");
            
            if ($response) {
                $data = json_decode($response, true);
                return $data['countryCode'] ?? 'Unknown';
            }
            
            return 'Unknown';
        } catch (Exception $e) {
            return 'Unknown';
        }
    }

    private function create_result($domain, $category, $payment_methods, $geolocation, 
                                   $captcha, $cloudflare, $execution_time, $error = null) {
        return [
            'domain' => $domain,
            'category' => $category,
            'payment_methods' => $payment_methods,
            'geolocation' => $geolocation,
            'captcha' => $captcha,
            'cloudflare' => $cloudflare,
            'execution_time' => $execution_time,
            'error_message' => $error
        ];
    }
}

// Handle the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['domains'])) {
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    $domains = $input['domains'];
    $checker = new PMChecker();
    $results = [];

    foreach ($domains as $domain) {
        $result = $checker->check_domain(trim($domain));
        $results[] = $result;
    }

    echo json_encode(['results' => $results]);
} else {
    echo json_encode(['error' => 'Method not allowed']);
}
?>
