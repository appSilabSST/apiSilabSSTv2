<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_POST['id_usuario_sistema'])) {

            $sql = "
                    UPDATE usuario_sistema SET
                    avatar = :avatar
                    WHERE id_usuario_sistema = :id_usuario_sistema
                ";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':avatar', trim($avatar));
            $stmt->bindParam(':id_usuario_sistema', trim($json['id_usuario_sistema']));
            $stmt->execute();

            $result = array(
                'status' => 'success',
                'result' => 'Cnae atualizado com sucesso!'
            );
        } else {
            http_response_code(400);
            $result = array(
                'status' => 'fail',
                'result' => 'Dados incompletos!'
            );
        }
    } catch (\Throwable $th) {
        http_response_code(500);
        // DADOS ÚNICOS JÁ UTILIZADOS
        if ($th->getCode() == 23000) {
            $result = array(
                'status' => 'fail',
                'result' => 'Cnae já existente nesta proposta!'
            );
        } else {
            $result = array(
                'status' => 'fail',
                'result' => $th->getMessage()
            );
        }
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
