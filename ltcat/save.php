<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];

// SALVAR OU EDITAR EMPRESA
if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

  $id_status_documento = mysqli_real_escape_string($conecta, trim($form['id_status_documento']));
  $id_profissional = mysqli_real_escape_string($conecta, trim($form['id_profissional']));
  $id_empresa = mysqli_real_escape_string($conecta, trim($form['id_empresa']));
  $grau_risco_empresa = mysqli_real_escape_string($conecta, trim($form['grau_risco_empresa']));
  $id_local_atividade = mysqli_real_escape_string($conecta, trim($form['id_local_atividade']));
  $grau_risco_local_atividade = mysqli_real_escape_string($conecta, trim($form['grau_risco_local_atividade']));
  $responsavel = mysqli_real_escape_string($conecta, trim($form['responsavel']));
  $responsavel_cpf = mysqli_real_escape_string($conecta, trim($form['responsavel_cpf']));
  $responsavel_email = mysqli_real_escape_string($conecta, trim($form['responsavel_email']));
  $data_inicio = mysqli_real_escape_string($conecta, trim($form['data_inicio']));
  $consideracoes_finais = mysqli_real_escape_string($conecta, trim($form['consideracoes_finais']));
  $corpo_documento = trim($form['corpo_documento']);

  // VERIFICA SE É PRA ATUALIZAR O CORPO DO DOCUMENTO
  if (!empty($corpo_documento)) {
    $sql = "
    UPDATE ltcat SET
    corpo_documento = '" . mysqli_real_escape_string($conecta, $corpo_documento) . "'
    WHERE id_ltcat = $id
    ";
    $query = mysqli_query($conecta, $sql);

    if ($query) {

      $result = json_encode(array(
        'success' => true,
        'result' => 'Registro salvo com sucesso.',
        'id' => $id
      ));
    } else {

      $result = json_encode(array(
        'success' => false,
        'result' => 'Falha ao tentar salvar registro.'
      ));
    }

    echo $result;
    exit;
  }

  // SE STATUS = FINALIZADO, VERIFICA SE HÁ SETOR SEM RISCO OU AUSÊNCIA DE RISCO CADASTRADO NOS GHE's
  if ($id_status_documento == 2) {

    $sql = "
    SELECT s.id_setor
    FROM setores s
    LEFT JOIN rl_setores_riscos rl ON s.id_setor = rl.id_setor 
    WHERE s.id_local_atividade = $id_local_atividade
    AND rl.id_rl_setor_risco IS NULL 
    ";

    $query = mysqli_query($conecta, $sql);
    if (mysqli_num_rows($query) > 0) {
      echo json_encode(array(
        'success' => false,
        'result' => 'Existem SETORES que não há informações de Riscos'
      ));
      exit;
    }
  }

  if ($id > 0) {

    $sql = "
        UPDATE ltcat SET
        id_status_documento = '$id_status_documento',
        id_profissional = '$id_profissional',
        grau_risco_empresa = '$grau_risco_empresa',
        grau_risco_local_atividade = '$grau_risco_local_atividade',
        data_inicio = '$data_inicio',
        responsavel = '$responsavel',
        responsavel_cpf = '$responsavel_cpf',
        responsavel_email = '$responsavel_email',
        consideracoes_finais = '$consideracoes_finais'
        WHERE id_ltcat = $id
        ";
  } else {

    // Realizar a numeracao do documento
    $rs = mysqli_query($conecta, "select IFNULL(max(nr_ltcat),0) as valor from ltcat");
    if ($row = mysqli_fetch_object($rs)) {
      $max = $row->valor;
      if (($max - (intval(gmdate("y")) * 100000)) >= 0) {
        $nr_ltcat = $max + 1;
      } else {
        $nr_ltcat = intval(gmdate("y")) * 100000 + 1;
      }
    }

    $sql = "
      INSERT INTO ltcat (nr_ltcat, id_profissional, data_inicio, id_empresa, id_local_atividade, grau_risco_empresa, grau_risco_local_atividade, responsavel, responsavel_cpf, responsavel_email, consideracoes_finais) VALUES
      (
        '$nr_ltcat',
        '$id_profissional',
        '$data_inicio',
        '$id_empresa',
        '$id_local_atividade',
        '$grau_risco_empresa',
        '$grau_risco_local_atividade',
        '$responsavel',
        '$responsavel_cpf',
        '$responsavel_email',
        '$consideracoes_finais'
      )
      ";
  }

  // echo $sql; exit;
  $query  = mysqli_query($conecta, $sql);
  $id = mysqli_insert_id($conecta);

  if ($query) {

    $result = json_encode(array(
      'success' => true,
      'result' => 'Registro salvo com sucesso.',
      'id' => $id
    ));
  } else {

    $result = json_encode(array(
      'success' => false,
      'result' => 'Falha ao tentar salvar registro.'
    ));
  }

  echo $result;
}
