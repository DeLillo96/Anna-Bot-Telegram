<?php
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if(!$update)
{
  exit;
}
require_once __DIR__ . '/vendor/autoload.php';

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
        $userRepository = new \Anna\Repository\UserRepository();
        $result = $userRepository->read([
            'username' => $username,
            'chat_id' => $chatId
        ]);

        if(!empty($result)) {
            $text = 'Ciao ' . $firstName;
        } else {
            $data = [
                'username' => $username,
                'chat_id' => $chatId
            ];
            if($firstName != "") array_merge($data, ['first_name' => $firstName]);
            if($lastName != "") array_merge($data, ['last_name' => $lastName]);

            $userRepository->create($data);
            $text = 'Benvenuto ' . $username . '! Mi raccomando, se cambi username non ti riconoscerò più!';
        }
    break;
    case '/help':
        $text = 'i comandi sono: <br />' .
            '-/start:   per inizializzare il servizio <br />' .
            '-ricordami <evento>:   Mi ricorderò dell\'evento <br />' .
            '-dimentica <evento>:   Mi dimenticherò ogni evento contenente la parola che specifichi <br />' .
            '-/racconta:    Ti racconterò gli eventi che hai detto di ricordarmi';
    break;
    case '/racconta' :
        $memoryRepository = new \Anna\Repository\MemoriesRepository();
        $result = $memoryRepository->read([
            'username' => $username,
            'chat_id' => $chatId
        ]);

        $text = 'Mi hai detto di ricordarti di: ';
        foreach ($result as $memory) {
            $text .= $memory['text'] . ' ';
        }
    break;
    case '/test':
        $text = $message;
    break;
    default:
        $text = 'Scusa non ho capito...';
}

if ('ricordati' == substr($text, 0, 9)) {
    $memory = substr($text, 9, strlen($text));

    $memoryRepository = new \Anna\Repository\MemoriesRepository();
    $result = $memoryRepository->create([
        'username' => $username,
        'chat_id' => $chatId,
        'text' => $memory
    ]);

    if(!empty($result))
        $text = 'Ricorderò per te';
    else
        $text = 'C\'è stato un errore';
}

header("Content-Type: application/json");
$parameters = array('chat_id' => $chatId, "text" => $text);
$parameters["method"] = "sendMessage";
echo json_encode($parameters);
