<?php

  include_once('conexao.php');

  $postjson = json_decode(file_get_contents('php://input'), true);

  $id = trim($postjson["id"]);
  $table = trim($postjson["table"]);
  $field = trim($postjson["field"]);

  //LISTAGEM DOS USUARIOS E PESQUISA PELO NOME E EMAIL
  if($postjson['requisicao'] == 'remove') {

    $sql = "
    UPDATE $table
    SET ativo = 0 
    WHERE $field = $id
    ;";

    if($table == 'regras_agendamento') {
      $sql.= "
      DELETE FROM agendamentos
      WHERE $field = $id
      AND nr_agendamento = 0
      AND data >= CURDATE()
      ;";
    }

    // echo $sql;exit;
    
    $query  = mysqli_multi_query($conecta,$sql);

    if($query){
        $result = json_encode(array(
          'success' => true
        ));

    }else{
        $result = json_encode(array(
          'success' => false
        ));
    }

    echo $result;

  }
