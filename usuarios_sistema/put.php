<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id_usuario_sistema']) && is_numeric($json['id_usuario_sistema'])) {

            // Caso o tipo da classe seja 1, altere todos os outros cnae da empresa classe 2
            if ($json['classe'] == 1 && $json['id_empresa'] > 0) {
                $sql = "UPDATE usuario_sistema SET classe = 2 WHERE id_empresa = :id_empresa";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id_empresa', $json['id_empresa']);
                $stmt->execute();
            }

            $sql = "
            UPDATE usuario_sistema SET
            id_cnae = :id_cnae,
            classe = :classe
            WHERE id_usuario_sistema = :id_usuario_sistema
            ";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_cnae', trim($json['id_cnae']), PDO::PARAM_INT);
            $stmt->bindParam(':classe', trim($json['classe']), PDO::PARAM_INT);
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
