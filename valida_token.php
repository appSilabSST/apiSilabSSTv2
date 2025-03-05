<?php

include_once("functions.php");

$allowedOrigins = array(
    'http://localhost:4200',
    'https://silabsst.com.br/appSilabSST'
);

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    $http_origin = $_SERVER['HTTP_ORIGIN'];
} else {
    $http_origin = "https://silabsst.com.br/appSilabSST";
    // header("HTTP/1.1 403 Forbidden");
    // echo 'Acesso não autorizado.';
    // exit;
}

// header("Access-Control-Allow-Origin: $http_origin");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// AUTHORIZARION RESTRICT
$authorization = false;
// REQUEST TOKEN
@$token = $_SERVER['HTTP_AUTHORIZATION'];
// REQUEST METHOD
$method = $_SERVER['REQUEST_METHOD'];
// REQUEST BODY
if (json_decode(file_get_contents('php://input'), true)) {
    $json = json_decode(file_get_contents('php://input'), true);
}
// VERIFICA SE O TOKEN FOI DECLARADO
if (!empty($token)) {
    try {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://silabsst.com.br/_backend/token/?token=' . $token,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        if ($response) {
            $authorization = true;
            $info = json_decode($response);
            include_once("conn.php");
        } else {
            echo json_encode(
                array(
                    'status' => 'fail',
                    'result' => 'Token inválido!'
                )
            );
            exit;
        }
    } catch (PDOException $ex) {
        echo json_encode(
            array(
                'status' => 'fail',
                'result' => $ex->getMessage()
            )
        );
        exit;
    }
} else {
    echo json_encode(
        array(
            'status' => 'fail',
            'result' => 'O envio do Token é obrigatório!'
        )
    );
    exit;
}
