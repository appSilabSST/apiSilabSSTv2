<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['nome']) &&
            isset($json['descricao']) &&
            isset($json['exames']) && count($json['exames']) > 0 && !in_array(null, $json['exames'])
        ) {

            $sql = "INSERT INTO anamneses (nome,descricao) VALUES (:nome,:descricao)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nome', trim($json['nome']));
            $stmt->bindParam(':descricao', trim($json['descricao']));
            $stmt->execute();


            if ($stmt->rowCount() > 0) {
                $id_anamnese = $conn->lastInsertId();


                // // Create both cURL resources
                $ch1 = curl_init();

                // // CHAMA A API PARA CADASTRAR OS EXAMES AO AGENDAMENTO CRIADO
                $postfields = array(
                    'id_anamnese' => $id_anamnese,
                    'exames' => $json['exames']
                );
                curl_setopt_array($ch1, array(
                    CURLOPT_URL => "https://silabsst.com.br/_backend/anamneses_exames/",
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

                $response = curl_exec($ch1);

                curl_close($ch1);

                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Anamnese criado com sucesso!',
                    'resultExames' => $response
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao criar o colaborador!'
                );
            }
        } else if (isset($json['nome']) && isset($json['descricao'])) {

            $sql = "INSERT INTO anamneses (nome,descricao) VALUES (:nome,:descricao)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nome', trim($json['nome']));
            $stmt->bindParam(':descricao', trim($json['descricao']));
            $stmt->execute();


            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Sala de atendimento criado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao criar o colaborador!'
                );
            }
        } else {
            http_response_code(400);
            $result = array(
                'status' => 'fail',
                'result' => 'Dados incompletos!'
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
