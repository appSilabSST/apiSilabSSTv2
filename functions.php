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
            isset($_GET["nr_inscricao"]) && is_numeric($_GET["nr_inscricao"])
            // || isset($_GET["id_agendamento"]) && is_numeric($_GET["id_agendamento"])
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


// function setupInsetAgendamento($date, $id)
// {
//     $insert = "";
//     $nova_data = trim($date['data_inicio']);
//     $data_fim = isset($date['data_fim']) ? trim($date['data_fim']) : date('Y-m-d', strtotime("+ 6 month", strtotime($nova_data)));

//     // Lista de feriados (datas sem expediente)
//     $filterDiasSemExpediente = [
//         '2025-05-01',
//         '2025-05-08',
//     ];
//     // Converte para timestamps para facilitar comparação
//     $filterTimestamps = array_map('strtotime', $filterDiasSemExpediente);

//     // VERIFICA SE A DATA INÍCIO É COMPATÍVEL COM O DIA DE SEMANA SELECIONADO
//     while ((date('w', strtotime($nova_data)) + 1) <> $date['id_dia_semana']) {
//         $nova_data = date('Y-m-d', strtotime("+ 1 day", strtotime($nova_data)));
//     }

//     // echo $nova_data;exit;
//     while (strtotime($nova_data) <= strtotime($data_fim)) {

//         // Se for feriado / dia sem expediente, pula para +1 semana
//         if (in_array(strtotime($nova_data), $filterTimestamps, true)) {
//             $nova_data = date('Y-m-d', strtotime("+1 week", $nova_data));
//             continue;
//         }

//         $novo_horario = date('H:i', strtotime(trim($date['horario_inicio'])));

//         while (strtotime($novo_horario) <= strtotime(trim($date['horario_fim']))) {
//             // CRIA HORÁRIOS DE AGENDA VIRTUALMENTE
//             for ($i = 0; $i < $date['qtde_intervalo']; $i++) {
//                 $insert .= "('$nova_data','$novo_horario','$id'),";
//             }

//             $novo_horario = date('H:i', strtotime("+{$date['intervalo']} minutes", strtotime($novo_horario)));
//         }

//         $nova_data = date('Y-m-d', strtotime("+ 1 week", strtotime($nova_data)));
//     }

//     return "
//              INSERT INTO agendamentos (data, horario, id_regra_agendamento) VALUES
//                 " . substr($insert, 0, -1) . "
//             ";
// }


function setupInsetAgendamento($date, $id, $diasSemExpediente)
{
    $insert = "";
    $nova_data = trim($date['data_inicio']);
    $data_fim  = isset($date['data_fim'])  ? trim($date['data_fim']) : date('Y-m-d', strtotime("+6 month", strtotime($nova_data)));

    // Filtra somente os feriados “Sem expediente” (ativo = 1)
    $semExpediente = array_filter($diasSemExpediente, function ($item) {
        return  $item['ativo'] == 1;
    });

    // Extrai apenas o campo 'data' em um array simples
    $filterDiasSemExpediente = array_map(function ($f) {
        return $f['data'];
    }, $semExpediente);

    // Converte para timestamps para facilitar comparação
    $filterTimestamps = array_map('strtotime', $filterDiasSemExpediente);

    // Ajusta data de início para bater com o dia da semana desejado
    while ((date('w', strtotime($nova_data)) + 1) != $date['id_dia_semana']) {
        $nova_data = date('Y-m-d', strtotime("+1 day", strtotime($nova_data)));
    }

    // Laço principal: data atual até data_fim
    while (strtotime($nova_data) <= strtotime($data_fim)) {
        $ts_nova_data = strtotime($nova_data);

        // Se for feriado / dia sem expediente, pula para +1 semana
        if (in_array($ts_nova_data, $filterTimestamps, true)) {
            $nova_data = date('Y-m-d', strtotime("+1 week", $ts_nova_data));
            continue;
        }

        // Gera intervalos de horário para o dia válido
        $novo_horario = date('H:i', strtotime(trim($date['horario_inicio'])));
        $horario_fim  = strtotime(trim($date['horario_fim']));

        while (strtotime($novo_horario) <= $horario_fim) {
            // Repete conforme qtde_intervalo
            for ($i = 0; $i < $date['qtde_intervalo']; $i++) {
                $insert .= "('$nova_data','$novo_horario','$id'),";
            }
            // Avança o intervalo
            $novo_horario = date(
                'H:i',
                strtotime("+{$date['intervalo']} minutes", strtotime($novo_horario))
            );
        }

        // Avança uma semana
        $nova_data = date('Y-m-d', strtotime("+1 week", $ts_nova_data));
    }

    // Monta a query final
    return "
        INSERT INTO agendamentos (data, horario, id_regra_agendamento) VALUES
        " . rtrim($insert, ',') . "
    ";
}


function move_file($file, $cnpj)
{
    // Verifica se um arquivo de anexo foi enviado
    if ($file["error"] != 4) {
        // Obtém o diretório raiz do servidor (sem o public_html)
        $raizServidor = dirname($_SERVER['DOCUMENT_ROOT']); // Volta um nível a partir do public_html

        // Define o caminho completo para o diretório .anexos
        echo   $anexoFolder = $raizServidor . "/.anexos/" . $cnpj . "/" . date("Y") . "/" . date("m") . "/" . date("d") . "/";

        // Cria o diretório, caso não exista
        if (!file_exists($anexoFolder)) {
            mkdir($anexoFolder, 0755, true);
        }

        // Obtém a extensão do arquivo de forma mais segura
        $info = pathinfo($file["name"]);
        $extensao = $info["extension"];

        // Gera um nome único para o arquivo
        $nomeArquivo = sha1($file["tmp_name"] . time() . $cnpj) . "." . $extensao;

        // Move o arquivo para o novo diretório
        if (move_uploaded_file($file["tmp_name"], $anexoFolder . $nomeArquivo)) {
            return  $cnpj . "/" . date("Y") . "/" . date("m") . "/" . date("d") . "/" . $nomeArquivo;
        } else {
            return false; // Se o arquivo não puder ser movido
        }
    } else {
        return false; // Se não houver arquivo enviado
    }
}
