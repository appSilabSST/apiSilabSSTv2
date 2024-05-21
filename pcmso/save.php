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
  $data_inicio = mysqli_real_escape_string($conecta, trim($form['data_inicio']) . "-01");
  $data_fim = mysqli_real_escape_string($conecta, trim($form['data_fim']) . "-01");
  $relatorio_analitico = mysqli_real_escape_string($conecta, trim($form['relatorio_analitico']));
  $consideracoes_finais = mysqli_real_escape_string($conecta, trim($form['consideracoes_finais']));
  $corpo_documento = mysqli_real_escape_string($conecta, trim($form['corpo_documento']));

  // VERIFICA SE É PRA ATUALIZAR O CORPO DO DOCUMENTO
  if (!empty($corpo_documento)) {
    $sql = "
    UPDATE pcmso SET
    corpo_documento = '$corpo_documento'
    WHERE id_pcmso = $id
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

  // SE STATUS = FINALIZADO, VERIFICA SE HÁ SETOR SEM EXAME NOS GHE's
  if ($id_status_documento == 2) {

    $sql = "
    SELECT s.id_setor
    FROM setores s
    LEFT JOIN rl_setores_exames rl ON s.id_setor = rl.id_setor 
    WHERE s.id_local_atividade = $id_local_atividade
    AND rl.id_rl_setor_exame IS NULL 
    ";

    $query = mysqli_query($conecta, $sql);
    if (mysqli_num_rows($query) > 0) {
      echo json_encode(array(
        'success' => false,
        'result' => 'Existem SETORES que não há informações de Exames'
      ));
      exit;
    }
  }

  if ($id > 0) {

    $sql = "
        UPDATE pcmso SET
        id_status_documento = '$id_status_documento',
        id_profissional = '$id_profissional',
        grau_risco_empresa = '$grau_risco_empresa',
        grau_risco_local_atividade = '$grau_risco_local_atividade',
        data_inicio = '$data_inicio',
        data_fim = '$data_fim',
        responsavel = '$responsavel',
        responsavel_email = '$responsavel_email',
        responsavel_cpf = '$responsavel_cpf',
        relatorio_analitico = '$relatorio_analitico',
        consideracoes_finais = '$consideracoes_finais'
        WHERE id_pcmso = $id
        ";
  } else {

    // Realizar a numeracao do pcmso
    $rs = mysqli_query($conecta, "select IFNULL(max(nr_pcmso),0) as valor from pcmso");
    if ($row = mysqli_fetch_object($rs)) {
      $max = $row->valor;
      if (($max - (intval(gmdate("y")) * 100000)) >= 0) {
        $nr_pcmso = $max + 1;
      } else {
        $nr_pcmso = intval(gmdate("y")) * 100000 + 1;
      }
    }

    $sql = "
      INSERT INTO pcmso (nr_pcmso, id_profissional, data_inicio, data_fim, id_empresa, id_local_atividade, grau_risco_empresa, grau_risco_local_atividade, responsavel, responsavel_cpf, responsavel_email, relatorio_analitico, consideracoes_finais) VALUES
      (
        '$nr_pcmso',
        '$id_profissional',
        '$data_inicio',
        '$data_fim',
        '$id_empresa',
        '$id_local_atividade',
        '$grau_risco_empresa',
        '$grau_risco_local_atividade',
        '$responsavel',
        '$responsavel_cpf',
        '$responsavel_email',
        '$relatorio_analitico',
        '$consideracoes_finais'
      );
      ";
    // echo $sql;exit;
    $query = mysqli_query($conecta, $sql);
    $id_pcmso = mysqli_insert_id($conecta);

    $sql = "
    INSERT INTO revisoes (id_pcmso, data, revisao) VALUES 
    ($id_pcmso, CURDATE(), 'Emissão original')
    ";
    $query = mysqli_query($conecta, $sql);

    // INSERIR EXAMES LINKADOS AOS RISCOS PADRONIZADOS
    $sql = "
      INSERT INTO rl_setores_exames (id_pcmso , id_setor , id_exame , periodicidade , admissional , periodico , retorno_trabalho , mudanca_risco , demissional) 
      SELECT DISTINCT $id_pcmso , rl_se.id_setor ,rl_re.id_exame , rl_re.periodicidade , rl_re.admissional , rl_re.periodico , rl_re.mudanca_risco , rl_re.retorno_trabalho , rl_re.demissional
      FROM rl_setores_riscos rl_se
      JOIN setores s ON (rl_se.id_setor = s.id_setor)
      JOIN rl_riscos_exames rl_re ON (rl_se.id_risco = rl_re.id_risco)
      WHERE s.id_local_atividade = '$id_local_atividade'
      AND s.ativo = 1
      
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
