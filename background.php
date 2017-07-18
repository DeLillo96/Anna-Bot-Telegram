<?php

require_once __DIR__ . '/vendor/autoload.php';

$userId = intval($argv[1]);
$chatId = intval($argv[2]);

if($userId == null || $chatId == null) {
    error_log('missing parameters');
    exit;
}

while (true){
    if(
        date("H") == "8" ||
        date("H") == "6" ||
        date("H") == "10" ||
        date("H") == "16"
    ) {
        $memoryRepository = new \Anna\Repository\MemoriesRepository();
        $result = $memoryRepository->read([
            'user_id' => $userId,
            'chat_id' => $chatId
        ]);

        $text = '';
        if(!empty($result)) {
            $text = "Mi hai detto di ricordarti di:\n";
            foreach ($result as $memory) {
                $text .= "-\t" . $memory['text'] . "\n";
            }
        } else {
            $userRepository = new \Anna\Repository\UserRepository();
            $result = $userRepository->read([
                'id' => $userId
            ]);
            if(empty($result)) exit;
        }

        $BOT_TOKEN = '368911276:AAHQqenFvjALb-WOMqzrbD9MmPd_dJmB4lE';
        $API_URL = 'https://api.telegram.org/bot' . $BOT_TOKEN .'/';
        $method = 'sendMessage';
        $parameters = [
            'chat_id' => $chatId,
            'text' => $text
        ];
        $url = $API_URL . $method. '?' . http_build_query($parameters);
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($handle, CURLOPT_TIMEOUT, 60);
        $result = curl_exec($handle);

        sleep(60);
    }
}