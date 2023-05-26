<?php

## PRIMERO CREO EL CLIENTE 
#########################################################################################################################
// librería OPENAI-PHP  https://github.com/openai-php/client
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$open_ai_key = $_ENV['OPENAI_API_KEY']; // esto sale del archivo .env ,poner tu API KEY en la clave 'OPENAI_API_KEY'
$client = OpenAI::client($open_ai_key);
#########################################################################################################################


//###  TODOS LOS TEST ESTAN ORDENADOS DE MAS NUEVOS PRIMERO.






#################################  MODIFICA IMAGENES #############################
$response = $client->images()->edit([
    'image' => fopen('image_edit_original.png', 'r'),
    'mask' => fopen('image_edit_mask.png', 'r'),
    'prompt' => 'Un salón interior iluminado por el sol con una piscina con un flamenco',
    'n' => 1,
    'size' => '256x256',
    'response_format' => 'url',
]);

$response->created; // 1589478378

foreach ($response->data as $data) {
    $data->url; // 'https://oaidalleapiprodscus.blob.core.windows.net/private/...'
    $data->b64_json; // null
}

$response->toArray(); // ['created' => 1589478378, data => ['url' => 'https://oaidalleapiprodscus...', ...]]
########################################################################################







#################################  CREA UNA IMAGEN EN BASE A UN PROMPT #############################
$response = $client->images()->create([
    'prompt' => 'Una linda nutria de mar bebé',
    'n' => 1,
    'size' => '256x256',
    'response_format' => 'url',
]);

$response->created; // 1589478378

foreach ($response->data as $data) {
    $data->url; // 'https://oaidalleapiprodscus.blob.core.windows.net/private/...'
    $data->b64_json; // null
}

$response->toArray(); // ['created' => 1589478378, data => ['url' => 'https://oaidalleapiprodscus...', ...]]
########################################################################################






#################################  CORRECTOR DE ORTOGRAFIA #############################
$response = $client->edits()->create([
    'model' => 'text-davinci-edit-001',
    'input' => 'What day of the wek is it?',
    'instruction' => 'Fix the spelling mistakes',
]);

$response->object; // 'edit'
$response->created; // 1589478378

foreach ($response->choices as $result) {
    $result->text; // 'What day of the week is it?'
    $result->index; // 0
}

$response->usage->promptTokens; // 25,
$response->usage->completionTokens; // 32,
$response->usage->totalTokens; // 57

$response->toArray(); // ['object' => 'edit', ...]
########################################################################################





#################################  TRADUCIR MP3 DE UN IDIOMA A OTRO  #############################
$response = $client->audio()->translate([
    'model' => 'whisper-1',
    'file' => fopen('german.mp3', 'r'), // ACA HAY QUE REEMPLAZAR german.mp3 por el nombre de tu archivo a traducir.
    'response_format' => 'verbose_json',
]);

$response->task; // 'translate'
$response->language; // 'english'
$response->duration; // 2.95
$response->text; // 'Hello, how are you?'

foreach ($response->segments as $segment) {
    $segment->index; // 0
    $segment->seek; // 0
    $segment->start; // 0.0
    $segment->end; // 4.0
    $segment->text; // 'Hello, how are you?'
    $segment->tokens; // [50364, 2425, 11, 577, 366, 291, 30, 50564]
    $segment->temperature; // 0.0
    $segment->avgLogprob; // -0.45045216878255206
    $segment->compressionRatio; // 0.7037037037037037
    $segment->noSpeechProb; // 0.1076972484588623
    $segment->transient; // false
}

$response->toArray(); // ['task' => 'translate', ...]
#######################################################################################################################





// MODEL RETRIEVE
########################################################################################
// Recupera una Instancia de Modelo y proporciona información básica sobre el Modelo, 
// como el Propietario y los Permisos.
$response = $client->models()->retrieve('text-davinci-003');

$response->id; // 'text-davinci-003'
$response->object; // 'model'
$response->created; // 1642018370
$response->ownedBy; // 'openai'
$response->root; // 'text-davinci-003'
$response->parent; // null

foreach ($response->permission as $result) {
    $result->id; // 'modelperm-7E53j9OtnMZggjqlwMxW4QG7' 
    $result->object; // 'model_permission' 
    $result->created; // 1664307523 
    $result->allowCreateEngine; // false 
    $result->allowSampling; // true 
    $result->allowLogprobs; // true 
    $result->allowSearchIndices; // false 
    $result->allowView; // true 
    $result->allowFineTuning; // false 
    $result->organization; // '*' 
    $result->group; // null 
    $result->isBlocking; // false 
}

$response->toArray(); // ['id' => 'text-davinci-003', ...]
########################################################################################




// MODEL LIST
################## RECUPERO LA LISTA DE MODELOS  ####################
$response = $client->models()->list();
$response->object; // 'list'
foreach ($response->data as $result) {
    $result->id; // 'text-davinci-003'
    $result->object; // 'model'
    // ...
}
$response->toArray(); 
################## RECUPERO LA LISTA DE MODELOS  ####################










?>