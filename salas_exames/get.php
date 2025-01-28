<?php
// Check if access is authorized
if ($authorization) {
    try {
        // Prepare SQL based on the presence of an ID
        if (isset($_GET["id_sala_atendimento"]) && is_numeric($_GET["id_sala_atendimento"])) {
            $id_sala_atendimento = trim($_GET["id_sala_atendimento"]);
            $sql = "SELECT * FROM rl_salas_exames JOIN exames ON exames.id_exame = rl_salas_exames.id_exame WHERE rl_salas_exames.id_sala_atendimento = :id_sala_atendimento";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_sala_atendimento', $id_sala_atendimento);
        } else {
            $sql = "SELECT * FROM rl_salas_exames JOIN exames ON exames.id_exame = rl_salas_exames.id_exame";
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
