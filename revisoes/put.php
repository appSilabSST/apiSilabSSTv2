<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_revisao']) && is_numeric($json['id_revisao']) &&
            isset($json['revisao']) && isset($json['descricao']) &&
            (
                (isset($json['id_pcmso']) && is_numeric($json['id_pcmso'])) ||
                (isset($json['id_pgr']) && is_numeric($json['id_pgr'])) ||
                (isset($json['id_ltcat']) && is_numeric($json['id_ltcat'])) ||
                (isset($json['id_proposta']) && is_numeric($json['id_proposta']))
            )
        ) {
            $sql = "
            UPDATE revisoes SET
            id_pcmso = :id_pcmso, 
            id_pgr = :id_pgr, 
            id_ltcat = :id_ltcat, 
            id_proposta = :id_proposta,
            revisao = :revisao, 
            descricao = :descricao
            WHERE id_revisao = :id_revisao
            AND status = 0
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id_pcmso', isset($json['id_pcmso']) ? trim($json['id_pcmso']) : null);
            $stmt->bindValue(':id_pgr', isset($json['id_pgr']) ? trim($json['id_pgr']) : null);
            $stmt->bindValue(':id_ltcat', isset($json['id_ltcat']) ? trim($json['id_ltcat']) : null);
            $stmt->bindValue(':id_proposta', isset($json['id_proposta']) ? trim($json['id_proposta']) : null);
            $stmt->bindValue(':revisao', trim($json['revisao']));
            $stmt->bindValue(':descricao', trim($json['descricao']), trim($json['descricao']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_revisao', trim($json['id_revisao']));
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
