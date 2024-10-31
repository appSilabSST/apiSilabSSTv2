<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id']) && is_numeric($json['id'])) {

            // VARIÁVEIS PARA VERIFICAR DUPLICIDADE DE CADASTROS ATIVOS
            $status = false;
            $id_rl_colaborador_empresa = 0;

            $sql = "
            UPDATE rl_colaboradores_empresas SET
            ";
            foreach ($json as $key => $value) {
                if ($key != 'id') {
                    $sql .= "$key = :$key,";
                } else {
                    $id_rl_colaborador_empresa = $value;
                }

                // VERIFICAR DUPLICIDADE DE CADASTRO ATIVO
                if ($key == 'status' && $value == 1) {
                    $status = true;
                }
            }
            $sql = substr($sql, 0, -1) . "
            WHERE id_rl_colaborador_empresa = :id_rl_colaborador_empresa
            ";

            if ($status == true && $id_rl_colaborador_empresa > 0) {
                $sql_ = "
                SELECT id_rl_colaborador_empresa
                FROM rl_colaboradores_empresas
                WHERE status = 1
                AND id_empresa = (SELECT id_empresa FROM rl_colaboradores_empresas WHERE id_rl_colaborador_empresa = $id_rl_colaborador_empresa)
                AND id_colaborador = (SELECT id_colaborador FROM rl_colaboradores_empresas WHERE id_rl_colaborador_empresa = $id_rl_colaborador_empresa)
                AND id_rl_colaborador_empresa <> '$id_rl_colaborador_empresa'
                ";
                $stmt = $conn->prepare($sql_);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    http_response_code(500);
                    $result = array(
                        'status' => 'fail',
                        'result' => 'Este colaborador já está ATIVO nesta empresa!'
                    );
                    $conn = null;
                    echo json_encode($result);
                    exit;
                }
            }

            $stmt = $conn->prepare($sql);
            foreach ($json as $key => $value) {
                if ($key != 'id') {
                    $stmt->bindParam(":$key", trim($value), trim($value) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                } else {
                    $stmt->bindValue(":id_rl_colaborador_empresa", $value);
                }
            }
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Colaborador atualizado com sucesso!'
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
        // DADOS ÚNICOS JÁ UTILIZADOS
        if ($th->getCode() == 23000) {
            $result = array(
                'status' => 'fail',
                'result' => 'RG, CPF ou Passaporte já existente!'
            );
        } else {
            $result = array(
                'status' => 'fail',
                'result' => $th->getMessage()
            );
        }
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
