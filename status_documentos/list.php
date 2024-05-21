<?php

  include_once('../conexao.php');

  $postjson = json_decode(file_get_contents('php://input'), true);

  // LISTAGEM DE REGISTROS
  if($postjson['requisicao'] == 'listar'){
    $id = $postjson['id_status_documento'];

    // SE TIVER ID PARA BUSCA
    if($id > 0) {
        $where.= "
        AND id_status_documento = $id
        ";
    }

    $sql = "
    SELECT id_status_documento , status_documento
    FROM status_documentos
    WHERE ativo = 1
    $where
    ORDER BY id_status_documento
    ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta,$sql);

    if(mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_object($query)){
            $dados[] = $row;
        }
    }
    else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Nenhum registro foi encontrado.'
        ));
        echo $result;
        exit;
    }

    if($query) {
        $result = json_encode(array(
            'success' => true,
            'result' => $dados
        ));
    }else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Falha ao carregar registros.'
        ));
    }
    
    echo $result;
    exit;
    
}
?>