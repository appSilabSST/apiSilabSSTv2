<?php
// Check if access is authorized
if ($authorization) {
    try {
        // Prepare SQL based on the presence of an ID
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_rl_anamneses_exames = trim($_GET["id"]);
            $sql = "SELECT * FROM rl_anamneses_exames JOIN anamneses ON anamneses.id_anamnese = rl_anamneses_exames.id_anamnese JOIN exames ON exames.id_exame = rl_anamneses_exames.id_exame WHERE rl_anamneses_exames.id_rl_anamneses_exames = :id_rl_anamneses_exames";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_anamneses_exames', $id_rl_anamneses_exames);
        } else if (isset($_GET["id_anamnese"]) && is_numeric($_GET["id_anamnese"])) {
            $id_anamnese = trim($_GET["id_anamnese"]);
            $sql = "SELECT * FROM rl_anamneses_exames JOIN anamneses ON anamneses.id_anamnese = rl_anamneses_exames.id_anamnese JOIN exames ON exames.id_exame = rl_anamneses_exames.id_exame WHERE rl_anamneses_exames.id_anamnese = :id_anamnese";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_anamnese', $id_anamnese);
        } else {
            $sql = "SELECT * FROM rl_anamneses_exames JOIN anamneses ON anamneses.id_anamnese = rl_anamneses_exames.id_anamnese JOIN exames ON exames.id_exame = rl_anamneses_exames.id_exame";
            $stmt = $conn->prepare($sql);
        }

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
} else {
    http_response_code(403);
    echo json_encode([
        'status' => 'fail',
        'result' => 'Sem autorização para acessar este conteúdo!'
    ]);
}
exit;
