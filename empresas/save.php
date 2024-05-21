<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];

// INATIVAR EMPRESA
if ($postjson['requisicao'] == 'status') {
  if ($id > 0) {
    $sql = "
    UPDATE empresas SET
    status = IF(status = 0, 1, 0)
    WHERE id_empresa = " . mysqli_real_escape_string($conecta, $id) . "
    ";

    $query = mysqli_query($conecta, $sql);

    if($query) {
      $result = json_encode(array(
        'success' => true,
        'result' => 'Empresa atualizada com sucesso.'
      ));
    } else {
      $result = json_encode(array(
        'success' => false,
        'result' => 'Falha ao tentar atualizar a empresa.'
      ));
    }

  } else {
    $result = json_encode(array(
      'success' => false,
      'result' => 'Empresa não encontrada na base de dados.'
    ));
  }

  echo $result;
  exit;

}

// SALVAR OU EDITAR EMPRESA
if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

  // VERIFICA SE JÁ EXISTE EMPRESA ATIVA COM ESSE NR_INSCRICAO
  if ($id > 0) {
    $where = "
    AND id_empresa <> $id
    ";
  } else {
    $where = "";
  }

  $sql = "
  SELECT id_empresa
  FROM empresas
  WHERE nr_inscricao = '$nr_inscricao'
  $where
  AND status = 1
  AND ativo = 1
  ";

  $query = mysqli_query($conecta, $sql);
  if (mysqli_num_rows($query) > 0) {
    $result = json_encode(array(
      'success' => false,
      'result' => 'Já existe uma empresa Ativa com este NÚMERO DE INSCRIÇÃO'
    ));

    echo $result;
    exit;
  }

  // RECUPERANDO INFORMAÇÕES
  $data_cadastro = trim($form["data_cadastro"]);
  $razao_social = trim($form["razao_social"]);
  $nome_fantasia = trim($form["nome_fantasia"]);
  $tipo_inscricao = trim($form["tipo_inscricao"]);
  $nr_inscricao = trim($form["nr_inscricao"]);
  $telefone = trim($form["telefone"]);
  $cnae = substr(trim($form["cnae"]), 0, 5);
  $atividade = trim($form["atividade"]);
  $grau_risco = trim($form["grau_risco"]);
  $cep = trim($form["cep"]);
  $logradouro = trim($form["logradouro"]);
  $numero = trim($form["numero"]);
  $complemento = trim($form["complemento"]);
  $bairro = trim($form["bairro"]);
  $cidade = trim($form["cidade"]);
  $uf = trim($form["uf"]);
  $status = trim($form["status"]);

  // SE TIVER ID - EDITAR CADASTRO EXISTENTE
  if ($id > 0) {

    $sql = "
      UPDATE empresas SET
      razao_social = '" . mysqli_real_escape_string($conecta, $razao_social) . "',
      nome_fantasia = '" . mysqli_real_escape_string($conecta, $nome_fantasia) . "',
      tipo_inscricao = '" . mysqli_real_escape_string($conecta, $tipo_inscricao) . "',
      nr_inscricao = '" . mysqli_real_escape_string($conecta, $nr_inscricao) . "',
      telefone = '" . mysqli_real_escape_string($conecta, $telefone) . "',
      data_cadastro = '" . mysqli_real_escape_string($conecta, $data_cadastro) . "',
      inscricao = '" . mysqli_real_escape_string($conecta, $inscricao) . "',
      cnae = '" . mysqli_real_escape_string($conecta, $cnae) . "',
      atividade = '" . mysqli_real_escape_string($conecta, $atividade) . "',
      grau_risco = '" . mysqli_real_escape_string($conecta, $grau_risco) . "',
      cep = '" . mysqli_real_escape_string($conecta, $cep) . "',
      logradouro = '" . mysqli_real_escape_string($conecta, $logradouro) . "',
      numero = '" . mysqli_real_escape_string($conecta, $numero) . "',
      complemento = '" . mysqli_real_escape_string($conecta, $complemento) . "',
      bairro = '" . mysqli_real_escape_string($conecta, $bairro) . "',
      cidade = '" . mysqli_real_escape_string($conecta, $cidade) . "',
      uf = '" . mysqli_real_escape_string($conecta, $uf) . "',
      status = '" . mysqli_real_escape_string($conecta, $status) . "'
      WHERE id_empresa = " . mysqli_real_escape_string($conecta, $id);

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);
  }

  // SE ID VAZIO - INSERIR NOVO REGISTRO
  else {

    $sql = "
      INSERT INTO empresas (razao_social, nome_fantasia, tipo_inscricao, nr_inscricao, telefone, data_cadastro, cnae, atividade, grau_risco, cep, logradouro, numero, complemento, bairro, cidade, uf, status) VALUES
      (
        '" . mysqli_real_escape_string($conecta, $razao_social) . "',
        '" . mysqli_real_escape_string($conecta, $nome_fantasia) . "',
        '" . mysqli_real_escape_string($conecta, $tipo_inscricao) . "',
        '" . mysqli_real_escape_string($conecta, $nr_inscricao) . "',
        '" . mysqli_real_escape_string($conecta, $telefone) . "',
        '" . mysqli_real_escape_string($conecta, $data_cadastro) . "',
        '" . mysqli_real_escape_string($conecta, $cnae) . "',
        '" . mysqli_real_escape_string($conecta, $atividade) . "',
        '" . mysqli_real_escape_string($conecta, $grau_risco) . "',
        '" . mysqli_real_escape_string($conecta, $cep) . "',
        '" . mysqli_real_escape_string($conecta, $logradouro) . "',
        '" . mysqli_real_escape_string($conecta, $numero) . "',
        '" . mysqli_real_escape_string($conecta, $complemento) . "',
        '" . mysqli_real_escape_string($conecta, $bairro) . "',
        '" . mysqli_real_escape_string($conecta, $cidade) . "',
        '" . mysqli_real_escape_string($conecta, $uf) . "',
        '" . mysqli_real_escape_string($conecta, $status) . "'
      )
      ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);
    $id_empresa = mysqli_insert_id($conecta);


    $sql = "
      INSERT INTO locais_atividade 
      (id_empresa, razao_social, id_tipo_ambiente, tipo_inscricao, nr_inscricao, cnae, atividade, grau_risco, cep, logradouro, numero, complemento, bairro, cidade, uf) 
      VALUES 
      (
        '$id_empresa',
        '" . mysqli_real_escape_string($conecta, $razao_social) . "', 
        '1', 
        '" . mysqli_real_escape_string($conecta, $tipo_inscricao) . "', 
        '" . mysqli_real_escape_string($conecta, $nr_inscricao) . "', 
        '" . mysqli_real_escape_string($conecta, $cnae) . "', 
        '" . mysqli_real_escape_string($conecta, $atividade) . "', 
        '" . mysqli_real_escape_string($conecta, $grau_risco) . "', 
        '" . mysqli_real_escape_string($conecta, $cep) . "',
        '" . mysqli_real_escape_string($conecta, $logradouro) . "',
        '" . mysqli_real_escape_string($conecta, $numero) . "',
        '" . mysqli_real_escape_string($conecta, $complemento) . "',
        '" . mysqli_real_escape_string($conecta, $bairro) . "',
        '" . mysqli_real_escape_string($conecta, $cidade) . "',
        '" . mysqli_real_escape_string($conecta, $uf) . "'
      )
      ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);
  }

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
