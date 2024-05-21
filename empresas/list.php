<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = trim($postjson['id']);
    $status = trim($postjson['status']);

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where = "
            AND e.id_empresa = " . mysqli_real_escape_string($conecta, $id) . "
        ";
    }

    // SE TIVER STATUS PARA BUSCA
    if (!empty($status)) {
        $where = "
            AND e.status = " . mysqli_real_escape_string($conecta, $status) . "
        ";
    }

    $sql = "
        SELECT e.*
        FROM empresas e
        WHERE e.ativo = '1'
        $where
        ORDER BY e.status DESC,e.razao_social
        ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_object($query)) {

            // FORMATAR NR INSCRIÇÃO
            $row->nr_inscricao_mask = "";
            if (!empty($row->nr_inscricao)) {
                if ($row->tipo_inscricao == 1) {
                    $row->tipo_inscricao_mask = 'CNPJ';
                    $row->nr_inscricao_mask = substr($row->nr_inscricao, 0, 2) . '.' . substr($row->nr_inscricao, 2, 3) . '.' . substr($row->nr_inscricao, 5, 3) . '/' . substr($row->nr_inscricao, 8, 4) . '-' . substr($row->nr_inscricao, 12, 2);
                } else {
                    $row->tipo_inscricao_mask = 'CPF';
                    $row->nr_inscricao_mask = substr($row->nr_inscricao, 0, 3) . '.' . substr($row->nr_inscricao, 3, 3) . '.' . substr($row->nr_inscricao, 6, 3) . '-' . substr($row->nr_inscricao, 9, 2);
                }
            }

            // FORMATAR TELEFONE
            if (!empty($row->telefone)) {
                $row->telefone_mask = '(' . substr($row->telefone, 0, 2) . ') ' . substr($row->telefone, 2, 4) . '-' . substr($row->telefone, 6, 4);
            }

            // FORMATAR STATUS
            if ($row->status == 0) {
                $row->status_mask = '
                    <div class="alert mb-0 alert-danger text-center" role="alert">
                    Inativo
                    </div>';
            } elseif ($row->status == 1) {
                $row->status_mask = '
                    <div class="alert mb-0 alert-success text-center" role="alert">
                    Ativo
                    </div>
                    ';
            }

            $dados[] = $row;
        }
    } else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Nenhuma empresa foi encontrada'
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
