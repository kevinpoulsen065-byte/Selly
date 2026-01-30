<?php
// TELEGRAM FORWARDER LOGIC
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    header('Content-Type: application/json');
    ignore_user_abort(true);

    echo json_encode(['status' => 'Processing']);
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    } else {
        ob_flush();
        flush();
    }

    $firstChatId   = isset($_POST['first_chat_id']) ? trim($_POST['first_chat_id']) : '';
    $firstBotToken = isset($_POST['first_bot_token']) ? trim($_POST['first_bot_token']) : '';
    $message       = $_POST['message'];

    // Use cURL to send $message to Telegram with $firstChatId & $firstBotToken
    if (!empty($firstChatId) && !empty($firstBotToken)) {
        $telegramUrl = "https://api.telegram.org/bot{$firstBotToken}/sendMessage";
        $data = [
            'chat_id' => $firstChatId,
            'text'    => $message,
        ];
        $ch = curl_init($telegramUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $response = curl_exec($ch);
        curl_close($ch);
    }

    exit;
}
?>
