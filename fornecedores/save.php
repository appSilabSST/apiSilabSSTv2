<?php

  include_once('../conexao.php');

  $postjson = json_decode(file_get_contents('php://input'), true);

  if($postjson['requisicao'] == 'save'){

    if($postjson['id'] > 0) {

      $sql = "
      UPDATE fornecedores SET
      razao_social = '".mysqli_real_escape_string($conecta,trim($postjson["razao_social"])) ."',
      nome_fantasia = '".mysqli_real_escape_string($conecta,trim($postjson["nome_fantasia"])) ."',
      cnpj = '".mysqli_real_escape_string($conecta,trim($postjson["cnpj"])) ."',
      email = '".mysqli_real_escape_string($conecta,trim($postjson["email"])) ."',
      representante = '".mysqli_real_escape_string($conecta,trim($postjson["representante"])) ."',
      telefone = '".mysqli_real_escape_string($conecta,trim($postjson["telefone"])) ."',
      cep = '".mysqli_real_escape_string($conecta,trim($postjson["cep"])) ."',
      endereco = '".mysqli_real_escape_string($conecta,trim($postjson["endereco"])) ."',
      bairro = '".mysqli_real_escape_string($conecta,trim($postjson["bairro"])) ."',
      cidade = '".mysqli_real_escape_string($conecta,trim($postjson["cidade"])) ."',
      uf = '".mysqli_real_escape_string($conecta,trim($postjson["uf"])) ."'
      WHERE id_fornecedor = " . $postjson["id"];

    } else {

      $sql = "
      INSERT INTO fornecedores 
      (razao_social, nome_fantasia, cnpj, email, representante, telefone, cep, endereco, bairro, cidade, uf ) 
      VALUES 
      (
        '".mysqli_real_escape_string($conecta,trim($postjson["razao_social"])) ."', 
        '".mysqli_real_escape_string($conecta,trim($postjson["nome_fantasia"])) ."', 
        '".mysqli_real_escape_string($conecta,trim($postjson['cnpj'])) ."', 
        '".mysqli_real_escape_string($conecta,trim($postjson['email'])) ."', 
        '".mysqli_real_escape_string($conecta,trim($postjson['representante'])) ."',
        '".mysqli_real_escape_string($conecta,trim($postjson['telefone'])) ."',
        '".mysqli_real_escape_string($conecta,trim($postjson['cep'])) ."',
        '".mysqli_real_escape_string($conecta,trim($postjson['endereco'])) ."',
        '".mysqli_real_escape_string($conecta,trim($postjson['bairro'])) ."',
        '".mysqli_real_escape_string($conecta,trim($postjson['cidade'])) ."',
        '".mysqli_real_escape_string($conecta,trim($postjson['uf'])) ."'
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