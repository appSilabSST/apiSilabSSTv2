<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id']) && is_numeric($json['id']) &&
            isset($json['id_dia_semana']) && is_numeric($json['id_dia_semana']) &&
            isset($json['data_inicio']) &&
            isset($json['horario_inicio']) && isset($json['horario_fim']) &&
            isset($json['intervalo']) && is_numeric($json['intervalo']) &&
            isset($json['qtde_intervalo']) && is_numeric($json['qtde_intervalo'])
        ) {
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
            $stmt->bindParam(':id_regra_agendamento', trim($json['id']), PDO::PARAM_INT);;
            $stmt->execute();

            http_response_code(200);
            $result = 'Regra de agendamento atualizada com sucesso!';
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
