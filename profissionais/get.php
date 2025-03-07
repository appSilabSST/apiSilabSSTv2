<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_profissional = trim($_GET["id"]);
            $sql = "
            SELECT p.*, tp_o.siglas, e.nome AS nome_especialidade
            FROM profissionais p
            JOIN tipos_orgao tp_o ON tp_o.id_tipo_orgao = p.id_tipo_orgao
            LEFT JOIN especialidades e ON (p.id_especialidade = e.id_especialidade)
            WHERE p.ativo = '1'
            AND p.id_profissional = :id_profissional
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_profissional', $id_profissional);
        } elseif (isset($_GET["id_especialidade"]) && is_numeric($_GET["id_especialidade"])) {
            $id_especialidade = trim($_GET["id_especialidade"]);
            $sql = "
            SELECT p.*, tp_o.siglas, e.nome AS nome_especialidade
            FROM profissionais p
            JOIN tipos_orgao tp_o ON tp_o.id_tipo_orgao = p.id_tipo_orgao
            LEFT JOIN especialidades e ON (p.id_especialidade = e.id_especialidade)
            WHERE p.ativo = '1'
            AND e.id_especialidade = :id_especialidade
            ORDER BY p.nome
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_especialidade', $id_especialidade);
        } else {
            $sql = "
            SELECT p.*, tp_o.siglas, e.nome AS nome_especialidade
            FROM profissionais p
            JOIN tipos_orgao tp_o ON tp_o.id_tipo_orgao = p.id_tipo_orgao
            LEFT JOIN especialidades e ON p.id_especialidade = e.id_especialidade
            WHERE p.ativo = '1'
            ORDER BY p.nome
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
