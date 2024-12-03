<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_rl_setor_risco_plano_acao = trim($_GET["id"]);
            $sql = "
            SELECT rl.id_rl_setor_risco_plano_acao , rl.id_rl_setor_risco , rl.data_avaliacao , DATE_FORMAT(rl.data_avaliacao, '%d/%m/%Y') data_avaliacao_mask , rl.plano_acao , rl.descricao , rl.medida_suficiente , rl.indicacao_medida ,
            s.setor ,
            r.descricao agente_nocivo , r.cod_esocial
            FROM rl_setores_riscos_planos_acao rl
            JOIN rl_setores_riscos rl1 ON (rl.id_rl_setor_risco = rl1.id_rl_setor_risco)
            JOIN setores s ON (rl1.id_setor = s.id_setor)
            JOIN riscos r ON (r.id_risco = rl1.id_risco)
            WHERE rl.ativo = 1
            AND rl.id_rl_setor_risco_plano_acao = :id_rl_setor_risco_plano_acao
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_setor_risco_plano_acao', $id_rl_setor_risco_plano_acao);
        } elseif (isset($_GET["id_pgr"]) && is_numeric($_GET["id_pgr"])) {
            $id_pgr = trim($_GET["id_pgr"]);
            $sql = "
            SELECT rl.id_rl_setor_risco_plano_acao , rl.id_rl_setor_risco , rl.data_avaliacao , DATE_FORMAT(rl.data_avaliacao, '%d/%m/%Y') data_avaliacao_mask , rl.plano_acao , rl.descricao , rl.medida_suficiente , rl.indicacao_medida ,
            s.setor ,
            r.descricao agente_nocivo , r.cod_esocial
            FROM rl_setores_riscos_planos_acao rl
            JOIN rl_setores_riscos rl1 ON (rl.id_rl_setor_risco = rl1.id_rl_setor_risco)
            JOIN setores s ON (rl1.id_setor = s.id_setor)
            JOIN riscos r ON (r.id_risco = rl1.id_risco)
            WHERE rl.ativo = 1
            AND rl.id_pgr = :id_pgr
            ORDER BY s.setor , r.descricao , rl.plano_acao
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_pgr', $id_pgr);
        } elseif (isset($_GET["id_rl_setor_risco"]) && is_numeric($_GET["id_rl_setor_risco"])) {
            $id_rl_setor_risco = trim($_GET["id_rl_setor_risco"]);
            $sql = "
            SELECT rl.id_rl_setor_risco_plano_acao , rl.id_rl_setor_risco , rl.data_avaliacao , DATE_FORMAT(rl.data_avaliacao, '%d/%m/%Y') data_avaliacao_mask , rl.plano_acao , rl.descricao , rl.medida_suficiente , rl.indicacao_medida ,
            s.setor ,
            r.descricao agente_nocivo , r.cod_esocial
            FROM rl_setores_riscos_planos_acao rl
            JOIN rl_setores_riscos rl1 ON (rl.id_rl_setor_risco = rl1.id_rl_setor_risco)
            JOIN setores s ON (rl1.id_setor = s.id_setor)
            JOIN riscos r ON (r.id_risco = rl1.id_risco)
            WHERE rl.ativo = 1
            AND rl.id_rl_setor_risco = :id_rl_setor_risco
            ORDER BY s.setor , r.descricao , rl.plano_acao
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_setor_risco', $id_rl_setor_risco);
        } else {
            $sql = "
            SELECT rl.id_rl_setor_risco_plano_acao , rl.id_rl_setor_risco , rl.data_avaliacao , DATE_FORMAT(rl.data_avaliacao, '%d/%m/%Y') data_avaliacao_mask , rl.plano_acao , rl.descricao , rl.medida_suficiente , rl.indicacao_medida ,
            s.setor ,
            r.descricao agente_nocivo , r.cod_esocial
            FROM rl_setores_riscos_planos_acao rl
            JOIN rl_setores_riscos rl1 ON (rl.id_rl_setor_risco = rl1.id_rl_setor_risco)
            JOIN setores s ON (rl1.id_setor = s.id_setor)
            JOIN riscos r ON (r.id_risco = rl1.id_risco)
            WHERE rl.ativo = 1
            ORDER BY s.setor , r.descricao , rl.plano_acao
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
