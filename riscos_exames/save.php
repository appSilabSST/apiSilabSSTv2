<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];

// SALVAR OU EDITAR EMPRESA
if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

  // RECUPERANDO INFORMAÇÕES
  $id_risco = trim($form["id_risco"]);
  $procedimento_format = trim($form["procedimento_format"]);
  $periodicidade = trim($form["periodicidade"]);
  $tipos_avaliacao = $form["tipos_avaliacao"];
  $padronizar = trim($form["padronizar"]);

  $admissional = 0;
  $periodico = 0;
  $mudanca_risco = 0;
  $retorno_trabalho = 0;
  $demissional = 0;

  foreach ($tipos_avaliacao as $key => $value) {
    if ($value == 'admissional') {
      $admissional = 1;
    } elseif ($value == 'periodico') {
      $periodico = 1;
    } elseif ($value == 'mudanca_risco') {
      $mudanca_risco = 1;
    } elseif ($value == 'retorno_trabalho') {
      $retorno_trabalho = 1;
    } elseif ($value == 'demissional') {
      $demissional = 1;
    }
  }

  // VERIFICA SE EXAME JÁ NÃO ESTÁ VINCULADO AO RISCO
  if($id > 0) {
    $where = "
    AND id_rl_risco_exame <> $id
    ";
  } else {
    $where = "";
  }

  $sql = "
  SELECT id_rl_risco_exame
  FROM rl_riscos_exames
  WHERE id_risco = '" . mysqli_real_escape_string($conecta, $id_risco) . "'
  AND id_exame = (
    SELECT id_exame 
    FROM exames
    WHERE ativo = 1 
    AND (
      procedimento LIKE '" . mysqli_real_escape_string($conecta, $procedimento_format) . "'
      OR CONCAT_WS(' | eSocial: ', procedimento , cod) LIKE '" . mysqli_real_escape_string($conecta, $procedimento_format) . "'
    ) 
    LIMIT 1
  )
  $where
  ";
  // echo $sql;exit;
  $query = mysqli_query($conecta,$sql);

  if(mysqli_num_rows($query) > 0) {
    $result = json_encode(array(
      'success' => false,
      'result' => 'Este vínculo de dados já existe.'
    ));
    echo $result;
    exit;
  }

  // SE TIVER ID - EDITAR CADASTRO EXISTENTE
  if ($id > 0) {

    $sql = "
      UPDATE rl_riscos_exames SET
      id_risco = '" . mysqli_real_escape_string($conecta, $id_risco) . "',
      id_exame = (
        SELECT id_exame 
        FROM exames
        WHERE ativo = 1 
        AND (
          procedimento LIKE '" . mysqli_real_escape_string($conecta, $procedimento_format) . "'
          OR CONCAT_WS(' | eSocial: ', procedimento , cod) LIKE '" . mysqli_real_escape_string($conecta, $procedimento_format) . "'
        ) 
        LIMIT 1
      ),
      padronizar = '" . mysqli_real_escape_string($conecta, $padronizar) . "',
      periodicidade = '" . mysqli_real_escape_string($conecta, $periodicidade) . "',
      admissional = '" . mysqli_real_escape_string($conecta, $admissional) . "',
      periodico = '" . mysqli_real_escape_string($conecta, $periodico) . "',
      mudanca_risco = '" . mysqli_real_escape_string($conecta, $mudanca_risco) . "',
      retorno_trabalho = '" . mysqli_real_escape_string($conecta, $retorno_trabalho) . "',
      demissional = '" . mysqli_real_escape_string($conecta, $demissional) . "'
      WHERE id_rl_risco_exame = " . mysqli_real_escape_string($conecta, $id);
  }

  // SE ID VAZIO - INSERIR NOVO REGISTRO
  else {

    $sql = "
      INSERT INTO rl_riscos_exames (id_risco, id_exame, padronizar, periodicidade, admissional, periodico, mudanca_risco, retorno_trabalho, demissional) VALUES
      (
        '" . mysqli_real_escape_string($conecta, $id_risco) . "',
        (
          SELECT id_exame 
          FROM exames
          WHERE ativo = 1 
          AND (
            procedimento LIKE '" . mysqli_real_escape_string($conecta, $procedimento_format) . "'
            OR CONCAT_WS(' | eSocial: ', procedimento , cod) LIKE '" . mysqli_real_escape_string($conecta, $procedimento_format) . "'
          ) 
          LIMIT 1
        ),
        '" . mysqli_real_escape_string($conecta, $padronizar) . "',
        '" . mysqli_real_escape_string($conecta, $periodicidade) . "',
        '" . mysqli_real_escape_string($conecta, $admissional) . "',
        '" . mysqli_real_escape_string($conecta, $periodico) . "',
        '" . mysqli_real_escape_string($conecta, $mudanca_risco) . "',
        '" . mysqli_real_escape_string($conecta, $retorno_trabalho) . "',
        '" . mysqli_real_escape_string($conecta, $demissional) . "'
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
