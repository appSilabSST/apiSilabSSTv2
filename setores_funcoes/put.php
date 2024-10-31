<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id']) && is_numeric($json['id'])) {
            $sql = "
            UPDATE rl_setores_funcoes SET
            cbo = :cbo, 
            id_setor = :id_setor, 
            funcao = :funcao, 
            qtd_funcionarios = :qtd_funcionarios, 
            jornada_trabalho = :jornada_trabalho, 
            descricao = :descricao,
            data_edit = NOW()
            WHERE id_rl_setor_funcao = :id_rl_setor_funcao
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':cbo', trim($json['cbo']), trim($json['cbo']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_setor', trim($json['id_setor']));
            $stmt->bindParam(':funcao', trim($json['funcao']));
            $stmt->bindParam(':qtd_funcionarios', trim($json['qtd_funcionarios']));
            $stmt->bindParam(':jornada_trabalho', trim($json['jornada_trabalho']));
            $stmt->bindParam(':descricao', trim($json['descricao']));
            $stmt->bindParam(':id_rl_setor_funcao', trim($json['id']));
            $stmt->execute();
            
            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Função atualizada com sucesso!'
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
