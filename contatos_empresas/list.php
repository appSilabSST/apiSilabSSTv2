<?php

    include_once('../conexao.php');

    $postjson = json_decode(file_get_contents('php://input'), true);
    
    // LISTAGEM DE REGISTROS
    if($postjson['requisicao'] == 'listar'){
        $id = $postjson['id'];
        $id_empresa = $postjson['id_empresa'];
        
        // SE TIVER ID PARA BUSCA
        if($id > 0) {
            $where = "
            AND ce.id_contato_empresa = $id
            ";
        }

        // SE TIVER ID EMPRESA
        if($id_empresa > 0) {
            $where = "
            AND ce.id_empresa = $id_empresa
            ";
        }

        $sql = "
        SELECT ce.*
        FROM contatos_empresas ce
        WHERE ce.ativo = 1
        $where
        ORDER BY ce.nome
        ";

        // echo $sql;exit;

        $query  = mysqli_query($conecta,$sql);

        if(mysqli_num_rows($query) > 0) {
            while ($row = mysqli_fetch_object($query)){

                $dados[] = $row;

            }
        } else {
            $result = json_encode(array(
                'success' => false,
                'result' => 'Nenhum contato foi encontrado'
            ));
            echo $result;
            exit;
        }
    }

    if($query) {
        $result = json_encode(array(
            'success' => true,
            'result' => $dados,
        ));
    }else {
        $result = json_encode(array(
            'success' => false
        ));
    }

    echo $result;
    exit;
?>