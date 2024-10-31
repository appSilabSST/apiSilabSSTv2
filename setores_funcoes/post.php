<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_setor']) && is_numeric($json['id_setor']) &&
            isset($json['funcao']) &&
            isset($json['qtd_funcionarios']) &&
            isset($json['jornada_trabalho']) &&
            isset($json['descricao'])
        ) {

            $sql = "
            INSERT INTO rl_setores_funcoes (cbo, id_setor, funcao, qtd_funcionarios, jornada_trabalho, descricao) VALUES
            (:cbo, :id_setor, :funcao, :qtd_funcionarios, :jornada_trabalho, :descricao)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':cbo', trim($json['cbo']), trim($json['cbo']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_setor', trim($json['id_setor']));
            $stmt->bindParam(':funcao', trim($json['funcao']));
            $stmt->bindParam(':qtd_funcionarios', trim($json['qtd_funcionarios']));
            $stmt->bindParam(':jornada_trabalho', trim($json['jornada_trabalho']));
            $stmt->bindParam(':descricao', trim($json['descricao']));
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Função cadastrada com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao cadastrar função!'
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
