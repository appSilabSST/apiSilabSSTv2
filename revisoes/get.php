<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_revisao = trim($_GET["id"]);
            $sql = "
            SELECT * , DATE_FORMAT(r.data_inicio, '%d/%m/%Y') data_inicio_format, DATE_FORMAT(r.data_fim, '%d/%m/%Y') data_fim_format
            FROM revisoes AS r
            WHERE r.ativo = 1
            AND r.id_revisao = :id_revisao
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_revisao', $id_revisao);
        } elseif (isset($_GET["id_pcmso"]) && is_numeric($_GET["id_pcmso"])) {
            $id_pcmso = trim($_GET["id_pcmso"]);
            $sql = "
            SELECT * , DATE_FORMAT(r.data_inicio, '%d/%m/%Y') data_inicio_format, DATE_FORMAT(r.data_fim, '%d/%m/%Y') data_fim_format
            FROM revisoes AS r
            WHERE r.ativo = 1
            AND r.id_pcmso = :id_pcmso
            ORDER BY r.data_inicio
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_pcmso', $id_pcmso);
        } elseif (isset($_GET["id_pgr"]) && is_numeric($_GET["id_pgr"])) {
            $id_pgr = trim($_GET["id_pgr"]);
            $sql = "
            SELECT * , DATE_FORMAT(r.data_inicio, '%d/%m/%Y') data_inicio_format, DATE_FORMAT(r.data_fim, '%d/%m/%Y') data_fim_format
            FROM revisoes AS r
            WHERE r.ativo = 1
            AND r.id_pgr = :id_pgr
            ORDER BY r.data_inicio
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_pgr', $id_pgr);
        } elseif (isset($_GET["id_ltcat"]) && is_numeric($_GET["id_ltcat"])) {
            $id_ltcat = trim($_GET["id_ltcat"]);
            $sql = "
            SELECT * , DATE_FORMAT(r.data_inicio, '%d/%m/%Y') data_inicio_format, DATE_FORMAT(r.data_fim, '%d/%m/%Y') data_fim_format,
            IF(status = 0, 'FECHADA', 'ABERTA') status_format
            FROM revisoes AS r
            WHERE r.ativo = 1
            AND r.id_ltcat = :id_ltcat
            ORDER BY r.data_inicio
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_ltcat', $id_ltcat);
        } elseif (isset($_GET["id_proposta"]) && is_numeric($_GET["id_proposta"])) {
            $id_proposta = trim($_GET["id_proposta"]);
            $sql = "
            SELECT * , DATE_FORMAT(r.data_inicio, '%d/%m/%Y') data_inicio_format, DATE_FORMAT(r.data_fim, '%d/%m/%Y') data_fim_format,
            IF(status = 0, 'FECHADA', 'ABERTA') status_format
            FROM revisoes AS r
            WHERE r.ativo = 1
            AND r.id_proposta = :id_proposta
            ORDER BY r.data_inicio
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_proposta', $id_proposta);
        } else {
            $sql = "
            SELECT * , DATE_FORMAT(r.data_inicio, '%d/%m/%Y') data_inicio_format, DATE_FORMAT(r.data_fim, '%d/%m/%Y') data_fim_format
            FROM revisoes AS r
            WHERE r.ativo = 1
            ORDER BY r.data_inicio
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
