<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_local_atividade']) && is_numeric($json['id_local_atividade']) &&
            isset($json['setor'])
        ) {

            $sql = "
            INSERT INTO setores (id_local_atividade, setor, descricao, status) VALUES
            (:id_local_atividade, :setor, :descricao, :ausencia_risco, :status)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_local_atividade', trim($json['id_local_atividade']));
            $stmt->bindParam(':setor', trim($json['setor']));
            $stmt->bindParam(':descricao', trim($json['descricao']));
            $stmt->bindParam(':ausencia_risco', trim($json['ausencia_risco']));
            $stmt->bindParam(':status', trim($json['status']));
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Setor cadastrado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao cadastrar setor!'
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
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
