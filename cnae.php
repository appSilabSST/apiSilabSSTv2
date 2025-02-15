<?php

include 'conn.php';

$token = "B9LIa8UfPD1tnS3SIIvA50dKzsL0H7xx8WFgOOYIm6uHSei74lxy35uXai9q9wGc3QBnPz5eATqrAfvbreSuWFy66b25ee0a2826";

// Inicializa o multi handler cURL
$mh = curl_multi_init();

// Cria uma lista de handles
$handles = [];

// Abre o arquivo txt
$file = fopen('cnae.txt', 'r');

// Lê o arquivo linha por linha
while (($line = fgets($file)) !== false) {
    // Remove o caractere de quebra de linha no final da linha
    $line = trim($line);

    // Separa o código e a descrição
    list($codigo, $atividade, $dataInicio, $dataFim, $aliquita) = explode('|', $line);

    // Preenche o campo $codigo com zeros à esquerda até que ele tenha 6 caracteres
    $codigo = str_pad($codigo, 6, '0', STR_PAD_LEFT);

    //no arquivo txt ele tráz os cnae finalizado e atual, para não ter duplicidade if verifica se data fim está vazia
    if (!empty($dataFim)) {

        // Inicializa o cURL
        $ch = curl_init();

        // Monta os dados a serem enviados via POST
        $postfields = array(
            'codigo' => $codigo,
            'atividade' => $atividade
        );

        // Define as opções do cURL
        curl_setopt_array($ch, array(
            CURLOPT_URL => "https://silabsst.com.br/_backend/cnae/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postfields),
            CURLOPT_HTTPHEADER => array(
                "Authorization: $token",
                "Content-Type: application/json"
            ),
        ));

        // Adiciona o handle cURL ao multi handler
        curl_multi_add_handle($mh, $ch);

        // Armazena o handle para posteriormente processar a resposta
        $handles[] = $ch;
    }
}

// Executa todas as requisições cURL em paralelo
$running = null;
do {
    curl_multi_exec($mh, $running);
    usleep(100); // Reduz a carga no servidor
} while ($running);

// Processa as respostas
foreach ($handles as $ch) {
    $response = curl_multi_getcontent($ch); // Pega a resposta de cada cURL

    // Aqui você pode processar a resposta como necessário
    if ($response === false) {
        echo "Erro ao chamar a API.\n";
    } else {
        // Sucesso, você pode salvar a resposta ou fazer mais algo com ela
        echo "Resposta: $response\n";
    }

    // Fecha o handle cURL
    curl_multi_remove_handle($mh, $ch);
    curl_close($ch);
}

// Fecha o multi handle
curl_multi_close($mh);

// Fecha o arquivo
fclose($file);
