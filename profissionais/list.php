<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {

    $id = trim($postjson['id']);
    $id_especialidade = $postjson['id_especialidade'];

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where.= "
            AND p.id_profissional = " . mysqli_real_escape_string($conecta, $id) . "
        ";
    }

    // SE TIVER ID_ESPECIALIDADE PARA BUSCA
    if ($id_especialidade != null && count($id_especialidade) > 0) {
        $id_especialidade = implode(',',$id_especialidade);

        $where.= "
            AND p.id_especialidade IN ( " . mysqli_real_escape_string($conecta, $id_especialidade) . " )
        ";
    }

    $sql = "
        SELECT p.*,
        e.nome nome_especialidade
        FROM profissionais p
        LEFT JOIN especialidades e ON (p.id_especialidade = e.id_especialidade)
        WHERE p.ativo = '1'
        $where
        ORDER BY p.nome
        ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_object($query)) {

            // FORMATAR CPF
            $row->cpf_mask = substr($row->cpf, 0, 3) . '.' . substr($row->cpf, 3, 3) . '.' . substr($row->cpf, 6, 3) . '-' . substr($row->cpf, 9, 2);

            $dados[] = $row;
        }
    } else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Nenhum profissional foi encontrado'
        ));
        echo $result;
        exit;
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

}

