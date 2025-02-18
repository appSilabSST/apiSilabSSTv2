<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['revisao']) &&
            (
                (isset($json['id_pcmso']) && is_numeric($json['id_pcmso'])) ||
                (isset($json['id_pgr']) && is_numeric($json['id_pgr'])) ||
                (isset($json['id_ltcat']) && is_numeric($json['id_ltcat'])) ||
                (isset($json['id_proposta']) && is_numeric($json['id_proposta']))
            )
        ) {

            $sql = "
            INSERT INTO revisoes (id_pcmso,id_proposta, id_pgr, id_ltcat, data_inicio, data_fim, revisao, descricao, corpo_documento) VALUES
            (:id_pcmso,:id_proposta, :id_pgr, :id_ltcat, :data_inicio, :data_fim, :revisao, :descricao, :corpo_documento)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id_pcmso', isset($json['id_pcmso']) ? trim($json['id_pcmso']) : null);
            $stmt->bindValue(':id_pgr', isset($json['id_pgr']) ? trim($json['id_pgr']) : null);
            $stmt->bindValue(':id_ltcat', isset($json['id_ltcat']) ? trim($json['id_ltcat']) : null);
            $stmt->bindValue(':id_proposta', isset($json['id_proposta']) ? trim($json['id_proposta']) : null);
            $stmt->bindValue(':data_inicio', isset($json['data_inicio']) ? trim($json['data_inicio']) : date("Y-m-d"));
            $stmt->bindValue(':data_fim', isset($json['data_fim']) ? trim($json['data_fim']) : date("Y-m-d"));
            $stmt->bindValue(':revisao', trim($json['revisao']));
            $stmt->bindValue(':descricao', trim($json['descricao']), trim($json['descricao']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':corpo_documento', trim($json['corpo_documento']), trim($json['corpo_documento']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Revisão cadastrado com sucesso!'
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
