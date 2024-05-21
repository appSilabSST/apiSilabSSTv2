<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];

// SALVAR OU EDITAR EMPRESA
if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

  // RECUPERANDO INFORMAÇÕES
  $id_setor = trim($form["id_setor"]);
  $id_pcmso = trim($form["id_pcmso"]);
  $procedimento_format = trim($form["procedimento_format"]);
  $periodicidade = trim($form["periodicidade"]);
  $tipos_avaliacao = $form["tipos_avaliacao"];

  $admissional = 0;
  $periodico = 0;
  $monitoracao_pontual = 0;
  $mudanca_risco = 0;
  $retorno_trabalho = 0;
  $demissional = 0;

  foreach ($tipos_avaliacao as $key => $value) {
    if ($value == 'admissional') {
      $admissional = 1;
    } elseif ($value == 'periodico') {
      $periodico = 1;
    } elseif ($value == 'monitoracao_pontual') {
      $monitoracao_pontual = 1;
    } elseif ($value == 'mudanca_risco') {
      $mudanca_risco = 1;
    } elseif ($value == 'retorno_trabalho') {
      $retorno_trabalho = 1;
    } elseif ($value == 'demissional') {
      $demissional = 1;
    }
  }

  if ($id > 0) {
    $where = "
    AND id_rl_setor_exame <> $id
    ";
  } else {
    $where = "";
  }

  // VERIFICA SE EXAME JÁ NÃO ESTÁ VINCULADO AO SETOR
  $sql = "
  SELECT id_rl_setor_exame
  FROM rl_setores_exames
  WHERE id_setor = '" . mysqli_real_escape_string($conecta, $id_setor) . "'
  AND id_pcmso = '" . mysqli_real_escape_string($conecta, $id_pcmso) . "'
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
  if ($id > 0) {

    $sql = "
      UPDATE rl_setores_exames SET
      id_setor = '" . mysqli_real_escape_string($conecta, $id_setor) . "',
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
      periodicidade = '" . mysqli_real_escape_string($conecta, $periodicidade) . "',
      admissional = '" . mysqli_real_escape_string($conecta, $admissional) . "',
      periodico = '" . mysqli_real_escape_string($conecta, $periodico) . "',
      monitoracao_pontual = '" . mysqli_real_escape_string($conecta, $monitoracao_pontual) . "',
      mudanca_risco = '" . mysqli_real_escape_string($conecta, $mudanca_risco) . "',
      retorno_trabalho = '" . mysqli_real_escape_string($conecta, $retorno_trabalho) . "',
      demissional = '" . mysqli_real_escape_string($conecta, $demissional) . "',
      data_edit = NOW()
      WHERE id_rl_setor_exame = " . mysqli_real_escape_string($conecta, $id);
  }

  // SE ID VAZIO - INSERIR NOVO REGISTRO
  else {

    $sql = "
      INSERT INTO rl_setores_exames (id_setor, id_pcmso, id_exame, periodicidade, admissional, periodico, monitoracao_pontual,mudanca_risco, retorno_trabalho, demissional) VALUES
      (
        '" . mysqli_real_escape_string($conecta, $id_setor) . "',
        '" . mysqli_real_escape_string($conecta, $id_pcmso) . "',
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
        '" . mysqli_real_escape_string($conecta, $periodicidade) . "',
        '" . mysqli_real_escape_string($conecta, $admissional) . "',
        '" . mysqli_real_escape_string($conecta, $periodico) . "',
        '" . mysqli_real_escape_string($conecta, $monitoracao_pontual) . "',
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
