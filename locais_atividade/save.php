<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];

if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

  if ($id > 0) {

    $sql = "
      UPDATE locais_atividade SET
      id_empresa = '" . mysqli_real_escape_string($conecta, trim($form["id_empresa"])) . "',
      razao_social = '" . mysqli_real_escape_string($conecta, trim($form["razao_social"])) . "',
      id_tipo_ambiente = '" . mysqli_real_escape_string($conecta, trim($form["id_tipo_ambiente"])) . "',
      tipo_inscricao = '" . mysqli_real_escape_string($conecta, trim($form["tipo_inscricao"])) . "',
      nr_inscricao = '" . mysqli_real_escape_string($conecta, trim($form["nr_inscricao"])) . "',
      cnae = '" . mysqli_real_escape_string($conecta, trim($form["cnae"])) . "',
      atividade = '" . mysqli_real_escape_string($conecta, trim($form["atividade"])) . "',
      grau_risco = '" . mysqli_real_escape_string($conecta, trim($form["grau_risco"])) . "',
      cep = '" . mysqli_real_escape_string($conecta, trim($form["cep"])) . "',
      logradouro = '" . mysqli_real_escape_string($conecta, trim($form["logradouro"])) . "',
      numero = '" . mysqli_real_escape_string($conecta, trim($form["numero"])) . "',
      complemento = '" . mysqli_real_escape_string($conecta, trim($form["complemento"])) . "',
      bairro = '" . mysqli_real_escape_string($conecta, trim($form["bairro"])) . "',
      cidade = '" . mysqli_real_escape_string($conecta, trim($form["cidade"])) . "',
      uf = '" . mysqli_real_escape_string($conecta, trim($form["uf"])) . "',
      atividade_principal = '" . mysqli_real_escape_string($conecta, trim($form["atividade_principal"])) . "'
      WHERE id_local_atividade = " . mysqli_real_escape_string($conecta, $id);
  } else {

    $sql = "
      INSERT INTO locais_atividade 
      (id_empresa, razao_social, id_tipo_ambiente, tipo_inscricao, nr_inscricao, cnae, atividade, grau_risco, cep, logradouro, numero, complemento, bairro, cidade, uf, atividade_principal) 
      VALUES 
      (
        '" . mysqli_real_escape_string($conecta, trim($form['id_empresa'])) . "',
        '" . mysqli_real_escape_string($conecta, trim($form["razao_social"])) . "', 
        '" . mysqli_real_escape_string($conecta, trim($form['id_tipo_ambiente'])) . "', 
        '" . mysqli_real_escape_string($conecta, trim($form['tipo_inscricao'])) . "', 
        '" . mysqli_real_escape_string($conecta, trim($form['nr_inscricao'])) . "', 
        '" . mysqli_real_escape_string($conecta, trim($form['cnae'])) . "', 
        '" . mysqli_real_escape_string($conecta, trim($form['atividade'])) . "', 
        '" . mysqli_real_escape_string($conecta, trim($form['grau_risco'])) . "', 
        '" . mysqli_real_escape_string($conecta, trim($form['cep'])) . "',
        '" . mysqli_real_escape_string($conecta, trim($form['logradouro'])) . "',
        '" . mysqli_real_escape_string($conecta, trim($form['numero'])) . "',
        '" . mysqli_real_escape_string($conecta, trim($form['complemento'])) . "',
        '" . mysqli_real_escape_string($conecta, trim($form['bairro'])) . "',
        '" . mysqli_real_escape_string($conecta, trim($form['cidade'])) . "',
        '" . mysqli_real_escape_string($conecta, trim($form['uf'])) . "',
        '" . mysqli_real_escape_string($conecta, trim($form['atividade_principal'])) . "'
      )
      ";
  }

  //echo $sql;
  $query  = mysqli_query($conecta, $sql);

  if ($query) {

    $result = json_encode(array(
      'success' => true,
      'result' => 'Registro salvo com sucesso'
    ));
  } else {

    $result = json_encode(array(
      'success' => false,
      'result' => 'Falha ao tentar editar registro'
    ));
  }

  echo $result;
}
