![Gloouds](https://raw.githubusercontent.com/aledc7/OpenAI-API/main/resources/gld500x500.png)

# OpenAI-API

### Este Documento describe como consumir la API de OpenAI


__Requisitos para usar la API:__   

- [x] Php 8.0 o superior   
- [x] [API Key](https://platform.openai.com/account/api-keys) de tu cuenta de OpenAI
- [x] Haber registrado un [Medio de Pago](https://platform.openai.com/account/billing/payment-methods) en OpenAI Platform.   
- [x] El uso de esta API conlleva un costo.

Aquí encontraras la [Lista de precios](https://openai.com/pricing) actual.   

A modo de resumen, estos son los precios en dolares.   
```
Model	Prompt	Completion
8K context	$0.03 / 1K tokens	$0.06 / 1K tokens
32K context	$0.06 / 1K tokens	$0.12 / 1K tokens
```

## Tokens    
```
Múltiples modelos, cada uno con diferentes capacidades y puntos de precio. Los precios son por 1.000 tokens. 
Puedes pensar en los tokens como piezas de palabras, donde 1000 tokens equivalen a unas 750 palabras. 
Este párrafo es de 35 tokens.
```




####################################################################################################################################################################


# Instalación   

OpenAI PHP es un cliente de API de PHP mantenido por la comunidad que permite interactuar con la API de OpenAI.   
A la fecha (25/05/2023) es lo mejorcito que hay.   

1. - Instalar la librería mediante Composer:
```php
composer require openai-php/client
```

2. Instalar esta librería para poder usar los archivos .env para guardar la API-KEY de OpenAI
```php
composer require vlucas/phpdotenv
```

3. Crear un archivo oculto .env con este contenido y guardar en él tu API Key  
```php
// NOTA IMPORTANTE: Por razones de seguridad, SIEMPRE este archivo .env debe estar en el .gitignore
OPENAI_API_KEY="tu-api-key-de-open-ai"
```

Una vez tenemos instaladas las dos librerías necesarias, y nuestro archivo .env configurado, podemos usar la API:

## Instanciamos el cliente
```php
#########################################################################################################################
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$open_ai_key = $_ENV['OPENAI_API_KEY']; // esto sale del archivo .env ,poner tu API KEY en la clave 'OPENAI_API_KEY' de tu .env
$client = OpenAI::client($open_ai_key);  
#########################################################################################################################
```


En esta instancia tendremos nuestro cliente en la variable php $clien     


De aqui en mas, ya se podrá interactuar de manera FULL con los [Modelos](https://platform.openai.com/docs/models) de Lenguaje que OpenAI pone a disposición.     
_____________________________________________________________________________________________________________________

# MODEL LIST
```php
################## RECUPERO LA LISTA DE MODELOS  ####################
$response = $client->models()->list();
$response->object; // 'list'
foreach ($response->data as $result) {
    $result->id; // 'text-davinci-003'
    $result->object; // 'model'
    // ...
}
$response->toArray(); 
```   
_____________________________________________________________________________________________________________________

_____________________________________________________________________________________________________________________
# MODEL RETRIEVE
```php
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
````
_____________________________________________________________________________________________________________________

_____________________________________________________________________________________________________________________
# TRADUCIR MP3 DE UN IDIOMA A OTRO  
```php
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
````
_____________________________________________________________________________________________________________________
_____________________________________________________________________________________________________________________
# CORRECTOR DE ORTOGRAFIA 
```php
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

````
_____________________________________________________________________________________________________________________
_____________________________________________________________________________________________________________________
# CREA UNA IMAGEN EN BASE A UN PROMPT
```php
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

````
_____________________________________________________________________________________________________________________
_____________________________________________________________________________________________________________________
# MODIFICA IMAGENES
```php
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
````
_____________________________________________________________________________________________________________________
_____________________________________________________________________________________________________________________
# Pasos a seguir...  

Si usas Composer, simplemente clona este proyecto e instala las dependencias:
```
composer install
```

Si no usas Composer:   
Dentro de la carpeta resources, hay un archivo project.zip con el proyecto funcionando,con todas estas pruebas, listo para usar, solo crear el .env   
por el de ustedes.
_____________________________________________________________________________________________________________________   
Copy@ Ale DC. May2024












