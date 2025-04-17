<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_escala_profissional = trim($_GET["id_escala_profissional"]);
            $sql = "
            SELECT e.nome AS nome_especialdade,e.`*`,p.`*`,ep.`*`,sa.nome AS sala
            FROM escalas_profissionais ep
            JOIN profissionais p ON (p.id_profissional = ep.id_profissional)
            JOIN especialidades e ON (e.id_especialidade = p.id_especialidade)
            JOIN salas_atendimentos sa ON (sa.id_sala_atendimento = ep.id_sala_atendimento)
            WHERE id_escala_profissional = :id_escala_profissional
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_escala_profissional', $id_escala_profissional);
        } else if (isset($_GET["id_profissional"]) && is_numeric($_GET["id_profissional"])) {
            $id_profissional = trim($_GET["id_profissional"]);
            $sql = "
            SELECT e.nome AS nome_especialdade,e.`*`,p.`*`,ep.`*`,sa.nome AS sala
            FROM escalas_profissionais ep
            JOIN profissionais p ON (p.id_profissional = ep.id_profissional)
            JOIN especialidades e ON (e.id_especialidade = p.id_especialidade)
            JOIN salas_atendimentos sa ON (sa.id_sala_atendimento = ep.id_sala_atendimento)
            WHERE id_escala_profissional = :id_escala_profissional
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_escala_profissional', $id_escala_profissional);
        } else if (isset($_GET["data"])) {
            $data = trim($_GET["data"]);
            $sql = "
            SELECT e.nome AS nome_especialdade,e.`*`,p.`*`,ep.`*`,sa.nome AS sala
            FROM escalas_profissionais ep
            JOIN profissionais p ON (p.id_profissional = ep.id_profissional)
            JOIN especialidades e ON (e.id_especialidade = p.id_especialidade)
            JOIN salas_atendimentos sa ON (sa.id_sala_atendimento = ep.id_sala_atendimento)
            WHERE data = :data
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':data', $data);
        } else {
            $sql = "
            SELECT e.nome AS nome_especialdade,e.`*`,p.`*`,ep.`*`,sa.nome AS sala
            FROM escalas_profissionais ep
            JOIN profissionais p ON (p.id_profissional = ep.id_profissional)
            JOIN especialidades e ON (e.id_especialidade = p.id_especialidade)
            JOIN salas_atendimentos sa ON (sa.id_sala_atendimento = ep.id_sala_atendimento)
            ";
            $stmt = $conn->prepare($sql);
        }

        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        $result = getResult($stmt);
    } catch (\Throwable $th) {
        http_response_code(500);
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
