<?php

    include_once('../conexao.php');

    $postjson = json_decode(file_get_contents('php://input'), true);

    // LISTAGEM DE REGISTROS
    if($postjson['requisicao'] == 'listar'){
        $id = $postjson['id'];

        $sql = "
        SELECT *
        FROM tipo_ambiente t
        ";

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