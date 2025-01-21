<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_rl_setor_risco = trim($_GET["id"]);
            $sql = "
            SELECT rl.*,
            r.descricao , r.cod_esocial , r.grupo , r.cor ,
            s.id_setor,s.setor,
            lt.id_local_atividade,lt.razao_social,
            te.id_tipo_exposicao,te.tipo_exposicao,
            cr.id_classificacao_risco,cr.classificacao_risco,
            mp.id_meio_propagacao,mp.meio_propagacao
            FROM rl_setores_riscos AS rl
            JOIN riscos r ON (rl.id_risco = r.id_risco)
            JOIN setores AS s ON (rl.id_setor = s.id_setor)
            JOIN locais_atividade AS lt ON (s.id_local_atividade = lt.id_local_atividade)
            LEFT JOIN tipos_exposicao AS te ON (te.id_tipo_exposicao = rl.id_tipo_exposicao)
            LEFT JOIN classificacao_riscos AS cr ON (cr.id_classificacao_risco = rl.id_classificacao_risco)
            LEFT JOIN meios_propagacao AS mp ON (mp.id_meio_propagacao = rl.id_meio_propagacao)
            WHERE rl.ativo = 1
            AND rl.id_rl_setor_risco = :id_rl_setor_risco
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_setor_risco', $id_rl_setor_risco);
        } elseif (isset($_GET["id_setor"]) && is_numeric($_GET["id_setor"])) {
            $id_setor = trim($_GET["id_setor"]);
            $sql = "
            SELECT rl.*,
            r.descricao, r.cod_esocial , r.grupo , r.cor ,
            s.id_setor,s.setor,
            lt.id_local_atividade,lt.razao_social,
            te.id_tipo_exposicao,te.tipo_exposicao,
            cr.id_classificacao_risco,cr.classificacao_risco,
            mp.id_meio_propagacao,mp.meio_propagacao,
            rlpa.id_rl_setor_risco_plano_acao,rlpa.plano_acao,rlpa.descricao as descricao_plano,rlpa.medida_suficiente,rlpa.data_avaliacao,rlpa.indicacao_medida
            FROM rl_setores_riscos AS rl
            JOIN riscos r ON (rl.id_risco = r.id_risco)
            JOIN setores AS s ON (rl.id_setor = s.id_setor)
            JOIN locais_atividade AS lt ON (s.id_local_atividade = lt.id_local_atividade)
            LEFT JOIN tipos_exposicao AS te ON (te.id_tipo_exposicao = rl.id_tipo_exposicao)
            LEFT JOIN classificacao_riscos AS cr ON (cr.id_classificacao_risco = rl.id_classificacao_risco)
            LEFT JOIN meios_propagacao AS mp ON (mp.id_meio_propagacao = rl.id_meio_propagacao)
            LEFT JOIN rl_setores_riscos_planos_acao rlpa ON (rlpa.id_rl_setor_risco = rl.id_rl_setor_risco)
            WHERE rl.ativo = 1
            AND rl.id_setor = :id_setor
            ORDER BY s.setor,r.grupo,r.descricao
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_setor', $id_setor);
        } elseif (isset($_GET["id_rl_setor_funcao"]) && is_numeric($_GET["id_rl_setor_funcao"])) {
            $id_rl_setor_funcao = trim($_GET["id_rl_setor_funcao"]);
            $sql = "
            SELECT rl.*,
            r.descricao , r.cod_esocial , r.grupo , r.cor ,
            s.id_setor,s.setor,
            lt.id_local_atividade,lt.razao_social,
            te.id_tipo_exposicao,te.tipo_exposicao,
            cr.id_classificacao_risco,cr.classificacao_risco,
            mp.id_meio_propagacao,mp.meio_propagacao
            FROM rl_setores_riscos AS rl
            JOIN riscos r ON (rl.id_risco = r.id_risco)
            JOIN setores AS s ON (rl.id_setor = s.id_setor)
            JOIN locais_atividade AS lt ON (s.id_local_atividade = lt.id_local_atividade)
            LEFT JOIN tipos_exposicao AS te ON (te.id_tipo_exposicao = rl.id_tipo_exposicao)
            LEFT JOIN classificacao_riscos AS cr ON (cr.id_classificacao_risco = rl.id_classificacao_risco)
            LEFT JOIN meios_propagacao AS mp ON (mp.id_meio_propagacao = rl.id_meio_propagacao)
            WHERE rl.ativo = 1
            AND s.id_setor = (
                SELECT id_setor
                FROM rl_setores_funcoes
                WHERE id_rl_setor_funcao = :id_rl_setor_funcao
            )
            ORDER BY s.setor,r.grupo,r.descricao
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_setor_funcao', $id_rl_setor_funcao);
        } elseif (isset($_GET["id_local_atividade"]) && is_numeric($_GET["id_local_atividade"])) {
            $id_local_atividade = trim($_GET["id_local_atividade"]);
            $sql = "
            SELECT rl.*,
            r.descricao , r.cod_esocial , r.grupo , r.cor ,
            s.id_setor,s.setor,
            lt.id_local_atividade,lt.razao_social,
            te.id_tipo_exposicao,te.tipo_exposicao,
            cr.id_classificacao_risco,cr.classificacao_risco,
            mp.id_meio_propagacao,mp.meio_propagacao
            FROM rl_setores_riscos AS rl
            JOIN riscos r ON (rl.id_risco = r.id_risco)
            JOIN setores AS s ON (rl.id_setor = s.id_setor)
            JOIN locais_atividade AS lt ON (s.id_local_atividade = lt.id_local_atividade)
            LEFT JOIN tipos_exposicao AS te ON (te.id_tipo_exposicao = rl.id_tipo_exposicao)
            LEFT JOIN classificacao_riscos AS cr ON (cr.id_classificacao_risco = rl.id_classificacao_risco)
            LEFT JOIN meios_propagacao AS mp ON (mp.id_meio_propagacao = rl.id_meio_propagacao)
            WHERE rl.ativo = 1
            AND lt.id_local_atividade = :id_local_atividade
            ORDER BY s.setor,r.grupo,r.descricao
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_local_atividade', $id_local_atividade);
        } else {
            $sql = "
            SELECT rl.*,
            r.descricao , r.cod_esocial , r.grupo , r.cor ,
            s.id_setor,s.setor,
            lt.id_local_atividade,lt.razao_social,
            te.id_tipo_exposicao,te.tipo_exposicao,
            cr.id_classificacao_risco,cr.classificacao_risco,
            mp.id_meio_propagacao,mp.meio_propagacao
            FROM rl_setores_riscos AS rl
            JOIN riscos r ON (rl.id_risco = r.id_risco)
            JOIN setores AS s ON (rl.id_setor = s.id_setor)
            JOIN locais_atividade AS lt ON (s.id_local_atividade = lt.id_local_atividade)
            LEFT JOIN tipos_exposicao AS te ON (te.id_tipo_exposicao = rl.id_tipo_exposicao)
            LEFT JOIN classificacao_riscos AS cr ON (cr.id_classificacao_risco = rl.id_classificacao_risco)
            LEFT JOIN meios_propagacao AS mp ON (mp.id_meio_propagacao = rl.id_meio_propagacao)
            WHERE rl.ativo = 1
            ORDER BY s.setor,r.grupo,r.descricao
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
