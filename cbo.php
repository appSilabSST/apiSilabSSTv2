<?php

include 'conn.php';
// Abre o arquivo txt
$file = fopen('cnae.txt', 'r');

// Cria uma string vazia para armazenar o insert mysql
$insert = '';

// Lê o arquivo linha por linha
while (($line = fgets($file)) !== false) {
    // Remove o caractere de quebra de linha no final da linha
    $line = trim($line);

    // Separa o código e a descrição
    list($codigo, $atividade, $dataInicio, $dataFim, $aliquita) = explode('|', $line);

    print_r($codigo);

    // Preenche o campo $codigo com zeros à esquerda até que ele tenha 6 caracteres
    $codigo = str_pad($codigo, 6, '0', STR_PAD_LEFT);
    // Escapa os caracteres especiais
    $descricao = utf8_encode($descricao);

    // Adiciona o insert mysql à string
    $insert .= "INSERT INTO cnae (codigo, descricao) VALUES ('$codigo', '$atividade');\n";
}

// Fecha o arquivo
fclose($file);

$stmt = $conn->prepare($insert);
$stmt->execute();
