<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_rl_proposta_servico = trim($_GET["id"]);
            $sql = "
            SELECT rl.* ,
            s.servico
            FROM propostas p
            JOIN rl_propostas_servicos rl ON (p.id_proposta = rl.id_proposta)
            JOIN servicos s ON (s.id_servico = rl.id_servico)
            WHERE p.ativo = '1'
            AND rl.id_rl_proposta_servico = :id_rl_proposta_servico
            ORDER BY s.servico
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_proposta_servico', $id_rl_proposta_servico);
        } elseif (isset($_GET["id_proposta"]) && is_numeric($_GET["id_proposta"])) {
            $id_proposta = trim($_GET["id_proposta"]);
            $sql = "
            SELECT rl.* ,
            s.servico
            FROM propostas p
            JOIN rl_propostas_servicos rl ON (p.id_proposta = rl.id_proposta)
            JOIN servicos s ON (s.id_servico = rl.id_servico)
            WHERE p.ativo = '1'
            AND rl.id_proposta = :id_proposta
            ORDER BY s.servico
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_proposta', $id_proposta);
        } else {
            $sql = "
            SELECT rl.* ,
            s.servico
            FROM propostas p
            JOIN rl_propostas_servicos rl ON (p.id_proposta = rl.id_proposta)
            JOIN servicos s ON (s.id_servico = rl.id_servico)
            WHERE p.ativo = '1'
            ORDER BY s.servico
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
