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
            if(!empty($firstName)) array_merge($data, ['first_name' => $firstName]);
            if(!empty($lastName)) array_merge($data, ['last_name' => $lastName]);

            $userRepository->create($data);
            $text = 'Benvenuto ' . $username . "!\n Mi raccomando, se cambi username non ti riconoscerò più!";

            $result = $userRepository->read([
                'username' => $username,
                'chat_id' => $chatId
            ]);
            $command = "php -f background.php " . array_shift($result)['id'] . " " . $chatId;
            exec($command . "> /dev/null &");
        }
    break;
    case '/help':
        $text = "i comandi sono: \n" .
            "-/start:\tPer inizializzare il servizio \n" .
            "-/addio:\tPer disiscriversi al servizio \n" .
            "-ricordami <evento>:\tMi ricorderò dell'evento \n" .
            "-dimentica <evento>:\tMi dimenticherò ogni evento contenente la parola che specifichi \n" .
            "-/racconta:\tTi racconterò gli eventi che hai detto di ricordarmi" .
            "-/contatti:\tSe vuoi contattare mio padre";
    break;
    case '/racconta' :
        $memoryRepository = new \Anna\Repository\MemoriesRepository();
        $result = $memoryRepository->read([
            'username' => $username,
            'chat_id' => $chatId
        ]);

        $text = "Mi hai detto di ricordarti di:\n";
        foreach ($result as $memory) {
            $text .= "-\t" . $memory['text'] . "\n";
        }
    break;
    case '/addio' :
        $userRepository = new \Anna\Repository\UserRepository();
        $result = $userRepository->delete([
            'username' => $username,
            'chat_id' => '' . $chatId
        ]);

        if ($result)
            $text = "Disiscrizione effettuata...\nTorna a utilizzare il servizio ogni volta che vorrai";
        else
            $text = "Disiscrizione fallita...\nPer contattare mio padre digita il comando /contatti";
    break;
    case '/contatti':
        $text = "Qui potrai trovare i contatti di mio padre:\n" .
            "\ttelegram: @delillo96\n" .
            "\te-mail: di.lillo.fernando@gmail.com\n" .
            "\tNome: Fernando\n" .
            "\tCognome: Di Lillo\n" .
            "\tIndirizzo: Naah questo non posso dartelo ahah";
    break;
    default:
        if ('ricordami' == substr($text, 0, 9)) {
            $memory = substr($text, 10, strlen($text));

            $memoryRepository = new \Anna\Repository\MemoriesRepository();
            $memoryRepository->create([
                'username' => $username,
                'chat_id' => $chatId,
                'text' => $memory
            ]);

            $text = 'Ricorderò per te!';
        } elseif ('dimentica' == substr($text, 0, 9)) {
            $memory = substr($text, 10, strlen($text));

            if(!(empty($memory) || $memory === ' ')){
                $memoryRepository = new \Anna\Repository\MemoriesRepository();
                $result = $memoryRepository->delete([
                    'username' => $username,
                    'chat_id' => $chatId,
                    'text' => $memory
                ]);
                if ($result)
                    $text = 'Dimenticato!';
                else
                    $text = 'Non riesco, c\'è un errore';
            } else {
                $text = 'Scusa, devi darmi almeno un carattere da confrontare';
            }

        } else
            $text = "Scusa non ho capito...\n/help per visualizzare cosa posso fare :)" ;
}

header("Content-Type: application/json");
$parameters = array('chat_id' => $chatId, "text" => $text);
$parameters["method"] = "sendMessage";
echo json_encode($parameters);
