<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id']) && is_numeric($json['id'])) {
            $sql = "
            UPDATE setores SET
            id_local_atividade = :id_local_atividade, 
            setor = :setor, 
            descricao = :descricao, 
            conclusao = :conclusao, 
            ausencia_risco = :ausencia_risco, 
            status = :status, 
            data_edit = NOW()
            WHERE id_setor = :id_setor
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_local_atividade', trim($json['id_local_atividade']));
            $stmt->bindParam(':setor', trim($json['setor']));
            $stmt->bindParam(':descricao', trim($json['descricao']));
            $stmt->bindParam(':ausencia_risco', trim($json['ausencia_risco']));
            $stmt->bindParam(':conclusao', trim($json['conclusao']));
            $stmt->bindParam(':status', trim($json['status']));
            $stmt->bindParam(':id_setor', trim($json['id']));
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Setor atualizado com sucesso!'
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
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
