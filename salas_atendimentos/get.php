<?php
// Check if access is authorized
if ($authorization) {
    try {
        // Prepare SQL based on the presence of an ID
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_sala_atendimento = trim($_GET["id"]);
            $sql = "SELECT * FROM salas_atendimentos WHERE id_sala_atendimento = :id_sala_atendimento";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_sala_atendimento', $id_sala_atendimento);
        } else {
            $sql = "SELECT * FROM salas_atendimentos";
            $stmt = $conn->prepare($sql);
        }

        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        $result = getResult($stmt);
    } catch (\Throwable $th) {
        $result = [
            'status' => 'fail',
            'result' => $th->getMessage()
        ];
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
