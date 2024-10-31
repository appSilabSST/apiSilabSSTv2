<?php

date_default_timezone_set('America/Sao_Paulo');

define('host_db', "162.241.2.151");
define('user_db', "silabs69_silab");
define('pass_db', '$f)$13m~>Hq3');
define('name_db', "silabs69_appsilab");

try {
    $conn = new PDO('mysql:host=' . host_db . ';dbname=' . name_db, user_db, pass_db);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SET lc_time_names = 'pt_br'");
    $stmt->execute();
} catch (\Throwable $th) {
    $result = array(
        "status" => "fail",
        "result" => $th->getMessage()
    );

    echo json_encode($result);
    exit;
}
