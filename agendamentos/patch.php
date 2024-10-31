<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id']) && is_numeric($json['id'])) {
            $sql = "
            UPDATE agendamentos SET
            ";
            foreach ($json as $key => $value) {
                // REMOVE O CAMPO ID E LISTA DE EXAMES E RISCOS
                if ($key != 'id' && !is_array($value)) {
                    $sql .= "$key = :$key,";
                }
            }
            $sql = substr($sql, 0, -1) . " WHERE id_agendamento = :id_agendamento";

            $stmt = $conn->prepare($sql);
            foreach ($json as $key => $value) {
                // REMOVE O CAMPO ID E LISTA DE EXAMES E RISCOS
                if ($key != 'id' && !is_array($value)) {
                    $stmt->bindValue(":$key", trim($value));
                } elseif ($key == 'id') {
                    $id_agendamento = $value;
                    $stmt->bindValue(":id_agendamento", $value);
                } else {
                    // IGNORA OS ARRAYS DE EXAMES E RISCOS
                }
            }
            $stmt->execute();

            // Create both cURL resources
            $ch1 = curl_init();
            $ch2 = curl_init();

            // CHAMA A API PARA CADASTRAR OS EXAMES AO AGENDAMENTO CRIADO
            $postfields = array(
                'id_agendamento' => $id_agendamento,
                'exames' => $json['exames']
            );
            curl_setopt_array($ch1, array(
                CURLOPT_URL => "https://silabsst.com.br/_backend/agendamento_exames/",
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

            // CHAMA A API PARA CADASTRAR OS RISCOS AO AGENDAMENTO CRIADO
            $postfields = array(
                'id_agendamento' => $id_agendamento,
                'riscos' => $json['riscos']
            );
            curl_setopt_array($ch2, array(
                CURLOPT_URL => "https://silabsst.com.br/_backend/agendamento_riscos/",
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

            // Create the multiple cURL handle
            $mh = curl_multi_init();

            // Add the two handles
            curl_multi_add_handle($mh, $ch1);
            curl_multi_add_handle($mh, $ch2);

            // Execute the multi handle
            do {
                $status = curl_multi_exec($mh, $active);
                if ($active) {
                    curl_multi_select($mh);
                }
            } while ($active && $status == CURLM_OK);

            // Optionally capture the response
            $response1 = curl_multi_getcontent($ch1);
            $response2 = curl_multi_getcontent($ch2);

            // Close the handles
            curl_multi_remove_handle($mh, $ch1);
            curl_close($ch1);
            curl_multi_remove_handle($mh, $ch2);
            curl_close($ch2);
            curl_multi_close($mh);

            // Print the responses (if captured)
            // echo "Response1: " . $response1 . PHP_EOL;
            // echo "Response2: " . $response2 . PHP_EOL;
            // echo "Response3: " . $response3 . PHP_EOL;
            // echo "Response4: " . $response4 . PHP_EOL;

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Agendamento atualizado com sucesso!'
            );
        } else {
            http_response_code(400);
            $result = array(
                'status' => 'fail',
                'result' => 'Dados incompletos!'
            );
        }
    } catch (\Throwable $th) {
        http_response_code(500);
        // DADOS ÚNICOS JÁ UTILIZADOS
        if ($th->getCode() == 23000) {
            $result = array(
                'status' => 'fail',
                'result' => 'Colaborador já existente nesta agenda!'
            );
        } else {
            $result = array(
                'status' => 'fail',
                'result' => $th->getMessage()
            );
        }
    } finally {
        $conn = null;
        echo json_encode($result);
    }
} else {
    http_response_code(403);
    echo json_encode(
        array(
            'status' => 'fail',
            'result' => 'Sem autorização para acessar este conteúdo!'
        )
    );
}
exit;
