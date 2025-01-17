<?php
// FORMATAR RESPOSTAS DO MÉTODO GET
function getResult($stmt)
{
    if ($stmt->rowCount() < 1) {
        $result = http_response_code(204);
    } elseif (
        $stmt->rowCount() == 1 &&
        (
            isset($_GET["id"]) && is_numeric($_GET["id"]) ||
            isset($_GET["nr_doc"]) && is_numeric($_GET["nr_doc"]) ||
            isset($_GET["codigo"]) && is_numeric($_GET["codigo"]) ||
            isset($_GET["nr_inscricao"]) && is_numeric($_GET["nr_inscricao"]) ||
            isset($_GET["id_agendamento"]) && is_numeric($_GET["id_agendamento"])
        )
    ) {
        $result = $stmt->fetch(PDO::FETCH_OBJ);
    } else {
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    return $result;
}


// Função para validar o formato da data
function isValidDate($date)
{
    $format = 'Y-m-d';
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}


function setupInsetAgendamento($date, $id)
{
    $insert = "";
    $nova_data = trim($date['data_inicio']);
    $data_fim = isset($date['data_fim']) ? trim($date['data_fim']) : date('Y-m-d', strtotime("+ 6 month", strtotime($nova_data)));

    // VERIFICA SE A DATA INÍCIO É COMPATÍVEL COM O DIA DE SEMANA SELECIONADO
    while ((date('w', strtotime($nova_data)) + 1) <> $date['id_dia_semana']) {
        $nova_data = date('Y-m-d', strtotime("+ 1 day", strtotime($nova_data)));
    }

    // echo $nova_data;exit;
    while (strtotime($nova_data) <= strtotime($data_fim)) {

        $novo_horario = date('H:i', strtotime(trim($date['horario_inicio'])));

        while (strtotime($novo_horario) <= strtotime(trim($date['horario_fim']))) {
            // CRIA HORÁRIOS DE AGENDA VIRTUALMENTE
            for ($i = 0; $i < $date['qtde_intervalo']; $i++) {
                $insert .= "('$nova_data','$novo_horario','$id'),";
            }

            $novo_horario = date('H:i', strtotime("+{$date['intervalo']} minutes", strtotime($novo_horario)));
        }

        $nova_data = date('Y-m-d', strtotime("+ 1 week", strtotime($nova_data)));
    }

    return "
             INSERT INTO agendamentos (data, horario, id_regra_agendamento) VALUES
                " . substr($insert, 0, -1) . "
            ";
}
