<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_regra_agendamento = trim($_GET["id"]);
            $sql = "
            SELECT ra.*, DATE_FORMAT(ra.data_inicio, '%d/%m/%Y') data_inicio_format, DATE_FORMAT(ra.data_fim, '%d/%m/%Y') data_fim_format, DATE_FORMAT(ra.horario_inicio, '%H:%i') horario_inicio_format, DATE_FORMAT(ra.horario_fim, '%H:%i') horario_fim_format,
            ds.dia_semana
            FROM regras_agendamento ra
            JOIN dias_semana ds ON (ra.id_dia_semana = ds.id_dia_semana)
            WHERE ra.ativo = '1'
            AND ra.id_regra_agendamento = :id_regra_agendamento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_regra_agendamento', $id_regra_agendamento);
        } else if (isset($_GET["id_dia_semana"]) && is_numeric($_GET["id_dia_semana"])) {
            $id_dia_semana = trim($_GET["id_dia_semana"]);
            $sql = "
            SELECT ra.*, DATE_FORMAT(ra.data_inicio, '%d/%m/%Y') data_inicio_format, DATE_FORMAT(ra.data_fim, '%d/%m/%Y') data_fim_format, DATE_FORMAT(ra.horario_inicio, '%H:%i') horario_inicio_format, DATE_FORMAT(ra.horario_fim, '%H:%i') horario_fim_format,
            ds.dia_semana
            FROM regras_agendamento ra
            JOIN dias_semana ds ON (ra.id_dia_semana = ds.id_dia_semana)
            WHERE ra.ativo = '1'
            AND ra.id_dia_semana = :id_dia_semana
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_dia_semana', $id_dia_semana);
        } else {
            $sql = "
            SELECT ra.*, DATE_FORMAT(ra.data_inicio, '%d/%m/%Y') data_inicio_format, DATE_FORMAT(ra.data_fim, '%d/%m/%Y') data_fim_format, DATE_FORMAT(ra.horario_inicio, '%H:%i') horario_inicio_format, DATE_FORMAT(ra.horario_fim, '%H:%i') horario_fim_format,
            ds.dia_semana
            FROM regras_agendamento ra
            JOIN dias_semana ds ON (ra.id_dia_semana = ds.id_dia_semana)
            WHERE ra.ativo = '1'
            ORDER BY ra.id_dia_semana
            ";
            $stmt = $conn->prepare($sql);
        }

        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        $result = getResult($stmt);
    } catch (\Throwable $th) {
        http_response_code(500);
        $result = $th->getMessage();
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
