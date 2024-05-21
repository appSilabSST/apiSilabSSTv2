<?php

    include_once('../conexao.php');

    $postjson = json_decode(file_get_contents('php://input'), true);

    // LISTAGEM DE REGISTROS
    if($postjson['requisicao'] == 'listar'){
        $id = $postjson['id'];

        // SE TIVER ID PARA BUSCA
        if($id > 0) {
            $where = " AND f.id_fornecedor = " . $id;
        }

        $sql = "SELECT *
        FROM fornecedores f
        WHERE ativo = '1'
        $where";

        $query  = mysqli_query($conecta,$sql);

        if(mysqli_num_rows($query) > 0) {
            while ($row = mysqli_fetch_object($query)){
                $dados[] = $row;
            }
        }

        if($query) {
            $result = json_encode(array(
                'success' => true,
                'result' => $dados
            ));
        }else {
            $result = json_encode(array(
                'success' => false
            ));
        }

        echo $result;
        exit;

    }
?>