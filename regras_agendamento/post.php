<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_dia_semana']) && is_numeric($json['id_dia_semana']) &&
            isset($json['data_inicio']) &&
            isset($json['horario_inicio']) && isset($json['horario_fim']) &&
            isset($json['intervalo']) && is_numeric($json['intervalo']) &&
            isset($json['qtde_intervalo']) && is_numeric($json['qtde_intervalo'])
        ) {

            // VERIFICA SE DATA INICIO ESTÁ NO FORMATO CORRETO
            if (!isValidDate($json['data_inicio'])) {
                throw new Exception('Data início não está no formato Y-m-d.');
            }
            // VERIFICA SE DATA FIM ESTÁ NO FORMATO CORRETO
            elseif (isset($json['data_fim']) && !isValidDate($json['data_fim'])) {
                throw new Exception('Data fim não está no formato Y-m-d.');
            }
            // VERIFICA SE DATA INICIO É ANTERIOR A DATA ATUAL
            elseif (strtotime($json['data_inicio']) < strtotime(date('Y-m-d'))) {
                throw new Exception('Data de início não pode ser menor que a data atual.');
            }

            $sql = "
            INSERT INTO regras_agendamento (id_dia_semana, data_inicio, data_fim, horario_inicio, horario_fim, intervalo, qtde_intervalo) VALUES
            (:id_dia_semana, :data_inicio, :data_fim, :horario_inicio, :horario_fim, :intervalo, :qtde_intervalo)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_dia_semana', trim($json['id_dia_semana']), PDO::PARAM_INT);
            $stmt->bindParam(':data_inicio', trim($json['data_inicio']), PDO::PARAM_STR);
            $stmt->bindParam(':data_fim', trim($json['data_fim']), isset($json['data_fim']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':horario_inicio', trim($json['horario_inicio']), PDO::PARAM_STR);
            $stmt->bindParam(':horario_fim', trim($json['horario_fim']), PDO::PARAM_STR);
            $stmt->bindParam(':intervalo', trim($json['intervalo']), PDO::PARAM_INT);
            $stmt->bindParam(':qtde_intervalo', trim($json['qtde_intervalo']), PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {

                http_response_code(200);

                $id_regra_agendamento = $conn->lastInsertId();

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

                $insertAgendamento = setupInsetAgendamento($json, $id_regra_agendamento, $response);

                if ($insertAgendamento) {
                    // Execute o insert ou a ação de agendamento se for necessário
                    $stmt = $conn->prepare($insertAgendamento);
                    $stmt->execute();
                }

                $result = 'Regra de agendamento cadastrada com sucesso!';
            } else {
                http_response_code(500);
                $result = 'Falha ao cadastrar regra de agendamento!';
            }
        } else {
            http_response_code(400);
            $result = 'Dados incompletos!';
        }
    } catch (\Throwable $th) {
        http_response_code(500);
        // DADOS ÚNICOS JÁ UTILIZADOS
        if ($th->getCode() == 23000) {
            $result = 'Regra de agendamento já existente!';
        } else {
            $result = $th->getMessage();
        }
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
