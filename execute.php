<?php
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if(!$update)
{
  exit;
}

$message = isset($update['message']) ? $update['message'] : "";
$messageId = isset($message['message_id']) ? $message['message_id'] : "";
$chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";
$firstName = isset($message['chat']['first_name']) ? $message['chat']['first_name'] : "";
$lastName = isset($message['chat']['last_name']) ? $message['chat']['last_name'] : "";
$username = isset($message['chat']['username']) ? $message['chat']['username'] : "";
$date = isset($message['date']) ? $message['date'] : "";
$text = isset($message['text']) ? $message['text'] : "";

$text = trim($text);
$text = strtolower($text);

switch ($text)
{
    case '/start' :
        $text = 'Ciao ' . $firstName;
    break;
    case '/help':
        $text = 'i comandi sono: </br>' .
            '-/start:   ti saluto </br>' .
            '-ricordami <evento>:   la mamma si ricorderà l\'evento </br>' .
            '-dimentica <evento>:   la mamma si dimentica di ogni evento contenente la parola che specifichi </br>' .
            '-/racconta:    la mamma ti racconterà gli eventi che hai detto di ricordarsi';
    break;
    case '/racconta' :
        $text = 'Non sono ancora capace scusa';
    break;
}

header("Content-Type: application/json");
$parameters = array('chat_id' => $chatId, "text" => $text);
$parameters["method"] = "sendMessage";
echo json_encode($parameters);
echo json_encode($parameters);
