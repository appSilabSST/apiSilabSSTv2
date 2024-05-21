<?php

  include_once('../conexao.php');

  $postjson = json_decode(file_get_contents('php://input'), true);

  $id = trim($postjson["id"]);
  $form = $postjson["form"];

  // SALVAR OU EDITAR CONTATO EMPRESA
  if($postjson['requisicao'] == 'salvar') {

    // RECUPERANDO INFORMAÇÕES
    $id_empresa = trim($form["id_empresa"]);
    $funcao = trim($form["funcao"]);
    $nome = trim($form["nome"]);
    $telefone = trim($form["telefone"]);
    $email = trim($form["email"]);

    // SE TIVER ID - EDITAR CADASTRO EXISTENTE
    if($id > 0) {

      $sql = "
      UPDATE contatos_empresas SET
      funcao = '".mysqli_real_escape_string($conecta,$funcao) ."',
      nome = '".mysqli_real_escape_string($conecta,$nome) ."',
      telefone = '".mysqli_real_escape_string($conecta,$telefone) ."',
      email = '".mysqli_real_escape_string($conecta,$email) ."'
      WHERE id_contato_empresa = " . mysqli_real_escape_string($conecta,$id);

    }

    else {

      $sql = "
      INSERT INTO contatos_empresas 
      (id_empresa, funcao, nome, telefone, email) VALUES 
      (
        '".mysqli_real_escape_string($conecta,$id_empresa) ."',
        '".mysqli_real_escape_string($conecta,$funcao) ."',
        '".mysqli_real_escape_string($conecta,$nome) ."',
        '".mysqli_real_escape_string($conecta,$telefone) ."',
        '".mysqli_real_escape_string($conecta,$email) ."'
      )
      ";

    }

    // echo $sql;exit;
    $query  = mysqli_query($conecta,$sql);

    if($query){

      $result = json_encode(array(
        'success' => true,
        'result' => 'Registro salvo com sucesso.'
      ));

    } else {

      $result = json_encode(array(
        'success' => false,
        'result' => 'Falha ao tentar editar registro'
      ));

    }

    echo $result;

  }