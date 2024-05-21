<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];

// SALVAR OU EDITAR EMPRESA
if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

  // RECUPERANDO INFORMAÇÕES
  $id_profissional = trim($form["id_profissional"]);
  $id_setor = trim($form["id_setor"]);
  $ausencia_risco = trim($form["ausencia_risco"]);
  $agente_nocivo = trim($form["agente_nocivo"]);
  $classificacao_agente = trim($form["classificacao_agente"]);
  $tipo_avaliacao = trim($form["tipo_avaliacao"]);
  $intensidade = trim($form["intensidade"]);
  $id_unidade_medida = trim($form["id_unidade_medida"]);
  $limite_tolerancia = trim($form["limite_tolerancia"]);
  $id_tipo_exposicao = trim($form["id_tipo_exposicao"]);
  $tecnica_medicao = trim($form["tecnica_medicao"]);
  $fonte_geradora = trim($form["fonte_geradora"]);
  $id_meio_propagacao = trim($form["id_meio_propagacao"]);
  $medidas_controle = trim($form["medidas_controle"]);
  $severidade = trim($form["severidade"]);
  $probabilidade = trim($form["probabilidade"]);
  $id_classificacao_risco = trim($form["id_classificacao_risco"]);
  $epc_utiliza = trim($form["epc_utiliza"]);
  $epc_eficaz = trim($form["epc_eficaz"]);
  $epi_utiliza = trim($form["epi_utiliza"]);
  $epi_eficaz = trim($form["epi_eficaz"]);
  $epi_medProtecao = trim($form["epi_medProtecao"]);
  $epi_condFuncto = trim($form["epi_condFuncto"]);
  $epi_usoInint = trim($form["epi_usoInint"]);
  $epi_przValid = trim($form["epi_przValid"]);
  $epi_periodicTroca = trim($form["epi_periodicTroca"]);
  $epi_higienizacao = trim($form["epi_higienizacao"]);
  $codigo_gfip = trim($form["codigo_gfip"]);
  $insalubridade = trim($form["insalubridade"]);
  $periculosidade = trim($form["periculosidade"]);

  // VERIFICA SE NÃO HÁ AUSÊNCIA DE RISCO CADASTRADO NESTE SETOR
  if ($id > 0) {
    $where = "
    AND id_rl_setor_risco <> $id
    ";
  } else {
    $where = "";
  }

  $sql = "
  SELECT id_rl_setor_risco
  FROM rl_setores_riscos
  WHERE ativo = 1
  AND id_risco = 92
  $where
  ";
  // echo $sql;
  // exit;

  $query = mysqli_query($conecta, $sql);
  if (mysqli_num_rows($query) > 0) {
    $result = json_encode(array(
      'success' => false,
      'result' => 'Este setor está registrado como [09.01.001] - AUSÊNCIA DE RISCO'
    ));

    echo $result;
    exit;
  }

  // SE AUSÊNCIA DE RISCO
  if ($ausencia_risco == true) {
    $id_risco = 92;
  } else {
    $id_risco = "
    (
      SELECT id_risco 
      FROM riscos 
      WHERE ativo = 1 
      AND (
        descricao LIKE '" . mysqli_real_escape_string($conecta, $agente_nocivo) . "'
        OR CONCAT(descricao, ' | eSocial: ', cod) LIKE '" . mysqli_real_escape_string($conecta, $agente_nocivo) . "'
      ) 
      LIMIT 1
    )
    ";
  }

  // SE TIVER ID - EDITAR CADASTRO EXISTENTE
  if ($id > 0) {

    $sql = "
      UPDATE rl_setores_riscos SET
      id_profissional = '" . mysqli_real_escape_string($conecta, $id_profissional) . "',
      id_setor = '" . mysqli_real_escape_string($conecta, $id_setor) . "',
      id_risco = $id_risco,
      classificacao_agente = '" . mysqli_real_escape_string($conecta, $classificacao_agente) . "',
      tipo_avaliacao = '" . mysqli_real_escape_string($conecta, $tipo_avaliacao) . "',
      intensidade = '" . mysqli_real_escape_string($conecta, $intensidade) . "',
      limite_tolerancia = '" . mysqli_real_escape_string($conecta, $limite_tolerancia) . "',
      id_unidade_medida = '" . mysqli_real_escape_string($conecta, $id_unidade_medida) . "',
      id_tipo_exposicao = '" . mysqli_real_escape_string($conecta, $id_tipo_exposicao) . "',
      tecnica_medicao = '" . mysqli_real_escape_string($conecta, $tecnica_medicao) . "',
      fonte_geradora = '" . mysqli_real_escape_string($conecta, $fonte_geradora) . "',
      id_meio_propagacao = '" . mysqli_real_escape_string($conecta, $id_meio_propagacao) . "',
      medidas_controle = '" . mysqli_real_escape_string($conecta, $medidas_controle) . "',
      severidade = '" . mysqli_real_escape_string($conecta, $severidade) . "',
      probabilidade = '" . mysqli_real_escape_string($conecta, $probabilidade) . "',
      id_classificacao_risco = '" . mysqli_real_escape_string($conecta, $id_classificacao_risco) . "',
      epc_utiliza = '" . mysqli_real_escape_string($conecta, $epc_utiliza) . "',
      epc_eficaz = '" . mysqli_real_escape_string($conecta, $epc_eficaz) . "',
      epi_utiliza = '" . mysqli_real_escape_string($conecta, $epi_utiliza) . "',
      epi_eficaz = '" . mysqli_real_escape_string($conecta, $epi_eficaz) . "',
      epi_medProtecao = '" . mysqli_real_escape_string($conecta, $epi_medProtecao) . "',
      epi_condFuncto = '" . mysqli_real_escape_string($conecta, $epi_condFuncto) . "',
      epi_usoInint = '" . mysqli_real_escape_string($conecta, $epi_usoInint) . "',
      epi_przValid = '" . mysqli_real_escape_string($conecta, $epi_przValid) . "',
      epi_periodicTroca = '" . mysqli_real_escape_string($conecta, $epi_periodicTroca) . "',
      epi_higienizacao = '" . mysqli_real_escape_string($conecta, $epi_higienizacao) . "',
      codigo_gfip = '" . mysqli_real_escape_string($conecta, $codigo_gfip) . "',
      insalubridade = '" . mysqli_real_escape_string($conecta, $insalubridade) . "',
      periculosidade = '" . mysqli_real_escape_string($conecta, $periculosidade) . "',
      data_edit = NOW()
      WHERE id_rl_setor_risco = " . mysqli_real_escape_string($conecta, $id);
  }

  // SE ID VAZIO - INSERIR NOVO REGISTRO
  else {

    $sql = "
      INSERT INTO rl_setores_riscos (id_profissional, id_setor, id_risco, classificacao_agente, tipo_avaliacao, intensidade, limite_tolerancia, id_unidade_medida, id_tipo_exposicao, fonte_geradora, tecnica_medicao, id_meio_propagacao, medidas_controle, severidade, probabilidade, id_classificacao_risco, epc_utiliza, epc_eficaz, epi_utiliza, epi_eficaz, epi_medProtecao, epi_condFuncto, epi_usoInint, epi_przValid, epi_periodicTroca, epi_higienizacao, codigo_gfip, insalubridade, periculosidade) VALUES
      (
        '" . mysqli_real_escape_string($conecta, $id_profissional) . "',
        '" . mysqli_real_escape_string($conecta, $id_setor) . "',
        $id_risco,
        '" . mysqli_real_escape_string($conecta, $classificacao_agente) . "',
        '" . mysqli_real_escape_string($conecta, $tipo_avaliacao) . "',
        '" . mysqli_real_escape_string($conecta, $intensidade) . "',
        '" . mysqli_real_escape_string($conecta, $limite_tolerancia) . "',
        '" . mysqli_real_escape_string($conecta, $id_unidade_medida) . "',
        '" . mysqli_real_escape_string($conecta, $id_tipo_exposicao) . "',
        '" . mysqli_real_escape_string($conecta, $fonte_geradora) . "',
        '" . mysqli_real_escape_string($conecta, $tecnica_medicao) . "',
        '" . mysqli_real_escape_string($conecta, $id_meio_propagacao) . "',
        '" . mysqli_real_escape_string($conecta, $medidas_controle) . "',
        '" . mysqli_real_escape_string($conecta, $severidade) . "',
        '" . mysqli_real_escape_string($conecta, $probabilidade) . "',
        '" . mysqli_real_escape_string($conecta, $id_classificacao_risco) . "',
        '" . mysqli_real_escape_string($conecta, $epc_utiliza) . "',
        '" . mysqli_real_escape_string($conecta, $epc_eficaz) . "',
        '" . mysqli_real_escape_string($conecta, $epi_utiliza) . "',
        '" . mysqli_real_escape_string($conecta, $epi_eficaz) . "',
        '" . mysqli_real_escape_string($conecta, $epi_medProtecao) . "',
        '" . mysqli_real_escape_string($conecta, $epi_condFuncto) . "',
        '" . mysqli_real_escape_string($conecta, $epi_usoInint) . "',
        '" . mysqli_real_escape_string($conecta, $epi_przValid) . "',
        '" . mysqli_real_escape_string($conecta, $epi_periodicTroca) . "',
        '" . mysqli_real_escape_string($conecta, $epi_higienizacao) . "',
        '" . mysqli_real_escape_string($conecta, $codigo_gfip) . "',
        '" . mysqli_real_escape_string($conecta, $insalubridade) . "',
        '" . mysqli_real_escape_string($conecta, $periculosidade) . "'
      )
      ";
  }

  // echo $sql;
  // exit;
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
