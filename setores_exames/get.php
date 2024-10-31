<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_rl_setor_exame = trim($_GET["id"]);
            $sql = "
            SELECT rl.*,
            e.procedimento , e.cod_esocial , CONCAT_WS(' | eSocial: ', e.procedimento , e.cod_esocial) procedimento_format,
            s.id_setor,s.setor
            FROM rl_setores_exames AS rl
            JOIN exames e ON (rl.id_exame = e.id_exame)
            JOIN setores AS s ON (rl.id_setor = s.id_setor)
            JOIN locais_atividade AS lt ON (s.id_local_atividade = lt.id_local_atividade)
            WHERE rl.ativo = 1
            AND rl.id_rl_setor_exame = :id_rl_setor_exame
            ORDER BY s.setor,e.procedimento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_setor_exame', $id_rl_setor_exame);
        } elseif (isset($_GET["id_setor"]) && is_numeric($_GET["id_setor"])) {
            $id_setor = trim($_GET["id_setor"]);
            $sql = "
            SELECT rl.*,
            e.procedimento , e.cod_esocial , CONCAT_WS(' | eSocial: ', e.procedimento , e.cod_esocial) procedimento_format,
            s.id_setor,s.setor
            FROM rl_setores_exames AS rl
            JOIN exames e ON (rl.id_exame = e.id_exame)
            JOIN setores AS s ON (rl.id_setor = s.id_setor)
            JOIN locais_atividade AS lt ON (s.id_local_atividade = lt.id_local_atividade)
            WHERE rl.ativo = 1
            AND s.id_setor = :id_setor
            ORDER BY s.setor,e.procedimento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_setor', $id_setor);
        } elseif (isset($_GET["id_local_atividade"]) && is_numeric($_GET["id_local_atividade"])) {
            $id_local_atividade = trim($_GET["id_local_atividade"]);
            $sql = "
            SELECT rl.*,
            e.procedimento , e.cod_esocial , CONCAT_WS(' | eSocial: ', e.procedimento , e.cod_esocial) procedimento_format,
            s.id_setor,s.setor
            FROM rl_setores_exames AS rl
            JOIN exames e ON (rl.id_exame = e.id_exame)
            JOIN setores AS s ON (rl.id_setor = s.id_setor)
            JOIN locais_atividade AS lt ON (s.id_local_atividade = lt.id_local_atividade)
            WHERE rl.ativo = 1
            AND s.id_local_atividade = :id_local_atividade
            ORDER BY s.setor,e.procedimento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_local_atividade', $id_local_atividade);
        } elseif (isset($_GET["id_rl_colaborador_empresa"]) && is_numeric($_GET["id_rl_colaborador_empresa"])) {
            $id_rl_colaborador_empresa = trim($_GET["id_rl_colaborador_empresa"]);
            $sql = "
            SELECT rl.*,
            e.procedimento , e.cod_esocial , CONCAT_WS(' | eSocial: ', e.procedimento , e.cod_esocial) procedimento_format,
            s.id_setor,s.setor
            FROM rl_setores_exames AS rl
            JOIN exames e ON (rl.id_exame = e.id_exame)
            JOIN setores AS s ON (rl.id_setor = s.id_setor)
            JOIN locais_atividade AS lt ON (s.id_local_atividade = lt.id_local_atividade)
            WHERE rl.ativo = 1
            AND s.id_setor = (
                SELECT id_setor
                FROM rl_setores_funcoes rl_sf
                JOIN rl_colaboradores_empresas rl_ce ON rl_sf.id_rl_setor_funcao = rl_ce.id_rl_setor_funcao
                WHERE id_rl_colaborador_empresa = :id_rl_colaborador_empresa
            )
            ORDER BY s.setor,e.procedimento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_colaborador_empresa', $id_rl_colaborador_empresa);
        } elseif (isset($_GET["id_rl_setor_funcao"]) && is_numeric($_GET["id_rl_setor_funcao"])) {
            $id_rl_setor_funcao = trim($_GET["id_rl_setor_funcao"]);
            $sql = "
            SELECT rl.*,
            e.procedimento , e.cod_esocial , CONCAT_WS(' | eSocial: ', e.procedimento , e.cod_esocial) procedimento_format,
            s.id_setor,s.setor
            FROM rl_setores_exames AS rl
            JOIN exames e ON (rl.id_exame = e.id_exame)
            JOIN setores AS s ON (rl.id_setor = s.id_setor)
            JOIN locais_atividade AS lt ON (s.id_local_atividade = lt.id_local_atividade)
            WHERE rl.ativo = 1
            AND s.id_setor = (
                SELECT id_setor
                FROM rl_setores_funcoes
                WHERE id_rl_setor_funcao = :id_rl_setor_funcao
            )
            ORDER BY s.setor,e.procedimento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_setor_funcao', $id_rl_setor_funcao);
        } else {
            $sql = "
            SELECT rl.*,
            e.procedimento , e.cod_esocial , CONCAT_WS(' | eSocial: ', e.procedimento , e.cod_esocial) procedimento_format,
            s.id_setor,s.setor
            FROM rl_setores_exames AS rl
            JOIN exames e ON (rl.id_exame = e.id_exame)
            JOIN setores AS s ON (rl.id_setor = s.id_setor)
            JOIN locais_atividade AS lt ON (s.id_local_atividade = lt.id_local_atividade)
            WHERE rl.ativo = 1
            ORDER BY s.setor,e.procedimento
            ";
            $stmt = $conn->prepare($sql);
        }

        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        if ($stmt->rowCount() < 1) {
            $result = array(
                'status' => 'fail',
                'result' => 'Nenhum exame foi encontrado'
            );
        } elseif ($stmt->rowCount() == 1 && isset($_GET["id"]) && is_numeric($_GET["id"])) {
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
