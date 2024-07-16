<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = trim($postjson['id']);
    $id_colaborador = trim($postjson['id_colaborador']);
    $id_empresa = trim($postjson['id_empresa']);
    $cpf = trim($postjson['cpf']);

    $where = "";
    $where1 = "";

    // SE TIVER CPF PARA BUSCA
    if (!empty($cpf)) {
        $where .= "
        AND c.cpf = '" . mysqli_real_escape_string($conecta, $cpf) . "'
        ";
    }

    // SE TIVER ID PARA BUSCA
    if ($id_colaborador > 0) {
        $where .= "
        AND c.id_colaborador = " . $id_colaborador . "
        ";
    }

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where .= "
        AND rl.id_rl_colaborador_empresa = " . $id . "
        ";
    }

    // SE TIVER id_local_atividade PARA BUSCA
    if ($id_empresa > 0) {
        $where .= "
        AND rl.id_empresa = " . $id_empresa . "
        ";
    }

    $sql = "
    SELECT c.*, 
    rl.id_rl_colaborador_empresa,rl.data_admissao,DATE_FORMAT(rl.data_admissao,'%d/%m/%Y') data_admissao_mask,rl.matricula,rl.status,
    e.id_empresa, e.tipo_inscricao, e.nr_inscricao , e.razao_social
    FROM colaboradores c
    JOIN rl_colaboradores_empresas rl ON (rl.id_colaborador = c.id_colaborador AND rl.ativo = '1')
    JOIN empresas e ON (rl.id_empresa = e.id_empresa)
    WHERE c.ativo = 1
    $where
    ORDER BY rl.status DESC , c.nome
    ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_object($query)) {

            $row->celular_mask = '(' . substr($row->celular, 0, 2) . ') ' . substr($row->celular, 2, 1) . ' ' . substr($row->celular, 3, 4) . '-' . substr($row->celular, 7, 4);
            $row->cpf_mask = substr($row->cpf, 0, 3) . '.' . substr($row->cpf, 3, 3) . '.' . substr($row->cpf, 6, 3) . '-' . substr($row->cpf, 9, 2);

            if (strlen($row->rg) > 8) {
                $row->rg_mask = substr($row->rg, 0, 2) . '.' . substr($row->rg, 2, 3) . '.' . substr($row->rg, 5, 3) . '-' . substr($row->rg, 8, 1);
            } else {
                $row->rg_mask = substr($row->rg, 0, 2) . '.' . substr($row->rg, 2, 3) . '.' . substr($row->rg, 5, 3);
            }

            // FORMATAR STATUS
            if ($row->status == 0) {
                $row->status_mask = '
                <div class="alert mb-0 alert-danger text-center" role="alert">
                    Inativo
                </div>
                ';
            } elseif ($row->status == 1) {
                $row->status_mask = '
                <div class="alert mb-0 alert-success text-center" role="alert">
                    Ativo
                </div>
                ';
            }

            // FORMATAR NR_INSCRICAO
            if (!empty($row->nr_inscricao)) {
                if ($row->tipo_inscricao == 1) {
                    $row->nr_inscricao_mask = substr($row->nr_inscricao, 0, 2) . '.' . substr($row->nr_inscricao, 2, 3) . '.' . substr($row->nr_inscricao, 5, 3) . '/' . substr($row->nr_inscricao, 8, 4) . '-' . substr($row->nr_inscricao, 12, 2);
                } elseif ($row->tipo_inscricao == 3) {
                    $row->nr_inscricao_mask = substr($row->nr_inscricao, 0, 3) . '.' . substr($row->nr_inscricao, 3, 3) . '.' . substr($row->nr_inscricao, 6, 3) . '/' . substr($row->nr_inscricao, 9, 3) . '-' . substr($row->nr_inscricao, 12, 2);
                } elseif ($row->tipo_inscricao == 4) {
                    $row->nr_inscricao_mask = substr($row->nr_inscricao, 0, 2) . '.' . substr($row->nr_inscricao, 2, 3) . '.' . substr($row->nr_inscricao, 5, 5) . '-' . substr($row->nr_inscricao, 10, 2);
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
