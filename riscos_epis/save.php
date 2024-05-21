<?php

  include_once('../conexao.php');

  $postjson = json_decode(file_get_contents('php://input'), true);

  $id = trim($postjson["id"]);
  $form = $postjson["form"];

  if($postjson['requisicao'] == 'salvar' && !empty($form)){

    $id_rl_setor_risco = trim($form["id_rl_setor_risco"]);
    $id_epi = trim($form["id_epi"]);
    $ca = trim($form["ca"]);

    // VERIFICA SE A OPÇÃO DE EPI_UTILIZA ESTÁ ATIVA
    $sql = "
    SELECT id_rl_setor_risco
    FROM rl_setores_riscos
    WHERE id_rl_setor_risco = $id_rl_setor_risco
    AND epi_utiliza = 2
    ";

    // echo $sql;exit;
    $query = mysqli_query($conecta,$sql);
    if(mysqli_num_rows($query) == 0) {

      echo $result = json_encode(array(
        'success' => false,
        'result' => 'Este RISCO não indica o uso de EPI.'
      ));
      exit;

    }

    if($id > 0) {

      $sql = "
      UPDATE rl_riscos_epis SET
      id_epi = '".mysqli_real_escape_string($conecta,$id_epi) ."',
      ca = '".mysqli_real_escape_string($conecta,$ca) ."'
      WHERE id_rl_risco_epi = $id
      ";

    } else {

      $sql = "
      INSERT INTO rl_riscos_epis (id_rl_setor_risco, id_epi, ca) VALUES 
      (
        '".mysqli_real_escape_string($conecta,$id_rl_setor_risco) ."',
        '".mysqli_real_escape_string($conecta,$id_epi) ."',
        '".mysqli_real_escape_string($conecta,$ca) ."'
      )
      ";

    }

    // echo $sql;exit;
    $query = mysqli_query($conecta,$sql);

    if($query) {

        $result = json_encode(array(
          'success' => true,
          'result' => 'Registro salvo com sucesso.'
        ));

    }else{

        $result = json_encode(array(
          'success' => false,
          'result' => 'Falha ao tentar salvar registro.'
        ));
    }

    echo $result;

  }
