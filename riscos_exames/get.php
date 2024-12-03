<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_rl_risco_exame = trim($_GET["id"]);
            $sql = "
            SELECT rl.*,
            e.procedimento , e.cod_esocial , e.procedimento , CONCAT_WS(' | eSocial: ', e.procedimento , e.cod_esocial) procedimento_format
            FROM rl_riscos_exames AS rl
            JOIN exames e ON (rl.id_exame = e.id_exame)
            WHERE rl.ativo = 1
            AND rl.id_rl_risco_exame = :id_rl_risco_exame
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_risco_exame', $id_rl_risco_exame);
        } elseif (isset($_GET["id_risco"]) && is_numeric($_GET["id_risco"])) {
            $id_risco = trim($_GET["id_risco"]);
            $sql = "
            SELECT rl.*,
            e.procedimento , e.cod_esocial , e.procedimento , CONCAT_WS(' | eSocial: ', e.procedimento , e.cod_esocial) procedimento_format
            FROM rl_riscos_exames AS rl
            JOIN exames e ON (rl.id_exame = e.id_exame)
            WHERE rl.ativo = 1
            AND rl.id_risco = :id_risco
            ORDER BY e.procedimento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_risco', $id_risco);
        } else {
            $sql = "
            SELECT rl.*,
            e.procedimento , e.cod_esocial , e.procedimento , CONCAT_WS(' | eSocial: ', e.procedimento , e.cod_esocial) procedimento_format
            FROM rl_riscos_exames AS rl
            JOIN exames e ON (rl.id_exame = e.id_exame)
            WHERE rl.ativo = 1
            ORDER BY e.procedimento
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
