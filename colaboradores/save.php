<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];

  // SALVAR OU EDITAR COLABORADOR
if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

  if ($id > 0) {

    $sql = "
    UPDATE colaboradores SET
    nome = '" . mysqli_real_escape_string($conecta, trim($form["nome"])) . "',
    celular = '" . mysqli_real_escape_string($conecta, trim($form["celular"])) . "',
    email = '" . mysqli_real_escape_string($conecta, trim($form["email"])) . "',
    cpf = '" . mysqli_real_escape_string($conecta, trim($form["cpf"])) . "',
    rg = '" . mysqli_real_escape_string($conecta, trim($form["rg"])) . "',
    deficiente = '" . mysqli_real_escape_string($conecta, trim($form["deficiente"])) . "',
    data_nascimento = '" . mysqli_real_escape_string($conecta, trim($form["data_nascimento"])) . "',
    sexo = '" . mysqli_real_escape_string($conecta, trim($form["sexo"])) . "'
    WHERE id_colaborador = " . mysqli_real_escape_string($conecta,$id);

  } else {

    $sql = "
    INSERT INTO colaboradores (nome,celular,email,cpf,rg,deficiente,data_nascimento,sexo) VALUES 
      (
        '" . mysqli_real_escape_string($conecta, trim($form['nome'])) . "', 
        '" . mysqli_real_escape_string($conecta, trim($form['celular'])) . "', 
        '" . mysqli_real_escape_string($conecta, trim($form['email'])) . "', 
        '" . mysqli_real_escape_string($conecta, trim($form['cpf'])) . "', 
        '" . mysqli_real_escape_string($conecta, trim($form['rg'])) . "', 
        '" . mysqli_real_escape_string($conecta, trim($form['deficiente'])) . "',
        '" . mysqli_real_escape_string($conecta, trim($form['data_nascimento'])) . "',
        '" . mysqli_real_escape_string($conecta, trim($form['sexo'])) . "'
      )
    ";

  }
  
  // echo $sql;exit;
  $query  = mysqli_query($conecta, $sql);

  if ($query) {

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
