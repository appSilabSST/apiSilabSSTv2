<?php

  include_once('../conexao.php');

  $postjson = json_decode(file_get_contents('php://input'), true);


  //LISTAGEM DOS USUARIOS E PESQUISA PELO NOME E EMAIL

  if($postjson['requisicao'] == 'save'){

    if($postjson['id'] > 0) {

      $sql = "UPDATE tipo_atendimentos SET
              atendimento = '".mysqli_real_escape_string($conecta,trim($postjson["atendimento"])) ."'
              WHERE id_tipo_atendimento = " . $postjson["id"];

    } else {

      $sql = "INSERT INTO tipo_atendimentos (atendimento) VALUES 
              (
                '".mysqli_real_escape_string($conecta,trim($postjson['atendimento'])) ."'
              )";
    }  

    //echo $sql;
    $query  = mysqli_query($conecta,$sql);

    if($query){

        $result = json_encode(array(
          'success'=>true
        ));

    }else{

        $result = json_encode(array(
          'success'=>false
        ));
    }

    echo $result;

  }
?>