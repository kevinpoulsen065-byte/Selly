<?php

ini_set("error_reporting", E_ERROR | E_PARSE);
ini_set("max_execution_time", "0");

extract($_GET);
$lista = $_GET['lista'];

$startTime = microtime(true); // Start execution time tracking
$timestamp = intval(time());

$parsedUrl = parse_url($lista);
$domain = $parsedUrl['host'];


// Telegram Bot API token and chat ID
$telegramToken = "8107078866:AAEMyhBGQiCJ92CKet4bfdI1G7c_ZLBt7Gs"; // Replace with your bot token
$chatId = "-1003087120633"; // Replace with your chat ID

// Function to send message to Telegram
function sendToTelegram($message, $telegramToken, $chatId) {
    $url = "https://api.telegram.org/bot$telegramToken/sendMessage?chat_id=$chatId&text=" . urlencode($message);
    file_get_contents($url); // Send the message
}

// Function to fetch HTML content from a URL
function fetch_html_content($url) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_USERAGENT => $_SERVER["HTTP_USER_AGENT"],
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT => 30
    ]);
    $htmlContent = curl_exec($ch);
    curl_close($ch);

    if ($htmlContent === false || empty($htmlContent)) {
        return '';
    }

    return $htmlContent;
}

// CAPTCHA Detection (on /checkout page)
$captchaDetected = false;
$htmlContent = fetch_html_content("https://$domain/checkout/");
if (stripos($htmlContent, 'sitekey') !== false || stripos($htmlContent, 'data-sitekey') !== false || stripos($htmlContent, 'siteKey') !== false) {
    $captchaDetected = true;
}
$captchaStatus = $captchaDetected ? "Yes" : "No";

function keum($string, $string1, $string2){
    return trim(explode($string2, explode($string1, $string)[1])[0]);
}

// Cookie file handling for curl
$cookies = tempnam(sys_get_temp_dir(), 'cookie');

// Create guest cart
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://$domain/rest/V1/guest-carts",
    CURLOPT_USERAGENT => $_SERVER["HTTP_USER_AGENT"],
    CURLOPT_POST => 1,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_HEADER => 1,
    CURLOPT_COOKIEFILE => $cookies,
    CURLOPT_COOKIEJAR => $cookies,
    CURLOPT_HTTPHEADER => [
        "Accept: */*",
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => "{}"
]);

$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($result, 0, $header_size);
$body = substr($result, $header_size);

curl_close($ch);

if (strpos($headers, 'Server: cloudflare') !== false || 
    strpos($body, 'Cloudflare') !== false || 
    $http_code == 403 || $http_code == 503) {
    $endTime = microtime(true);
    $timeTaken = round($endTime - $startTime, 2);
    exit("#DEAD<br><font color=red><small>[CLOUDFLARE][T:".$timeTaken."s]<br>");
}

if ($http_code == 405) {
    $endTime = microtime(true);
    $timeTaken = round($endTime - $startTime, 2);
    exit("#DEAD<br><font color=red><small>[GUEST CHECKOUT ERROR][T:".$timeTaken."s]<br>");
}

$cartID = keum($body, '"', '"');

// Request available payment methods via GraphQL
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://$domain/graphql",
    CURLOPT_USERAGENT => $_SERVER["HTTP_USER_AGENT"],
    CURLOPT_POST => 1,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_COOKIEFILE => $cookies,
    CURLOPT_COOKIEJAR => $cookies,
    CURLOPT_HTTPHEADER => [
        "Accept: */*",
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => json_encode([
        "query" => "query { cart(cart_id: \"$cartID\") { available_payment_methods { code title } } }"
    ])
]);

$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 405) {
    $endTime = microtime(true);
    $timeTaken = round($endTime - $startTime, 2);
    exit("#DEAD<br><font color=red><small>[ERROR 405][T:".$timeTaken."s]<br>");
}

$response = json_decode($result, true);
$payment_methods = $response['data']['cart']['available_payment_methods'] ?? [];

$endTime = microtime(true);
$timeTaken = round($endTime - $startTime, 2);

if (!empty($payment_methods)) {
    $codes = array_column($payment_methods, 'code');
    $payment_list = implode(', ', $codes);

  
    } else {
        if ($captchaDetected) {
            echo "#CVV
            <br><font color=yellow>[#MID][$payment_list][CAP:Yes][T:$timeTaken s]<br>";

            $telegramMessage = "$lista\n[#MID]-[$payment_list]-[CAP:Yes]-[T:$timeTaken" . "s]";
            sendToTelegram($telegramMessage, $telegramToken, $chatId); // Send to Telegram
        } else {
            echo "#CHARGED
            <br><font color=green>[#HQ][$payment_list][CAP:No][T:$timeTaken s]<br>";

            $telegramMessage = "$lista\n[#HQ]-[$payment_list]-[CAP:No]-[T:$timeTaken" . "s]";
        sendToTelegram($telegramMessage, $telegramToken, $chatId); // Send to Telegram
        }
    
} else {
    exit("#DEAD<br><font color=red><small>[FAILED TO DETECT PAYMENT METHOD][T:$timeTaken s]<br>");
}

?>
