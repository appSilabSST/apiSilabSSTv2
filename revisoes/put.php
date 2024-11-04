<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id']) && is_numeric($json['id']) &&
            isset($json['revisao']) &&
            isset($json['status']) && is_numeric($json['status']) &&
            (
                (isset($json['id_pcmso']) && is_numeric($json['id_pcmso'])) ||
                (isset($json['id_pgr']) && is_numeric($json['id_pgr'])) ||
                (isset($json['id_ltcat']) && is_numeric($json['id_ltcat']))
            )
        ) {
            $sql = "
            UPDATE revisoes SET
            id_pcmso = :id_pcmso, 
            id_pgr = :id_pgr, 
            id_ltcat = :id_ltcat, 
            data_fim = :data_fim, 
            revisao = :revisao, 
            descricao = :descricao, 
            corpo_documento = :corpo_documento, 
            status = :status
            WHERE id_revisao = :id_revisao
            AND status = 0
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id_pcmso', isset($json['id_pcmso']) ? trim($json['id_pcmso']) : null);
            $stmt->bindValue(':id_pgr', isset($json['id_pgr']) ? trim($json['id_pgr']) : null);
            $stmt->bindValue(':id_ltcat', isset($json['id_ltcat']) ? trim($json['id_ltcat']) : null);
            $stmt->bindValue(':data_fim', trim($json['status']) == 1 ? date("Y-m-d") : null);
            $stmt->bindValue(':revisao', trim($json['revisao']));
            $stmt->bindValue(':descricao', trim($json['descricao']), trim($json['descricao']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':corpo_documento', trim($json['corpo_documento']), trim($json['corpo_documento']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':status', trim($json['status']), PDO::PARAM_INT);
            $stmt->bindParam(':id_revisao', trim($json['id']));
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Revisão atualizada com sucesso!'
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
