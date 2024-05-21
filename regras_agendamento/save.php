<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];

// SALVAR OU EDITAR 
if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

    // RECUPERANDO INFORMAÇÕES
    $id_dia_semana = trim($form["id_dia_semana"]);
    $data_inicio = trim($form["data_inicio"]);
    $data_fim = trim($form["data_fim"]);
    $horario_inicio = trim($form["horario_inicio"]);
    $horario_fim = trim($form["horario_fim"]);
    $intervalo = trim($form["intervalo"]);
    $qtde = trim($form["qtde"]);

    // SE TIVER ID - EDITAR CADASTRO EXISTENTE
    if ($id > 0) {

        // VERIFICAR SE ALGO FOI MODIFICADO PARA ATUALIZAR HORÁRIOS DISPONÍVEIS
        $sql = "
        SELECT id_regra_agendamento
        FROM regras_agendamento
        WHERE id_dia_semana = '" . mysqli_real_escape_string($conecta, $id_dia_semana) . "'
        AND data_inicio = '" . mysqli_real_escape_string($conecta, $data_inicio) . "'
        AND data_fim = '" . mysqli_real_escape_string($conecta, $data_fim) . "'
        AND horario_inicio = '" . mysqli_real_escape_string($conecta, $horario_inicio) . "'
        AND horario_fim = '" . mysqli_real_escape_string($conecta, $horario_fim) . "'
        AND intervalo = '" . mysqli_real_escape_string($conecta, $intervalo) . "'
        AND qtde = '" . mysqli_real_escape_string($conecta, $qtde) . "'
        AND id_regra_agendamento = " . mysqli_real_escape_string($conecta, $id) . "
        AND ativo = 1
        ";

        $query = mysqli_query($conecta, $sql);

        if (mysqli_num_rows($query) == 0) {
            $sql = "
            DELETE FROM agendamentos 
            WHERE data >= CURDATE()
            AND id_regra_agendamento = '" . mysqli_real_escape_string($conecta, $id) . "'
            AND nr_agendamento = 0
            ";
            // echo $sql;exit;
            mysqli_query($conecta, $sql);

            $insert = "";
            $nova_data = $data_inicio;

            // VERIFICA SE A DATA INÍCIO É COMPATÍVEL COM O DIA DE SEMANA SELECIONADO
            while (date('w', strtotime($nova_data)) + 1 <> $id_dia_semana) {
                $nova_data = date('Y-m-d', strtotime("+ 1 day", strtotime($nova_data)));
            }

            // echo $nova_data;exit;
            while (strtotime($nova_data) <= strtotime($data_fim)) {

                $novo_horario = date('H:i', strtotime($horario_inicio));

                while (strtotime($novo_horario) <= strtotime($horario_fim)) {

                    // CRIA HORÁRIOS DE AGENDA VIRTUALMENTE
                    for ($i = 0; $i < $qtde; $i++) {
                        $insert .= "('$nova_data','$novo_horario','$id'),";
                    }

                    $novo_horario = date('H:i', strtotime("+ $intervalo minutes", strtotime($novo_horario)));
                }

                $nova_data = date('Y-m-d', strtotime("+ 1 week", strtotime($nova_data)));
            }

            $sql = "
            INSERT INTO agendamentos (data, horario, id_regra_agendamento) VALUES
            " . substr($insert, 0, -1) . "
            ";

            // echo $sql0;exit;
            mysqli_query($conecta, $sql);

            // VERIFICAR SE JÁ EXISTIA ALGUM AGENDAMENTO FEITO EM ALGUM HORÁRIO ESPECIFICADO ACIMA
            $sql = "
            SELECT * FROM (
                SELECT data, horario, COUNT(id_agendamento) - $qtde limite
                FROM agendamentos
                WHERE id_regra_agendamento = " . mysqli_real_escape_string($conecta, $id) . "
                GROUP BY data, horario
            ) AS r
            WHERE limite > 0
            ";

            $query = mysqli_query($conecta, $sql);
            if (mysqli_num_rows($query) > 0) {
                $sql = "";
                while ($row = mysqli_fetch_object($query)) {
                    $sql .= "
                    DELETE FROM agendamentos
                    WHERE data = '$row->data'
                    AND horario = '$row->horario'
                    AND nr_agendamento = 0
                    LIMIT $row->limite ; 
                    ";
                }
                // echo $sql;exit;
                mysqli_multi_query($conecta, $sql);
                mysqli_close($conecta);
                include('../conexao.php');
            }
        }

        $sql = "
        UPDATE regras_agendamento SET
        id_dia_semana = '" . mysqli_real_escape_string($conecta, $id_dia_semana) . "',
        data_inicio = '" . mysqli_real_escape_string($conecta, $data_inicio) . "',
        data_fim = '" . mysqli_real_escape_string($conecta, $data_fim) . "',
        horario_inicio = '" . mysqli_real_escape_string($conecta, $horario_inicio) . "',
        horario_fim = '" . mysqli_real_escape_string($conecta, $horario_fim) . "',
        intervalo = '" . mysqli_real_escape_string($conecta, $intervalo) . "',
        qtde = '" . mysqli_real_escape_string($conecta, $qtde) . "'
        WHERE id_regra_agendamento = " . mysqli_real_escape_string($conecta, $id) . "
        AND ativo = 1
        ";
    } else {

        $sql = "
        INSERT INTO regras_agendamento (id_dia_semana, data_inicio, data_fim, horario_inicio, horario_fim, intervalo, qtde) VALUES
        (
            '" . mysqli_real_escape_string($conecta, $id_dia_semana) . "',
            '" . mysqli_real_escape_string($conecta, $data_inicio) . "',
            '" . mysqli_real_escape_string($conecta, $data_fim) . "',
            '" . mysqli_real_escape_string($conecta, $horario_inicio) . "',
            '" . mysqli_real_escape_string($conecta, $horario_fim) . "',
            '" . mysqli_real_escape_string($conecta, $intervalo) . "',
            '" . mysqli_real_escape_string($conecta, $qtde) . "'
        )
        ";

        $query = mysqli_query($conecta, $sql);
        $id = mysqli_insert_id($conecta);

        $insert = "";
        $nova_data = $data_inicio;

        // VERIFICA SE A DATA INÍCIO É COMPATÍVEL COM O DIA DE SEMANA SELECIONADO
        while (date('w', strtotime($nova_data)) + 1 <> $id_dia_semana) {
            $nova_data = date('Y-m-d', strtotime("+ 1 day", strtotime($nova_data)));
        }

        // echo $nova_data;exit;
        while (strtotime($nova_data) <= strtotime($data_fim)) {

            $novo_horario = date('H:i', strtotime($horario_inicio));

            while (strtotime($novo_horario) <= strtotime($horario_fim)) {

                // CRIA HORÁRIOS DE AGENDA VIRTUALMENTE
                for ($i = 0; $i < $qtde; $i++) {
                    $insert .= "('$nova_data','$novo_horario','$id'),";
                }

                $novo_horario = date('H:i', strtotime("+ $intervalo minutes", strtotime($novo_horario)));
            }

            $nova_data = date('Y-m-d', strtotime("+ 1 week", strtotime($nova_data)));
        }

        $sql = "
            INSERT INTO agendamentos (data, horario, id_regra_agendamento) VALUES
            " . substr($insert, 0, -1) . "
            ";

        // echo $sql0;exit;
        // mysqli_query($conecta, $sql);
    }

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
