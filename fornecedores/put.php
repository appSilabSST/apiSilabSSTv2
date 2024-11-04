<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id']) && is_numeric($json['id']) &&
            isset($json['epi']) && isset($json['grupo'])
        ) {
            $sql = "
            UPDATE epis SET
            epi = :epi,
            grupo = :grupo
            WHERE id_epi = :id_epi
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':epi', trim($json['epi']));
            $stmt->bindParam(':grupo', trim($json['grupo']));
            $stmt->bindParam(':id_epi', trim($json['id']));
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'EPI atualizado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao atualizar o EPI!'
                );
            }
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
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
