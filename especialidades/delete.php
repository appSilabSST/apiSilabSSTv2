<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $sql = "
            DELETE FROM especialidades
            WHERE id_especialidade = :id_especialidade
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_especialidade', trim($_GET['id']));
            $stmt->execute();

            http_response_code(200);
            $result = 'Exame atualizado com sucesso!';
        } else {
            http_response_code(400);
            $result = 'Dados incompletos!';
        }
    } catch (\Throwable $th) {
        http_response_code(500);
        $result =  $th->getCode();
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
