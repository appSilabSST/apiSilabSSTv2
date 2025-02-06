<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_rl_proposta_exame = trim($_GET["id"]);
            $sql = "
            SELECT rl_pe.* ,
            e.id_exame , e.cod_esocial , e.procedimento , e.valor_cobrar
            FROM propostas p
            JOIN rl_propostas_exames rl_pe ON (p.id_proposta = rl_pe.id_proposta)
            JOIN exames e ON (e.id_exame = rl_pe.id_exame)
            WHERE rl_pe.id_rl_proposta_exame = :id_rl_proposta_exame
            ORDER BY e.procedimento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_proposta_exame', $id_rl_proposta_exame);
        } elseif (isset($_GET["id_proposta"]) && is_numeric($_GET["id_proposta"])) {
            $id_proposta = trim($_GET["id_proposta"]);
            $sql = "
            SELECT rl_pe.* ,
            e.id_exame , e.cod_esocial , e.procedimento , e.valor_cobrar
            FROM propostas p
            JOIN rl_propostas_exames rl_pe ON (p.id_proposta = rl_pe.id_proposta)
            JOIN exames e ON (e.id_exame = rl_pe.id_exame)
            WHERE rl_pe.id_proposta = :id_proposta
            ORDER BY e.procedimento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_proposta', $id_proposta);
        } else {
            $sql = "
            SELECT rl_pe.* ,
            e.id_exame , e.cod_esocial , e.procedimento , e.valor_cobrar
            FROM propostas p
            JOIN rl_propostas_exames rl_pe ON (p.id_proposta = rl_pe.id_proposta)
            JOIN exames e ON (e.id_exame = rl_pe.id_exame)
            ORDER BY e.procedimento
            ";
            $stmt = $conn->prepare($sql);
        }

        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        $result = getResult($stmt);
    } catch (\Throwable $th) {
        http_response_code(501);
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
