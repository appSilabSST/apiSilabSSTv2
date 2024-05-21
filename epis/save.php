<?php

  include_once('../conexao.php');

  $postjson = json_decode(file_get_contents('php://input'), true);


  //LISTAGEM DOS USUARIOS E PESQUISA PELO NOME E EMAIL

  if($postjson['requisicao'] == 'save'){

    if($postjson['id'] > 0) {

      $sql = "
      UPDATE epis SET
      epi = '".mysqli_real_escape_string($conecta,trim($postjson["epi"])) ."',
      grupo = '".mysqli_real_escape_string($conecta,trim($postjson["grupo"])) ."'
      WHERE id_epi = " . $postjson["id"];

    } else {

      $sql = "
      INSERT INTO epis (epi, grupo) VALUES 
      (
        '".mysqli_real_escape_string($conecta,trim($postjson['epi'])) ."', 
        '".mysqli_real_escape_string($conecta,trim($postjson['grupo'])) ."'
      )
      ";
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