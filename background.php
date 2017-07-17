<?php

require_once __DIR__ . '/vendor/autoload.php';

$userId = intval($argv[1]);
$chatId = intval($argv[2]);

if($userId == null || $chatId == null) {
    error_log('missing parameters');
    exit;
}

echo 'primaciclo';
while (true){
    echo (date("H"));
    if(
        date("H") == "16" ||
        date("H") == "17"
    ) {
        echo 'ciclo con '. $userId . ' ' . $chatId;

        $memoryRepository = new \Anna\Repository\MemoriesRepository();
        $result = $memoryRepository->read([
            'user_id' => $userId,
            'chat_id' => $chatId
        ]);

        $text = "Mi hai detto di ricordarti di:\n";
        foreach ($result as $memory) {
            $text .= "-\t" . $memory['text'] . "\n";
        }

        $BOT_TOKEN = '435772795:AAESeQKeW53yZkJT7AEVHU348by8o0tBTq8';
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

        sleep(30);
    }
}