<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_especialidade = trim($_GET["id"]);
            $sql = "
            SELECT * FROM especialidades WHERE id_especialidade = :id_especialidade
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_especialidade', $id_especialidade);
        } else {
            $sql = "
            SELECT * FROM especialidades
            ";
            $stmt = $conn->prepare($sql);
        }

        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        $result = getResult($stmt);
    } catch (\Throwable $th) {
        http_response_code(502);
        $result = $th->getMessage();
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
