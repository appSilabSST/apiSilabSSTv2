<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_exame = trim($_GET["id"]);
            $sql = "
            SELECT e.id_exame, e.procedimento, e.valor_custo, e.valor_cobrar, e.cod_esocial , e.validade, e.padronizar,
            f.razao_social, f.id_fornecedor
            FROM exames e 
            LEFT JOIN fornecedores f on f.id_fornecedor = e.id_fornecedor             
            WHERE e.ativo = 1
            AND e.id_exame = :id_exame
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_exame', $id_exame);
        } else {
            $sql = "
            SELECT e.id_exame, e.procedimento, e.valor_custo, e.valor_cobrar, e.cod_esocial , e.validade, e.padronizar,
            f.razao_social, f.id_fornecedor,f.nr_doc,f.id_tipo_orgao
            FROM exames e 
            LEFT JOIN fornecedores f on (f.id_fornecedor = e.id_fornecedor)             
            WHERE e.ativo = 1
            ORDER BY e.procedimento
            ";
            $stmt = $conn->prepare($sql);
        }

        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        $result = getResult($stmt);
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
