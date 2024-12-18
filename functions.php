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
