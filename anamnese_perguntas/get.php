<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        // SELECIONAR UM AGENDAMENTO ESPECÍFICO
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_anamnese_pergunta = trim($_GET["id"]);
            $sql = "
                SELECT * FROM anamnese_perguntas WHERE id_anamnese_pergunta = :id_anamnese_pergunta
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_anamnese_pergunta', $id_anamnese_pergunta);
        } else if (isset($_GET["id_anamnese"]) && is_numeric($_GET["id_anamnese"])) {
            $id_anamnese = trim($_GET["id_anamnese"]);
            $sql = "
                    SELECT * FROM anamnese_perguntas WHERE id_anamnese = :id_anamnese
                ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_anamnese', $id_anamnese);
        } else {
            $sql = "
                SELECT * FROM anamnese_perguntas
            ";
            $stmt = $conn->prepare($sql);
        }
        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        if ($stmt->rowCount() < 1) {
            $result = array(
                'status' => 'fail',
                'result' => 'Nenhum Pergunta foi encontrado'
            );
        } elseif ($stmt->rowCount() == 1 && isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $dados = $stmt->fetch(PDO::FETCH_OBJ);
            $result = array(
                'status' => 'success',
                'result' => $dados
            );
        } else {
            $dados = $stmt->fetchAll(PDO::FETCH_OBJ);
            $result = array(
                'status' => 'success',
                'result' => $dados
            );
        }
    } catch (\Throwable $th) {
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
} else {
    http_response_code(403);
    echo json_encode(
        array(
            'status' => 'fail',
            'result' => 'Sem autorização para acessar este conteúdo!'
        )
    );
}
exit;
