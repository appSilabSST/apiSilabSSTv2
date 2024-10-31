<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_tipo_atendimento = trim($_GET["id"]);
            $sql = "
            SELECT id_tipo_atendimento, cod_esocial, tipo_atendimento
            FROM tipos_atendimento
            WHERE ativo = 1
            AND id_tipo_atendimento = :id_tipo_atendimento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_tipo_atendimento', $id_tipo_atendimento);
        } else {
            $sql = "
            SELECT id_tipo_atendimento, cod_esocial, tipo_atendimento
            FROM tipos_atendimento
            WHERE ativo = 1
            ORDER BY tipo_atendimento
            ";
            $stmt = $conn->prepare($sql);
        }

        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        if ($stmt->rowCount() < 1) {
            $result = array(
                'status' => 'fail',
                'result' => 'Nenhum tipo de atendimento foi encontrado'
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
        http_response_code(500);
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
