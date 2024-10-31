<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id']) && is_numeric($json['id'])) {
            $sql = "
            UPDATE rl_colaboradores_empresas SET
            id_empresa = :id_empresa,
            id_rl_setor_funcao = :id_rl_setor_funcao,
            id_colaborador = :id_colaborador,
            data_admissao = :data_admissao,
            matricula = :matricula,
            status = :status
            WHERE id_rl_colaborador_empresa = :id_rl_colaborador_empresa
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_empresa', trim($json['id_empresa']));
            $stmt->bindParam(':id_rl_setor_funcao', trim($json['id_rl_setor_funcao']));
            $stmt->bindParam(':id_colaborador', trim($json['id_colaborador']));
            $stmt->bindParam(':data_admissao', trim($json['data_admissao']), trim($json['data_admissao']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':matricula', trim($json['matricula']), trim($json['matricula']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':status', trim($json['status']));
            $stmt->bindParam(':id_rl_colaborador_empresa', trim($json['id']));
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Vínculo atualizado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao atualizar o vínculo!'
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
