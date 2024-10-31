<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_rl_setor_funcao = trim($_GET["id"]);
            $sql = "
            SELECT r.id_rl_setor_funcao,r.cbo,r.funcao,r.descricao,r.jornada_trabalho,r.qtd_funcionarios,
            s.id_setor,s.setor,
            la.id_local_atividade,la.razao_social
            FROM rl_setores_funcoes AS r
            INNER JOIN setores AS s ON (r.id_setor = s.id_setor)
            INNER JOIN locais_atividade AS la ON (s.id_local_atividade = la.id_local_atividade)
            WHERE r.ativo = 1
            AND r.id_rl_setor_funcao = :id_rl_setor_funcao
            ORDER BY s.setor,r.funcao
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_setor_funcao', $id_rl_setor_funcao);
        } elseif (isset($_GET["id_setor"]) && is_numeric($_GET["id_setor"])) {
            $id_setor = trim($_GET["id_setor"]);
            $sql = "
            SELECT r.id_rl_setor_funcao,r.cbo,r.funcao,r.descricao,r.jornada_trabalho,r.qtd_funcionarios,
            s.id_setor,s.setor,
            la.id_local_atividade,la.razao_social
            FROM rl_setores_funcoes AS r
            INNER JOIN setores AS s ON (r.id_setor = s.id_setor)
            INNER JOIN locais_atividade AS la ON (s.id_local_atividade = la.id_local_atividade)
            WHERE r.ativo = 1
            AND r.id_setor = :id_setor
            ORDER BY s.setor,r.funcao
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_setor', $id_setor);
        } elseif (isset($_GET["id_local_atividade"]) && is_numeric($_GET["id_local_atividade"])) {
            $id_local_atividade = trim($_GET["id_local_atividade"]);
            $sql = "
            SELECT r.id_rl_setor_funcao,r.cbo,r.funcao,r.descricao,r.jornada_trabalho,r.qtd_funcionarios,
            s.id_setor,s.setor,
            la.id_local_atividade,la.razao_social
            FROM rl_setores_funcoes AS r
            INNER JOIN setores AS s ON (r.id_setor = s.id_setor)
            INNER JOIN locais_atividade AS la ON (s.id_local_atividade = la.id_local_atividade)
            WHERE r.ativo = 1
            AND s.id_local_atividade = :id_local_atividade
            ORDER BY s.setor,r.funcao
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_local_atividade', $id_local_atividade);
        } elseif (isset($_GET["id_pcmso"]) && is_numeric($_GET["id_pcmso"])) {
            $id_pcmso = trim($_GET["id_pcmso"]);
            $sql = "
            SELECT r.id_rl_setor_funcao,r.cbo,r.funcao,r.descricao,r.jornada_trabalho,r.qtd_funcionarios,
            s.id_setor,s.setor,
            la.id_local_atividade,la.razao_social
            FROM rl_setores_funcoes AS r
            INNER JOIN setores AS s ON (r.id_setor = s.id_setor)
            INNER JOIN locais_atividade AS la ON (s.id_local_atividade = la.id_local_atividade)
            WHERE r.ativo = 1
            AND s.id_local_atividade = (
                SELECT id_local_atividade
                FROM pcmso
                WHERE id_pcmso = :id_pcmso
            )
            ORDER BY s.setor,r.funcao
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_pcmso', $id_pcmso);
        } else {
            $sql = "
            SELECT r.id_rl_setor_funcao,r.cbo,r.funcao,r.descricao,r.jornada_trabalho,r.qtd_funcionarios,
            s.id_setor,s.setor,
            la.id_local_atividade,la.razao_social
            FROM rl_setores_funcoes AS r
            INNER JOIN setores AS s ON (r.id_setor = s.id_setor)
            INNER JOIN locais_atividade AS la ON (s.id_local_atividade = la.id_local_atividade)
            WHERE r.ativo = 1
            ORDER BY s.setor,r.funcao
            ";
            $stmt = $conn->prepare($sql);
        }

        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        if ($stmt->rowCount() < 1) {
            $result = array(
                'status' => 'fail',
                'result' => 'Nenhuma função foi encontrada'
            );
        } elseif ($stmt->rowCount() == 1) {
            $dados = $stmt->fetch(PDO::FETCH_OBJ);
            $result = array(
                'status' => 'success',
                'result' => $dados
            );
        } else {
            $dados = $stmt->fetchAll(PDO::FETCH_OBJ);
            $result = array(
                'status' => 'success',
                'result' => $dados
            );
        }
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
