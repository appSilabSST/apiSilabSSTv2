<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        // SELECIONAR UM AGENDAMENTO ESPECÍFICO
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_anamnese = trim($_GET["id"]);
            $sql = "
                SELECT * FROM anamneses WHERE id_anamnese = :id_anamnese
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_anamnese', $id_anamnese);
        } else {
            $sql = "
                SELECT * FROM anamneses WHERE ativo = '1'
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
}
