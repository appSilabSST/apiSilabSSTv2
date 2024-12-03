<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_setor = trim($_GET["id"]);
            $sql = "
            SELECT s.id_setor, s.id_local_atividade, s.setor, s.descricao, s.conclusao, s.status,
            la.razao_social,
            (
                SELECT GROUP_CONCAT(IF(LENGTH(r.cod_esocial) > 0, CONCAT_WS(' | eSocial: ' , r.procedimento , r.cod_esocial), r.procedimento) ORDER BY r.procedimento SEPARATOR '||')
                FROM rl_setores_exames AS b
                JOIN exames r ON b.id_exame = r.id_exame
                WHERE b.id_setor = s.id_setor
            ) exames,
            (
                SELECT GROUP_CONCAT(a.funcao ORDER BY a.funcao)
                FROM rl_setores_funcoes AS a
                WHERE a.id_setor = s.id_setor
            ) funcoes,
            (
                SELECT GROUP_CONCAT(IF(LENGTH(r.cod_esocial) > 0, CONCAT_WS(' | eSocial: ' , r.descricao , r.cod_esocial), r.descricao) ORDER BY r.descricao SEPARATOR '||')
                FROM rl_setores_riscos AS b
                JOIN riscos r ON b.id_risco = r.id_risco
                WHERE b.id_setor = s.id_setor
            ) riscos
            FROM setores s
            LEFT JOIN locais_atividade la ON (s.id_local_atividade = la.id_local_atividade)
            WHERE s.ativo = 1
            AND s.id_setor = :id_setor
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_setor', $id_setor);
        } elseif (isset($_GET["id_local_atividade"]) && is_numeric($_GET["id_local_atividade"])) {
            $id_local_atividade = trim($_GET["id_local_atividade"]);
            $sql = "
            SELECT s.id_setor, s.id_local_atividade, s.setor, s.descricao, s.conclusao, s.status,
            la.razao_social,
            (
                SELECT GROUP_CONCAT(IF(LENGTH(r.cod_esocial) > 0, CONCAT_WS(' | eSocial: ' , r.procedimento , r.cod_esocial), r.procedimento) ORDER BY r.procedimento SEPARATOR '||')
                FROM rl_setores_exames AS b
                JOIN exames r ON b.id_exame = r.id_exame
                WHERE b.id_setor = s.id_setor
            ) exames,
            (
                SELECT GROUP_CONCAT(a.funcao ORDER BY a.funcao)
                FROM rl_setores_funcoes AS a
                WHERE a.id_setor = s.id_setor
            ) funcoes,
            (
                SELECT GROUP_CONCAT(IF(LENGTH(r.cod_esocial) > 0, CONCAT_WS(' | eSocial: ' , r.descricao , r.cod_esocial), r.descricao) ORDER BY r.descricao SEPARATOR '||')
                FROM rl_setores_riscos AS b
                JOIN riscos r ON b.id_risco = r.id_risco
                WHERE b.id_setor = s.id_setor
            ) riscos
            FROM setores s
            LEFT JOIN locais_atividade la ON (s.id_local_atividade = la.id_local_atividade)
            WHERE s.ativo = 1
            AND s.id_local_atividade = :id_local_atividade
            ORDER BY s.setor
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_local_atividade', $id_local_atividade);
        } else {
            $sql = "
            SELECT s.id_setor, s.id_local_atividade, s.setor, s.descricao, s.conclusao, s.status,
            la.razao_social,
            (
                SELECT GROUP_CONCAT(IF(LENGTH(r.cod_esocial) > 0, CONCAT_WS(' | eSocial: ' , r.procedimento , r.cod_esocial), r.procedimento) ORDER BY r.procedimento SEPARATOR '||')
                FROM rl_setores_exames AS b
                JOIN exames r ON b.id_exame = r.id_exame
                WHERE b.id_setor = s.id_setor
            ) exames,
            (
                SELECT GROUP_CONCAT(a.funcao ORDER BY a.funcao)
                FROM rl_setores_funcoes AS a
                WHERE a.id_setor = s.id_setor
            ) funcoes,
            (
                SELECT GROUP_CONCAT(IF(LENGTH(r.cod_esocial) > 0, CONCAT_WS(' | eSocial: ' , r.descricao , r.cod_esocial), r.descricao) ORDER BY r.descricao SEPARATOR '||')
                FROM rl_setores_riscos AS b
                JOIN riscos r ON b.id_risco = r.id_risco
                WHERE b.id_setor = s.id_setor
            ) riscos
            FROM setores s
            LEFT JOIN locais_atividade la ON (s.id_local_atividade = la.id_local_atividade)
            WHERE s.ativo = 1
            ORDER BY s.setor
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
