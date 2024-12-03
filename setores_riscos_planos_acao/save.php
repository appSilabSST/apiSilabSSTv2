<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];

// SALVAR OU EDITAR EMPRESA
if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

  // RECUPERANDO INFORMAÇÕES
  $id_rl_setor_risco_plano_acao = trim($form["id_rl_setor_risco_plano_acao"]);
  $id_pgr = trim($form["id_pgr"]);
  $id_rl_setor_risco = trim($form["id_rl_setor_risco"]);
  $plano_acao = trim($form["plano_acao"]);
  $descricao = trim($form["descricao"]);
  $medida_suficiente = trim($form["medida_suficiente"]);
  $data_avaliacao = trim($form["data_avaliacao"]);
  $indicacao_medida = trim($form["indicacao_medida"]);

  // SE TIVER ID - EDITAR CADASTRO EXISTENTE
  if ($id > 0 || $id_rl_setor_risco_plano_acao > 0) {

    $sql = "
      UPDATE rl_setores_riscos_planos_acao SET
      id_rl_setor_risco = '" . mysqli_real_escape_string($conecta, $id_rl_setor_risco) . "',
      plano_acao = '" . mysqli_real_escape_string($conecta, $plano_acao) . "',
      descricao = '" . mysqli_real_escape_string($conecta, $descricao) . "',
      medida_suficiente = '" . mysqli_real_escape_string($conecta, $medida_suficiente) . "',
      data_avaliacao = '" . mysqli_real_escape_string($conecta, $data_avaliacao) . "',
      indicacao_medida = '" . mysqli_real_escape_string($conecta, $indicacao_medida) . "',
      data_edit = NOW()
      WHERE id_rl_setor_risco_plano_acao = '" . mysqli_real_escape_string($conecta, $id) . "'
      OR id_rl_setor_risco_plano_acao = '" . mysqli_real_escape_string($conecta, $id_rl_setor_risco_plano_acao) . "'
      ";
  }

  // SE ID VAZIO - INSERIR NOVO REGISTRO
  else {

    $sql = "
      INSERT INTO rl_setores_riscos_planos_acao (id_pgr, id_rl_setor_risco, plano_acao, descricao, medida_suficiente, data_avaliacao, indicacao_medida) VALUES
      (
        '" . mysqli_real_escape_string($conecta, $id_pgr) . "',
        '" . mysqli_real_escape_string($conecta, $id_rl_setor_risco) . "',
        '" . mysqli_real_escape_string($conecta, $plano_acao) . "',
        '" . mysqli_real_escape_string($conecta, $descricao) . "',
        '" . mysqli_real_escape_string($conecta, $medida_suficiente) . "',
        '" . mysqli_real_escape_string($conecta, $data_avaliacao) . "',
        '" . mysqli_real_escape_string($conecta, $indicacao_medida) . "'
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
