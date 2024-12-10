<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_dia_semana']) && is_numeric($json['id_dia_semana']) &&
            isset($json['data_inicio']) &&
            isset($json['horario_inicio']) && isset($json['horario_fim']) &&
            isset($json['intervalo']) && is_numeric($json['intervalo']) &&
            isset($json['qtde']) && is_numeric($json['qtde'])
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
            INSERT INTO regras_agendamento (id_dia_semana, data_inicio, data_fim, horario_inicio, horario_fim, intervalo, qtde) VALUES
            (:id_dia_semana, :data_inicio, :data_fim, :horario_inicio, :horario_fim, :intervalo, :qtde)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_dia_semana', trim($json['id_dia_semana']), PDO::PARAM_INT);
            $stmt->bindParam(':data_inicio', trim($json['data_inicio']), PDO::PARAM_STR);
            $stmt->bindParam(':data_fim', trim($json['data_fim']), isset($json['data_fim']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':horario_inicio', trim($json['horario_inicio']), PDO::PARAM_STR);
            $stmt->bindParam(':horario_fim', trim($json['horario_fim']), PDO::PARAM_STR);
            $stmt->bindParam(':intervalo', trim($json['intervalo']), PDO::PARAM_INT);
            $stmt->bindParam(':qtde', trim($json['qtde']), PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);

                $id = $conn->lastInsertId();

                $insert = "";
                $nova_data = trim($json['data_inicio']);
                $data_fim = isset($json['data_fim']) ? trim($json['data_fim']) : date('Y-m-d', strtotime("+ 6 month", strtotime($nova_data)));

                // VERIFICA SE A DATA INÍCIO É COMPATÍVEL COM O DIA DE SEMANA SELECIONADO
                while ((date('w', strtotime($nova_data)) + 1) <> $json['id_dia_semana']) {
                    $nova_data = date('Y-m-d', strtotime("+ 1 day", strtotime($nova_data)));
                }

                // echo $nova_data;exit;
                while (strtotime($nova_data) <= strtotime($data_fim)) {

                    $novo_horario = date('H:i', strtotime(trim($json['horario_inicio'])));

                    while (strtotime($novo_horario) <= strtotime(trim($json['horario_fim']))) {
                        // CRIA HORÁRIOS DE AGENDA VIRTUALMENTE
                        for ($i = 0; $i < $json['qtde']; $i++) {
                            $insert .= "('$nova_data','$novo_horario','$id'),";
                        }

                        $novo_horario = date('H:i', strtotime("+ $intervalo minutes", strtotime($novo_horario)));
                    }

                    $nova_data = date('Y-m-d', strtotime("+ 1 week", strtotime($nova_data)));
                }

                $sql = "
                INSERT INTO agendamentos (data, horario, id_regra_agendamento) VALUES
                " . substr($insert, 0, -1) . "
                ";

                // echo $sql;exit;
                $stmt = $conn->prepare($sql);
                $stmt->execute();

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
