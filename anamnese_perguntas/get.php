<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        // SELECIONAR UM AGENDAMENTO ESPECÍFICO
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_anamnese_pergunta = trim($_GET["id"]);
            $sql = "
                SELECT * 
                FROM anamnese_perguntas 
                WHERE id_anamnese_pergunta = :id_anamnese_pergunta AND ativo = 1
                ORDER BY  ordem
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_anamnese_pergunta', $id_anamnese_pergunta);
        } else if (isset($_GET["id_anamnese"]) && is_numeric($_GET["id_anamnese"])) {
            $id_anamnese = trim($_GET["id_anamnese"]);
            $sql = "
                    SELECT * 
                    FROM anamnese_perguntas 
                    WHERE id_anamnese = :id_anamnese AND ativo = 1
                    ORDER BY  ordem
                ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_anamnese', $id_anamnese);
        } else {
            $sql = "
                SELECT * 
                FROM anamnese_perguntas 
                WHERE ativo = 1
                ORDER BY  ordem
            ";
            $stmt = $conn->prepare($sql);
        }
        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        $result = getResult($stmt);
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
