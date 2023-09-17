<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

error_log("Request received at " . date('Y-m-d H:i:s'));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera il testo inserito dall'utente
    $user_input = $_POST["info"];

    // Add instruction to verify the matter
    $prompt = "Verify this information like if you where a really good journalist. 
        First tell if the information is true or false, like 'true' or 'false'. 
        Then explain like this: 

        Information: The number of States in the United States of America are 51
        Fact-check: No, there are 50 states in the USA. 
        
        Information: The first moon landing has been the 12/08/1996
        Fact-check: The first moon landing did not occur on December 8, 1996. The first moon landing by     NASA's Apollo 11 mission took place on July 20, 1969. Astronauts Neil Armstrong and Buzz Aldrin became the first humans to walk on the moon during that historic mission. The date you mentioned, December 8, 1996, does not correspond to any significant moon landing event.

        Information: $user_input
        Fact-check:";

    // Imposta l'API Key di OpenAI
    $api_key = getenv('OPENAI_API_KEY');

    // URL dell'API di OpenAI per generare risposte da ChatGPT
    $api_url = 'https://api.openai.com/v1/chat/completions';

    // Imposta i dati per la richiesta API
    $data = array(
        'model' => 'gpt-3.5-turbo',
        'max_tokens' => 100, // Limite di lunghezza della risposta
        'messages' => [
            array('role' => 'system', 'content' => 'Enter the information to verify:'),
            array('role' => 'user', 'content' => $user_input),
        ],
        'temperature' => 0.6
    );

    // Configura l'intestazione della richiesta con l'API Key
    $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
        
    );

    // Effettua la richiesta POST all'API di OpenAI
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Esegui la richiesta e ottieni la risposta
    $response = curl_exec($ch);

    // Gestisci la risposta JSON
    if ($response === false) {
        echo 'Errore nella richiesta API: ' . curl_error($ch);
    } else {
        $decoded_response = json_decode($response, true);

        $chat_gpt_response = $decoded_response['choices'][0]['message']['content'];

        $utf8Response = htmlspecialchars($chat_gpt_response, ENT_QUOTES, 'UTF-8');

        // Return the answer
        echo json_encode($utf8Response);
    }

    // Chiudi la connessione cURL
    curl_close($ch);
}
?>
