<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Expose-Headers: Content-Length, X-JSON");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
header('Access-Control-Max-Age: 86400');   

date_default_timezone_set('America/Sao_Paulo');

define('host' , "162.241.62.112");
define('username' , "silabs69_silab");
define('password' , '$f)$13m~>Hq3');
define('db_name' , "silabs69_appsilab");

$conecta = mysqli_connect(host,username,password,db_name);

if($conecta) {
    mysqli_query($conecta,"SET lc_time_names = 'pt_br'");
} else {
    echo "ERRO: Falha ao conectar na base de dados!";
}

$where = "";

?>