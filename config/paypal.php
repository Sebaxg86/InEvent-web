<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/env.php';

use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;

// Leer las credenciales desde el .env
$clientId = env('PAYPAL_CLIENT_ID');
$clientSecret = env('PAYPAL_SECRET_KEY');
$mode = env('PAYPAL_MODE', 'sandbox'); // 'live' para producción

// Crear el builder de credenciales
$authBuilder = ClientCredentialsAuthCredentialsBuilder::init($clientId, $clientSecret);

// Inicializar el cliente usando el builder del SDK
$client = PaypalServerSdkClientBuilder::init()
    ->clientCredentialsAuthCredentials($authBuilder)
    ->environment($mode)
    ->build();

// Ahora puedes usar $client para acceder a los controladores, por ejemplo OrdersController, PaymentsController, etc.