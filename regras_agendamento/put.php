<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        // Verifica se todos os parâmetros necessários estão presentes e válidos
        if (
            isset($json['id_regra_agendamento']) && is_numeric($json['id_regra_agendamento']) &&
            isset($json['id_dia_semana']) && is_numeric($json['id_dia_semana']) &&
            isset($json['data_inicio']) &&
            isset($json['horario_inicio']) && isset($json['horario_fim']) &&
            isset($json['intervalo']) && is_numeric($json['intervalo']) &&
            isset($json['qtde_intervalo']) && is_numeric($json['qtde_intervalo'])
        ) {
            // SQL de atualização
            $sql = "
            UPDATE regras_agendamento SET
            id_dia_semana = :id_dia_semana, 
            data_inicio = :data_inicio, 
            data_fim = :data_fim, 
            horario_inicio = :horario_inicio, 
            horario_fim = :horario_fim, 
            intervalo = :intervalo, 
            qtde_intervalo = :qtde_intervalo
            WHERE id_regra_agendamento = :id_regra_agendamento
            ";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_dia_semana', trim($json['id_dia_semana']), PDO::PARAM_INT);
            $stmt->bindParam(':data_inicio', trim($json['data_inicio']), PDO::PARAM_STR);
            $stmt->bindParam(':data_fim', trim($json['data_fim']), PDO::PARAM_STR);
            $stmt->bindParam(':horario_inicio', trim($json['horario_inicio']), PDO::PARAM_STR);
            $stmt->bindParam(':horario_fim', trim($json['horario_fim']), PDO::PARAM_STR);
            $stmt->bindParam(':intervalo', trim($json['intervalo']), PDO::PARAM_INT);
            $stmt->bindParam(':qtde_intervalo', trim($json['qtde_intervalo']), PDO::PARAM_INT);
            $stmt->bindParam(':id_regra_agendamento', trim($json['id_regra_agendamento']), PDO::PARAM_INT);

            // Executa a query
            $stmt->execute();

            // Verifica se a atualização foi bem-sucedida
            if ($stmt->rowCount() > 0) {
                http_response_code(200); // Código de sucesso
                $id_regra_agendamento = $json['id_regra_agendamento'];

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://silabsst.com.br/_backend/agendamentos/?id_regra_agendamento=' . $id_regra_agendamento,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'DELETE',
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: $token",
                        "Content-Type: application/json"
                    ),
                ));

                $response = curl_exec($curl);

                if (curl_errno($curl)) {
                    throw new Exception('Erro na requisição cURL: ' . curl_error($curl));
                }

                curl_close($curl);

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://silabsst.com.br/_backend/feriados/',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: $token",
                        "Content-Type: application/json"
                    ),
                ));

                $response = curl_exec($curl);

                if (curl_errno($curl)) {
                    throw new Exception('Erro na requisição cURL: ' . curl_error($curl));
                }

                curl_close($curl);

                $response = json_decode($response, true);

                // Chama a função para configurar o agendamento (insira ou atualize, conforme necessário)
                $insertAgendamento = setupInsetAgendamento($json, $id_regra_agendamento, $response);

                if ($insertAgendamento) {
                    // Execute o insert ou a ação de agendamento se for necessário
                    $stmt = $conn->prepare($insertAgendamento);
                    $stmt->execute();
                }

                // Mensagem de sucesso
                $result = 'Regra de agendamento atualizada com sucesso!';
            } else {
                // Se nenhuma linha foi atualizada
                http_response_code(400);
                $result = 'Nenhuma regra de agendamento foi atualizada!';
            }
        } else {
            // Se os dados enviados estiverem incompletos
            http_response_code(400);
            $result = 'Dados incompletos!';
        }
    } catch (\Throwable $th) {
        // Tratar exceções
        http_response_code(500);
        if ($th->getCode() == 23000) {
            $result = 'Regra de agendamento já existente!';
        } else {
            $result = $th->getMessage();
        }
    } finally {
        // Fecha a conexão e envia o resultado
        $conn = null;
        echo json_encode($result);
    }
}
