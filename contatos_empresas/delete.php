<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $sql = "
            DELETE FROM contatos_empresas
            WHERE id_contato_empresa = :id_contato_empresa
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_contato_empresa', trim($_GET['id']));
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Contato da empresa removido com sucesso!'
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
        // VÍNCULOS EM OUTRAS TABELAS
        if ($th->getCode() == 23000) {
            $result = array(
                'status' => 'fail',
                'result' => 'Não é possível remover o contato da empresa, pois há vínculos em outras tabelas'
            );
        } else {
            $result = array(
                'status' => 'fail',
                'result' => $th->getMessage(),
                'code' => $th->getCode()
            );
        }
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
