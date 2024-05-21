<?php

function encodeJWT($user_remember, $user_id, $user_name, $user_function, $user_role)
{

    // secret key
    $secret_key = 'JwT!SiLaB@2023';

    // Create token header as a JSON string
    $header = json_encode(
        array(
            'typ' => 'JWT',
            'alg' => 'HS256'
        )
    );

    // Expire time
    $exp = time() + 3000; 

    // Create token payload as a JSON string
    $payload = json_encode(
        array(
            'user_remember' => $user_remember,
            'user_id' => $user_id,
            'user_name' => $user_name,
            'user_permission' => $user_function,
            'user_role' => $user_role,
            'iat' => time(),
            'exp' => $exp
        )
    );

    // Encode Header to Base64Url String
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

    // Encode Payload to Base64Url String
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

    // Create Signature Hash
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret_key, true);

    // Encode Signature to Base64Url String
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    // Create JWT
    $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

    return $jwt;
}

// echo encodeJWT('false', '2', 'Marcos Vinícius','Assistente de desenvolvimento','admin');