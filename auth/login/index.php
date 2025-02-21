<?php

include_once('../../conexao.php');
include_once('../jwt.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// var_dump($postjson);exit;

$username = trim($postjson['username']);
$password = hash('sha256', trim($postjson['password']));
$remember = trim($postjson['rememberMe']) ?? 0;

// VERIFICA CAMPOS OBRIGATÓRIOS
if (empty($username) || empty($password)) {
    $result = json_encode(array(
        'success' => true
    ));
} else {
    $sql = "
    SELECT
        id_usuario_sistema , nome ,
        tipo_usuario_sistema
    FROM 
        usuarios_sistema
    JOIN
        tipo_usuarios_sistema ON usuarios_sistema.id_tipo_usuario_sistema = tipo_usuarios_sistema.id_tipo_usuario_sistema
    WHERE 
        username LIKE '$username'
        AND senha LIKE '$password'
    ";

    // echo $sql;exit;

    $query = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_object($query);

        $name_fullname = explode(" ", $row->nome);
        $name_fullname = $name_fullname[0] . " " . end($name_fullname);

        $sala = array(
            'id_sala' => $row->id_sala,
            'classe' => $row->classe
        );

        $sala = array(
            'id_sala' => 1,
            'classe' => 1,
            'nome' => "Clínico"
        );

        $token = encodeJWT($remember, $row->id_usuario_sistema, $name_fullname, $row->tipo_usuario_sistema, 'admin', $sala);
        // echo $token;exit;

        $result = json_encode(array(
            'success' => true,
            'token' => $token
        ));
    } else {
        $result = json_encode(array(
            'success' => true,
        ));
    }
}

http_response_code(200);

echo $result;
exit;
