<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (empty($_GET['id'])) {
            //Está vazio ou não é númerico: ERRO
            throw new ErrorException("Valor inválido", 1);
        }

        $id_rl_formulario_componente =  $_GET['id'];

        $delete = "DELETE FROM rl_formularios_componentes  WHERE id_rl_formulario_componente = :id_rl_formulario_componente";

        $stmt = $conn->prepare($delete);

        $stmt->bindValue("id_rl_formulario_componente", $id_rl_formulario_componente);

        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            // Se houver um erro ao executar a declaração SQL
            $errorInfo = $stmt->errorInfo();
            $error = $errorInfo[2]; // Mensagem de erro específica
            $result = array("status" => "fail", "error" => $error);
            http_response_code(500); // Internal Server Error
            exit;
        }
        $result = [
            'status' => 'success',
            'result' => 'Success'
        ];
    } catch (\Throwable $th) {
        http_response_code(200);
        $result = array(
            "status" => "fail",
            "error" => $th->getMessage()
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
