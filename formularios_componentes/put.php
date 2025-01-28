<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {

    // Obtém e limpa os parâmetros de entrada
    $id_rl_formulario_componente = $json['id_rl_formulario_componente'];
    $obrigatorio = $json['obrigatorio'];
    $id_componente = $json['tipo'];
    $label = $json['label'];
    $options = $json['options'];
    $ordem = $json["ordem"];
    $linha = $json["linha"];
    $tamanho = $json["tamanho"];

    try {

        if (isset($id_rl_formulario_componente) && isset($id_componente) && isset($label)) {
            // Prepara a declaração SQL de atualização
            $update = "
                UPDATE 
                    rl_formularios_componentes 
                SET 
                    id_componente = :id_componente,
                    label = :label,
                    ordem = :ordem,
                    linha = :linha,
                    tamanho = :tamanho
                WHERE 
                    id_rl_formulario_componente = :id_rl_formulario_componente
            ";

            $stmt = $conn->prepare($update);

            // Liga os parâmetros
            $stmt->bindParam(':id_componente', $id_componente);
            $stmt->bindParam(':label', $label);
            $stmt->bindParam(':ordem', $ordem);
            $stmt->bindParam(':linha', $linha);
            $stmt->bindParam(':tamanho', $tamanho);
            $stmt->bindParam(':id_rl_formulario_componente', $id_rl_formulario_componente);

            $stmt->execute();

            $delete = "DELETE FROM rl_componentes_opcoes WHERE rl_componentes_opcoes.id_rl_formulario_componente = :id_rl_formulario_componente";

            $stmt = $conn->prepare($delete);

            $stmt->bindValue("id_rl_formulario_componente", $id_rl_formulario_componente);

            $stmt->execute();

            if ($options) {

                // DEFINE ORDEM
                $i = 0;
                // Inserir na tabela usuario
                $insert_rl_componentes_opcoes = "INSERT INTO rl_componentes_opcoes (id_rl_formulario_componente,opcao, ordem) VALUES (:id_rl_formulario_componente, :opcao, :ordem)";

                $stmt = $conn->prepare($insert_rl_componentes_opcoes);

                foreach ($options as $option) {

                    $i++;
                    $stmt->bindParam(':id_rl_formulario_componente',  $id_rl_formulario_componente);
                    $stmt->bindParam(':opcao', $option);
                    $stmt->bindParam(':ordem', $i);
                    $stmt->execute();

                    if ($stmt->rowCount() == 0) {
                        // Se houver um erro ao executar a declaração SQL
                        $errorInfo = $stmt->errorInfo();
                        $error = $errorInfo[2]; // Mensagem de erro específica
                        $result = array("status" => "fail", "error" => $error);
                        http_response_code(500); // Internal Server Error
                        exit;
                    }
                }
            }

            $result = [
                'status' => 'success',
                'result' => 'Pgta atualizado com success'
            ];
        } else if (isset($id_rl_formulario_componente) && isset($obrigatorio)) {
            // Prepara a declaração SQL de atualização
            $update = "
                UPDATE 
                    rl_formularios_componentes 
                SET 
                    obrigatorio = :obrigatorio 
                WHERE 
                    id_rl_formulario_componente = :id_rl_formulario_componente
            ";

            $stmt = $conn->prepare($update);

            // Liga os parâmetros
            $stmt->bindParam(':obrigatorio', $obrigatorio);
            $stmt->bindParam(':id_rl_formulario_componente', $id_rl_formulario_componente);
            $stmt->execute();
        }
    } catch (\Throwable $th) {
        http_response_code(500);
        // DADOS ÚNICOS JÁ UTILIZADOS
        if ($th->getCode() == 23000) {
            $result = array(
                'status' => 'fail',
                'result' => 'Colaborador já existente nesta agenda!'
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
} else {
    http_response_code(403);
    echo json_encode(
        array(
            'status' => 'fail',
            'result' => 'Sem autorização para acessar este conteúdo!'
        )
    );
}
exit;
