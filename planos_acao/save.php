<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];

// SALVAR OU EDITAR EMPRESA
if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

  // RECUPERANDO INFORMAÇÕES
  $agente_nocivo = trim($form["agente_nocivo"]);
  $padronizar = trim($form["padronizar"]);
  $plano_acao = trim($form["plano_acao"]);
  $descricao = trim($form["descricao"]);

  // VERIFICA SE PLANO DE AÇÃO JÁ NÃO ESTÁ VINCULADO AO RISCO
  if ($id > 0) {
    $where = "
    AND id_plano_acao <> $id
    ";
  } else {
    $where = "";
  }

  $sql = "
  SELECT id_plano_acao
  FROM planos_acao
  WHERE id_risco = (
    SELECT id_risco 
    FROM riscos 
    WHERE ativo = 1 
    AND (
      descricao LIKE '" . mysqli_real_escape_string($conecta, $agente_nocivo) . "'
      OR CONCAT(descricao, ' | eSocial: ', cod) LIKE '" . mysqli_real_escape_string($conecta, $agente_nocivo) . "'
    ) 
    LIMIT 1
  )
  $where
  ";
  // echo $sql;exit;
  $query = mysqli_query($conecta, $sql);

  if (mysqli_num_rows($query) > 0) {
    $result = json_encode(array(
      'success' => false,
      'result' => 'Este vínculo de dados já existe.'
    ));
    echo $result;
    exit;
  }

  // SE TIVER ID - EDITAR CADASTRO EXISTENTE
  if ($id > 0 || $id_rl_setor_risco_plano_acao > 0) {

    $sql = "
      UPDATE planos_acao SET
      id_risco = (
        SELECT id_risco 
        FROM riscos 
        WHERE ativo = 1 
        AND (
          descricao LIKE '" . mysqli_real_escape_string($conecta, $agente_nocivo) . "'
          OR CONCAT(descricao, ' | eSocial: ', cod) LIKE '" . mysqli_real_escape_string($conecta, $agente_nocivo) . "'
        ) 
        LIMIT 1
        ),
      padronizar = '" . mysqli_real_escape_string($conecta, $padronizar) . "',
      plano_acao = '" . mysqli_real_escape_string($conecta, $plano_acao) . "',
      descricao = '" . mysqli_real_escape_string($conecta, $descricao) . "'
      WHERE id_plano_acao = '" . mysqli_real_escape_string($conecta, $id) . "'
      ";
  }

  // SE ID VAZIO - INSERIR NOVO REGISTRO
  else {

    $sql = "
      INSERT INTO planos_acao (id_risco, padronizar, plano_acao, descricao) VALUES
      (
        (
          SELECT id_risco 
          FROM riscos 
          WHERE ativo = 1 
          AND (
            descricao LIKE '" . mysqli_real_escape_string($conecta, $agente_nocivo) . "'
            OR CONCAT(descricao, ' | eSocial: ', cod) LIKE '" . mysqli_real_escape_string($conecta, $agente_nocivo) . "'
          ) 
          LIMIT 1
          ),
        '" . mysqli_real_escape_string($conecta, $padronizar) . "',
        '" . mysqli_real_escape_string($conecta, $plano_acao) . "',
        '" . mysqli_real_escape_string($conecta, $descricao) . "'
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
      'result' => 'Falha ao tentar salvar registro'
    ));
  }

  echo $result;
}
