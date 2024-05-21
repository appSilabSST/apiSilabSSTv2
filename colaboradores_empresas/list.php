<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = trim($postjson['id']);
    $id_colaborador = trim($postjson['id_colaborador']);
    $id_empresa = trim($postjson['id_empresa']);
    $id_local_atividade = trim($postjson['id_local_atividade']);
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

    // SE TIVER id_local_atividade PARA BUSCA
    if ($id_local_atividade > 0) {
        $where .= "
        AND rl.id_local_atividade = " . $id_local_atividade . "
        ";
    }

    $sql = "
    SELECT c.*, 
    rl.id_rl_colaborador_empresa,rl.id_rl_setor_funcao,rl.data_admissao,DATE_FORMAT(rl.data_admissao,'%d/%m/%Y') data_admissao_mask,rl.matricula,rl.status,
    rl2.funcao,
    s.setor,
    lt.id_empresa,lt.id_local_atividade,lt.razao_social local_atividade,
    e.tipo_inscricao, e.nr_inscricao , e.razao_social
    FROM colaboradores c
    JOIN rl_colaboradores_empresas rl ON (rl.id_colaborador = c.id_colaborador AND rl.ativo = '1')
    JOIN rl_setores_funcoes rl2 ON (rl.id_rl_setor_funcao = rl2.id_rl_setor_funcao)
    JOIN setores s ON (rl2.id_setor = s.id_setor)
    JOIN locais_atividade lt ON (s.id_local_atividade = lt.id_local_atividade)
    JOIN empresas e ON (lt.id_empresa = e.id_empresa)
    WHERE c.ativo = 1
    $where
    ORDER BY rl.status DESC , c.nome
    ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_object($query)) {

            $row->razao_social_local_atividade = $row->razao_social . " | " . $row->local_atividade;
            $row->setor_funcao = $row->setor . " | " . $row->funcao;

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
