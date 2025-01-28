<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = trim($postjson['id']);
    $cpf = trim($postjson['cpf']);

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where = "
        AND id_colaborador = " . mysqli_real_escape_string($conecta, $id) . "
        ";
    }

    // SE TIVER ID PARA BUSCA
    if (!empty($cpf)) {
        $where = "
        AND cpf = '" . mysqli_real_escape_string($conecta, $cpf) . "'
        ";
    }

    $sql = "
    SELECT *
    FROM colaboradores
    WHERE ativo = '1'
    $where
    ORDER BY nome
    ";
    $query  = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_object($query)) {

            if ($row->deficiente == 1) {
                $row->deficiente = true;
            } else {
                $row->deficiente = false;
            }

            if (!empty($row->cpf)) {
                $row->cpf_mask = substr($row->cpf, 0, 3) . '.' . substr($row->cpf, 3, 3) . '.' . substr($row->cpf, 6, 3) . '-' . substr($row->cpf, 9, 2);
            }

            if (!empty($row->celular)) {
                $row->celular_mask = '(' . substr($row->celular, 0, 2) . ') ' . substr($row->celular, 2, 1) . ' ' . substr($row->celular, 3, 4) . '-' . substr($row->celular, 7, 4);
            }

            if (!empty($row->rg)) {
                if (strlen($row->rg) > 8) {
                    $row->rg_mask = substr($row->rg, 0, 2) . '.' . substr($row->rg, 2, 3) . '.' . substr($row->rg, 5, 3) . '-' . substr($row->rg, 8, 1);
                } else {
                    $row->rg_mask = substr($row->rg, 0, 2) . '.' . substr($row->rg, 2, 3) . '.' . substr($row->rg, 5, 3);
                }
            }


            $dados[] = $row;
        }


        $result = json_encode(array(
            'success' => true,
            'result' => $dados
        ));
    } else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Nenhum colaborador foi encontrado'
        ));
    }

    echo $result;
    exit;
}
