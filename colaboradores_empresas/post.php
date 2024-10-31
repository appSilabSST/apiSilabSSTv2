<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id_empresa']) && is_numeric($json['id_empresa']) && isset($json['id_colaborador']) && is_numeric($json['id_colaborador'])) {

            // VERIFICA SE JÁ NÃO EXISTE VÍNCULO ATIVO DO COLABORADOR COM A EMPRESA
            if ($json['status'] == 1) {
                $sql = "
                SELECT id_rl_colaborador_empresa
                FROM rl_colaboradores_empresas
                WHERE id_empresa = :id_empresa
                AND id_colaborador = :id_colaborador
                AND status = 1
                ";

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id_empresa', trim($json['id_empresa']));
                $stmt->bindParam(':id_colaborador', trim($json['id_colaborador']));
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $result = json_encode(array(
                        'status' => 'fail',
                        'result' => 'Este colaborador já está ATIVO nesta empresa!'
                    ));
                    echo $result;
                    exit;
                }
            }

            $sql = "
            INSERT INTO rl_colaboradores_empresas (id_empresa, id_rl_setor_funcao, id_colaborador, data_admissao, matricula, status) VALUES 
            (:id_empresa, :id_rl_setor_funcao, :id_colaborador, :data_admissao, :matricula, :status)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_empresa', trim($json['id_empresa']));
            $stmt->bindParam(':id_rl_setor_funcao', trim($json['id_rl_setor_funcao']), trim($json['id_rl_setor_funcao']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':id_colaborador', trim($json['id_colaborador']));
            $stmt->bindParam(':data_admissao', trim($json['data_admissao']), trim($json['data_admissao']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':matricula', trim($json['matricula']), trim($json['matricula']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':status', trim($json['status']));
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Colaborador vinculado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao vincular o colaborador!'
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
