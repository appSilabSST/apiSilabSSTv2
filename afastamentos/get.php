<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        // SELECIONAR UM AFASTAMENTO ESPECÍFICO
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_afastamento = trim($_GET["id"]);
            $sql = "
            SELECT a.id_afastamento, a.id_rl_colaborador_empresa, a.data_entrega, DATE_FORMAT(a.data_entrega, '%d/%m/%Y') data_entrega_format, a.data_afastamento, DATE_FORMAT(a.data_afastamento, '%d/%m/%Y') data_afastamento_format, a.data_retorno, DATE_FORMAT(a.data_retorno, '%d/%m/%Y') data_retorno_format, a.num_dias, a.cid, a.observacao
            FROM afastamentos a
            JOIN rl_colaboradores_empresas rl ON (a.id_rl_colaborador_empresa = rl.id_rl_colaborador_empresa)
            WHERE a.ativo = 1
            AND a.id_afastamento = :id_afastamento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_afastamento', $id_afastamento);
        }
        // SELECIONAR AFASTAMENTOS DE UMA EMPRESA ESPECÍFICA
        elseif (isset($_GET["id_empresa"]) && is_numeric($_GET["id_empresa"])) {
            $id_empresa = trim($_GET["id_empresa"]);
            $sql = "
            SELECT a.id_afastamento, a.id_rl_colaborador_empresa, a.data_entrega, DATE_FORMAT(a.data_entrega, '%d/%m/%Y') data_entrega_format, a.data_afastamento, DATE_FORMAT(a.data_afastamento, '%d/%m/%Y') data_afastamento_format, a.data_retorno, DATE_FORMAT(a.data_retorno, '%d/%m/%Y') data_retorno_format, a.num_dias, a.cid, a.observacao
            FROM afastamentos a
            JOIN rl_colaboradores_empresas rl ON (a.id_rl_colaborador_empresa = rl.id_rl_colaborador_empresa)
            WHERE a.ativo = 1
            AND rl.id_empresa = :id_empresa
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_empresa', $id_empresa);
        }
        // SELECIONAR AFASTAMENTOS DE UM COLABORADOR ESPECÍFICO 
        elseif (isset($_GET["id_colaborador"]) && is_numeric($_GET["id_colaborador"])) {
            $id_colaborador = trim($_GET["id_colaborador"]);
            $sql = "
            SELECT a.id_afastamento, a.id_rl_colaborador_empresa, a.data_entrega, DATE_FORMAT(a.data_entrega, '%d/%m/%Y') data_entrega_format, a.data_afastamento, DATE_FORMAT(a.data_afastamento, '%d/%m/%Y') data_afastamento_format, a.data_retorno, DATE_FORMAT(a.data_retorno, '%d/%m/%Y') data_retorno_format, a.num_dias, a.cid, a.observacao
            FROM afastamentos a
            JOIN rl_colaboradores_empresas rl ON (a.id_rl_colaborador_empresa = rl.id_rl_colaborador_empresa)
            JOIN colaboradores c ON (rl.id_colaborador = c.id_colaborador)
            WHERE a.ativo = 1
            AND c.id_colaborador = :id_colaborador
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_colaborador', $id_colaborador);
        }
        // SELECIONAR TODOS OS AFASTAMENTOS 
        else {
            $sql = "
            SELECT a.id_afastamento, a.id_rl_colaborador_empresa, a.data_entrega, DATE_FORMAT(a.data_entrega, '%d/%m/%Y') data_entrega_format, a.data_afastamento, DATE_FORMAT(a.data_afastamento, '%d/%m/%Y') data_afastamento_format, a.data_retorno, DATE_FORMAT(a.data_retorno, '%d/%m/%Y') data_retorno_format, a.num_dias, a.cid, a.observacao
            FROM afastamentos a
            JOIN rl_colaboradores_empresas rl ON (a.id_rl_colaborador_empresa = rl.id_rl_colaborador_empresa)
            WHERE a.ativo = 1
            ORDER BY a.data_entrega DESC
            ";
            $stmt = $conn->prepare($sql);
        }
        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        if ($stmt->rowCount() < 1) {
            $result = array(
                'status' => 'fail',
                'result' => 'Nenhum afastamento foi encontrado'
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
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
} else {
    http_response_code(403);
    echo json_encode(
        array(
            'status' => 'fail',
            'result' => 'Sem autorização para acessar este conteúdo!'
        )
    );
}
exit;
