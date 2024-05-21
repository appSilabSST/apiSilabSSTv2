<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = trim($postjson['id']);
    $id_proposta = trim($postjson['id_proposta']);

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where = "
            AND rl_pe.id_rl_proposta_exame = " . mysqli_real_escape_string($conecta, $id) . "
        ";
    }

    // SE TIVER ID PROPOSTA PARA BUSCA
    if ($id_proposta > 0) {
        $where = "
            AND p.id_proposta = " . mysqli_real_escape_string($conecta, $id_proposta) . "
        ";
    }

    $sql = "
        SELECT rl_pe.* ,
        e.id_exame , e.cod , e.procedimento , e.valor_cobrar
        FROM propostas p
        JOIN rl_propostas_exames rl_pe ON (p.id_proposta = rl_pe.id_proposta)
        JOIN exames e ON (e.id_exame = rl_pe.id_exame)
        WHERE p.ativo = '1'
        $where
        ORDER BY e.procedimento
        ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_object($query)) {

            // FORMATAR NOME DO PROCEDIMENTO COM CÓD ESOCIAL
            if (!empty($row->cod)) {
                $row->procedimento_format = $row->procedimento . ' | eSocial: ' . $row->cod;
            } else {
                $row->procedimento_format = $row->procedimento;
            }

            // FORMATAR VALOR E VALOR_COBRAR
            $row->valor_mask = number_format($row->valor, 2, ',', '.');
            $row->valor_cobrar_mask = number_format($row->valor_cobrar, 2, ',', '.');

            $dados[] = $row;
        }
    } else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Nenhum Exame foi encontrado'
        ));
        echo $result;
        exit;
    }
}

if ($query) {
    $result = json_encode(array(
        'success' => true,
        'result' => $dados,
    ));
} else {
    $result = json_encode(array(
        'success' => false
    ));
}

echo $result;
exit;
