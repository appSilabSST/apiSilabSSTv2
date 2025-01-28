<?php
set_time_limit(0); // Mantém o script rodando indefinidamente

$address = '0.0.0.0';
$port = 8081;

$server = stream_socket_server("tcp://$address:$port", $errno, $errstr);
if (!$server) {
    echo "Erro ao criar o servidor: $errstr ($errno)\n";
    exit(1);
}

echo "Servidor WebSocket rodando em ws://$address:$port\n";

// Função para handshake
function handshake($client)
{
    $headers = fread($client, 1024);
    if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $headers, $matches)) {
        $key = trim($matches[1]);
        $acceptKey = base64_encode(sha1($key . "258EAFA5-E914-47DA-95CA-C5AB0DC85B11", true));
        $response = "HTTP/1.1 101 Switching Protocols\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "Sec-WebSocket-Accept: $acceptKey\r\n\r\n";
        fwrite($client, $response);
        return true;
    }
    return false;
}

// Funções para codificar/decodificar mensagens WebSocket
function decodeWebSocketMessage($data)
{
    $length = ord($data[1]) & 127;
    $offset = 2;

    if ($length == 126) {
        $offset = 4;
    } elseif ($length == 127) {
        $offset = 10;
    }

    $masks = substr($data, $offset, 4);
    $payload = substr($data, $offset + 4);
    $message = '';

    for ($i = 0; $i < strlen($payload); ++$i) {
        $message .= $payload[$i] ^ $masks[$i % 4];
    }
    return json_decode($message, true);
}

function encodeWebSocketMessage($message)
{
    $length = strlen($message);
    if ($length <= 125) {
        return chr(129) . chr($length) . $message;
    } elseif ($length <= 65535) {
        return chr(129) . chr(126) . pack("n", $length) . $message;
    } else {
        return chr(129) . chr(127) . pack("J", $length) . $message;
    }
}

$clients = [];

while ($client = @stream_socket_accept($server)) {
    $clients[] = $client;

    if (!handshake($client)) {
        fclose($client);
        continue;
    }

    while ($data = @fread($client, 2048)) {
        $decodedData = decodeWebSocketMessage($data);

        if ($decodedData && isset($decodedData['tipo']) && $decodedData['tipo'] === 'linha-selecionada') {
            $message = encodeWebSocketMessage(json_encode($decodedData));

            // Transmite para todos os clientes conectados
            foreach ($clients as $key => $connectedClient) {
                if (@fwrite($connectedClient, $message) === false) {
                    fclose($connectedClient);
                    unset($clients[$key]); // Remove cliente desconectado
                }
            }
        }
    }
    fclose($client);
}

// Fecha o servidor
fclose($server);
