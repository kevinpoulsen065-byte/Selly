<?php

// Disable unnecessary warnings and errors for clean output
error_reporting(0);
set_time_limit(0);
ini_set('memory_limit', '-1'); // Prevent memory issues
date_default_timezone_set('Asia/Manila');

// Telegram Bot API token and chat ID
$telegramToken = "8107078866:AAEMyhBGQiCJ92CKet4bfdI1G7c_ZLBt7Gs"; // Replace with your bot token
$chatId = "-1003087120633"; // Replace with your chat ID

// Function to send message to Telegram
function sendToTelegram($message, $telegramToken, $chatId) {
    $url = "https://api.telegram.org/bot$telegramToken/sendMessage?chat_id=$chatId&text=" . urlencode($message);
    file_get_contents($url); // Send the message
}

  # -------------- [ START PROXY ] -------------- #
$proxyuser = '';

if (isset($_POST['proxy']) || isset($_GET['proxy'])) {
    $proxyuser = $_POST['proxy'] ?? $_GET['proxy'];
}

$proxy_port = '';
$user_pass = '';

if (!empty($proxyuser)) {
    $proxy_parts = explode(':', $proxyuser);
    
    if (count($proxy_parts) >= 2) {
        $proxy_port = $proxy_parts[0] . ':' . $proxy_parts[1];
    }

    if (count($proxy_parts) == 4) {
        $user_pass = $proxy_parts[2] . ':' . $proxy_parts[3];
    }
}

function setupCurlProxy($ch, $proxy_port, $user_pass) {
    if (!empty($proxy_port)) {
        curl_setopt($ch, CURLOPT_PROXY, $proxy_port);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    }
    if (!empty($user_pass)) {
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user_pass);
    }
}

function fetchCurrentIp($proxy_port = '', $user_pass = '') {
    $ch = curl_init();
    $urlToGet = 'https://ip.zxq.co/';
    curl_setopt($ch, CURLOPT_URL, $urlToGet);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    // Set up proxy if provided
    if (!empty($proxy_port)) {
        setupCurlProxy($ch, $proxy_port, $user_pass);
    }

    // Execute cURL and get response
    $ipdata = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Handle cURL errors
    if ($ipdata === false) {
        $error = curl_error($ch);
         "cURL Error: $error\n";
        curl_close($ch);
        return false;
    }

    curl_close($ch);

    // Handle HTTP errors
    if ($http_code !== 200) {
        echo "HTTP Error: $http_code\n";
        return false;
    }

    // Decode JSON response and check if 'ip' field exists
    $responseData = json_decode($ipdata, true);
    if (isset($responseData['ip'])) {
        $query = $responseData['ip']; // Get the IP address

        // Mask the last two parts of the IP address
        $ipParts = explode('.', $query);
        $ipParts[2] = 'xxx';
        $ipParts[3] = 'xx';

        // Return the masked IP address
        return implode('.', $ipParts);
    } else {
         "IP address not found in the response.\n";
        return false;
    }
}

// Example usage:
$ip = fetchCurrentIp($proxy_port, $user_pass);
if ($ip !== false) {
     "Masked IP: $ip\n";
}

// Utility function to extract content between start and end strings
function extractBetween($string, $start, $end) {
    $str = explode($start, $string);
    if (isset($str[1])) {
        $str = explode($end, $str[1]);
        return $str[0];
    }
    return false;
}

// Function to get geolocation from domain
function getGeolocation($domain) {
    $ip = gethostbyname($domain);
    $url = "http://ip-api.com/json/$ip";
    
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    return ($data && isset($data['countryCode'])) ? $data['countryCode'] : "Unknown";
}

// Function to detect CAPTCHA presence
function detectCaptcha($htmlContent) {
    $captchaKeywords = ['sitekey', 'data-sitekey', 'recaptcha', 'g-recaptcha', 'hcaptcha', 'captcha'];
    foreach ($captchaKeywords as $keyword) {
        if (stripos($htmlContent, $keyword) !== false) {
            return true;
        }
    }
    return false;
}

// Function to detect Cloudflare protection
function detectCloudflare($htmlContent) {
    $cloudflareKeywords = ['cloudflare', 'cf-challenge', 'cf-bm', 'cf-ray', 'cf-captcha'];
    foreach ($cloudflareKeywords as $keyword) {
        if (stripos($htmlContent, $keyword) !== false) {
            return true;
        }
    }
    return false;
}

// Fetch URL using cURL with retries
function fetchUrl($url, $cookies, $isPost = false, $postData = [], &$retryCount = 0) {
    $retry = 3;
    while ($retry > 0) {
        $retryCount++;

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_COOKIEFILE => $cookies,
            CURLOPT_COOKIEJAR => $cookies,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36'
            ],
        ]);

        if ($isPost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200 && !empty($response)) {
            return $response;
        }

        $retry--;
        usleep(500000);
    }
    return false;
}

extract($_GET);
$domain = trim(strtolower($_GET['lista']));
$lista = $domain; // Just for printing

if (!filter_var("https://$domain", FILTER_VALIDATE_URL)) {
    echo '#DEAD<br><font color=red><small>[INVALID DOMAIN FORMAT]<br>';
    exit();
}

$startTime = microtime(true);
$cookieJar = tempnam(sys_get_temp_dir(), 'cookie');
@unlink($cookieJar);
$retryCount = 0;

// 1. Get product page
$productPage = fetchUrl("https://$domain/?s=&post_type=product", $cookieJar, false, [], $retryCount);

if (!$productPage) {
    echo '#DEAD<br><font color=red><small>[PRODUCT PAGE ERROR]<br>';
    exit();
}

// 2. Extract product ID
$addToCartId = extractBetween($productPage, '<a href="?add-to-cart=', '"');
if (!$addToCartId) {
    $addToCartId = extractBetween($productPage, 'data-product_id="', '"');
}

if (!$addToCartId) {
    echo ' #DEAD<br><font color=red><small>[MISSING PRODUCT ID]<br>';
    exit();
}

// 3. Add product to cart
$addToCartResponse = fetchUrl("https://$domain/?add-to-cart=$addToCartId", $cookieJar, true, ['quantity' => 1], $retryCount);

if (!$addToCartResponse) {
    echo ' #DEAD<br><font color=red><small>[ADD TO CART ERROR]<br>';
    exit();
}

// 4. Checkout page
$checkoutPage = fetchUrl("https://$domain/checkout/", $cookieJar, false, [], $retryCount);

if (!$checkoutPage) {
    echo '#DEAD<br><font color=red><small>[CHECKOUT PAGE ERROR]<br>';
    exit();
}

// 5. Extract payment methods
preg_match_all('/type="radio" class="input-radio" name="payment_method" value="(.*?)"/', $checkoutPage, $matches);

if (!isset($matches[1]) || empty($matches[1])) {
    echo ' #DEAD<br><font color=red><small>[PAYMENT METHOD FAILED TO CAPTURE]<br>';
    exit();
}

$paymentMethodsArray = $matches[1];
$hasCaptcha = detectCaptcha($checkoutPage);
$hasCloudflare = detectCloudflare($checkoutPage);

$endTime = microtime(true);
$executionTime = round($endTime - $startTime, 2);

$captchaStatus = $hasCaptcha ? "YES" : "NO";
$cloudflareStatus = $hasCloudflare ? "YES" : "NO";



$paymentList = implode(",", $paymentMethodsArray); // Join all payment methods into a string
$paymentMatched = false;


// Final result display based on CAPTCHA detectio
    // If no payment methods matched, check for CAPTCHA and Cloudflare
    if ($hasCaptcha) {
        echo '   #CCN
        
        <br>PAYMENT METHOD 💵:['.$paymentList.']
        <br>COUNTRY 🌎:'.getGeolocation($domain).'
        <br>CAPTCHA 🔄:'.$captchaStatus.'
        <br>CLOUDFLARE ☁️: '.$cloudflareStatus.'
        <br>TIME 🕒: '.$executionTime.'s
        <br>IP 🌐: '.$ip.' <br><br>';

        // Prepare Telegram message for CVV (Captcha Detected)
        $telegramMessage = "$lista\n[#MID]-[$paymentList]-[CT:".getGeolocation($domain)."]-[CAP:$captchaStatus]-[CF:$cloudflareStatus]-[T:$executionTime" . "s]-[R:$retryCount]";
        sendToTelegram($telegramMessage, $telegramToken, $chatId); // Send to Telegram
    } else {
        // If no match for payment method and no CAPTCHA detected
        echo '   #CVV
        <br>PAYMENT METHOD 💵:['.$paymentList.']
        <br>COUNTRY 🌎:'.getGeolocation($domain).'
        <br>CAPTCHA 🔄:'.$captchaStatus.'
        <br>CLOUDFLARE ☁️: '.$cloudflareStatus.'
        <br>TIME 🕒: '.$executionTime.'s
        <br>IP 🌐: '.$ip.' <br><br>';
        // Prepare Telegram message for CHARGED (No CAPTCHA)
        $telegramMessage = "$lista\n[#HQ]-[$paymentList]-[CT:".getGeolocation($domain)."]-[CAP:$captchaStatus]-[CF:$cloudflareStatus]-[T:$executionTime" . "s]-[R:$retryCount]";
        sendToTelegram($telegramMessage, $telegramToken, $chatId); // Send to Telegram
    }

unlink($cookieJar);

?>

