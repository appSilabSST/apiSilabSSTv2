<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_plano_acao = trim($_GET["id"]);
            $sql = "
            SELECT pa.* ,
            r.descricao descricao_risco, r.cod_esocial, IF(r.cod_esocial IS NOT NULL, CONCAT(r.descricao, ' | eSocial: ' , r.cod_esocial), r.descricao) agente_nocivo
            FROM planos_acao pa
            LEFT JOIN riscos r ON (pa.id_risco = r.id_risco)
            WHERE pa.ativo = 1
            AND pa.id_plano_acao = :id_plano_acao
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_plano_acao', $id_plano_acao);
        } elseif (isset($_GET["id_risco"]) && is_numeric($_GET["id_risco"])) {
            $id_risco = trim($_GET["id_risco"]);
            $sql = "
            SELECT pa.* ,
            r.descricao descricao_risco, r.cod_esocial, IF(r.cod_esocial IS NOT NULL, CONCAT(r.descricao, ' | eSocial: ' , r.cod_esocial), r.descricao) agente_nocivo
            FROM planos_acao pa
            LEFT JOIN riscos r ON (pa.id_risco = r.id_risco)
            WHERE pa.ativo = 1
            AND pa.id_risco = :id_risco
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_risco', $id_risco);
        } else {
            $sql = "
            SELECT pa.* ,
            r.descricao descricao_risco, r.cod_esocial, IF(r.cod_esocial IS NOT NULL, CONCAT(r.descricao, ' | eSocial: ' , r.cod_esocial), r.descricao) agente_nocivo
            FROM planos_acao pa
            LEFT JOIN riscos r ON (pa.id_risco = r.id_risco)
            WHERE pa.ativo = 1
            ORDER BY pa.plano_acao
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
