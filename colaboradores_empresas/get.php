<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_rl_colaborador_empresa = trim($_GET["id"]);
            $sql = "
            SELECT c.*, 
                IF(LENGTH(nr_doc) = 11, INSERT( INSERT( INSERT( nr_doc, 10, 0, '-' ), 7, 0, '.' ), 4, 0, '.' ), null) nr_doc_format,
			    IF(LENGTH(rg) = 9, INSERT( INSERT( INSERT( rg, 9, 0, '-' ), 6, 0, '.' ), 3, 0, '.' ), 
			    IF(LENGTH(rg) = 8, INSERT( INSERT( rg, 6, 0, '.' ), 3, 0, '.' ), null)) rg_format,
            rl.id_rl_colaborador_empresa,rl.data_admissao,DATE_FORMAT(rl.data_admissao,'%d/%m/%Y') data_admissao_mask,rl.matricula,rl.status,
            e.id_empresa, e.tipo_inscricao, e.nr_inscricao , e.razao_social
            FROM colaboradores c
            JOIN rl_colaboradores_empresas rl ON (rl.id_colaborador = c.id_colaborador AND rl.ativo = '1')
            JOIN empresas e ON (rl.id_empresa = e.id_empresa)
            WHERE c.ativo = 1
            AND rl.id_rl_colaborador_empresa = :id_rl_colaborador_empresa
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_colaborador_empresa', $id_rl_colaborador_empresa);
        } elseif (isset($_GET["nr_doc"])) {
            $nr_doc = trim($_GET["nr_doc"]);
            $sql = "
            SELECT c.*, 
                IF(LENGTH(nr_doc) = 11, INSERT( INSERT( INSERT( nr_doc, 10, 0, '-' ), 7, 0, '.' ), 4, 0, '.' ), null) nr_doc_format,
			    IF(LENGTH(rg) = 9, INSERT( INSERT( INSERT( rg, 9, 0, '-' ), 6, 0, '.' ), 3, 0, '.' ), 
			    IF(LENGTH(rg) = 8, INSERT( INSERT( rg, 6, 0, '.' ), 3, 0, '.' ), null)) rg_format,
            rl.id_rl_colaborador_empresa,rl.data_admissao,DATE_FORMAT(rl.data_admissao,'%d/%m/%Y') data_admissao_mask,rl.matricula,rl.status,
            e.id_empresa, e.tipo_inscricao, e.nr_inscricao , e.razao_social
            FROM colaboradores c
            JOIN rl_colaboradores_empresas rl ON (rl.id_colaborador = c.id_colaborador AND rl.ativo = '1')
            JOIN empresas e ON (rl.id_empresa = e.id_empresa)
            WHERE c.ativo = 1
            AND nr_doc = :nr_doc
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nr_doc', $nr_doc);
        } elseif (isset($_GET["id_empresa"]) && is_numeric($_GET["id_empresa"])) {
            $id_empresa = trim($_GET["id_empresa"]);
            $sql = "
            SELECT c.*, 
                IF(LENGTH(nr_doc) = 11, INSERT( INSERT( INSERT( nr_doc, 10, 0, '-' ), 7, 0, '.' ), 4, 0, '.' ), null) nr_doc_format,
			    IF(LENGTH(rg) = 9, INSERT( INSERT( INSERT( rg, 9, 0, '-' ), 6, 0, '.' ), 3, 0, '.' ), 
			    IF(LENGTH(rg) = 8, INSERT( INSERT( rg, 6, 0, '.' ), 3, 0, '.' ), null)) rg_format,
            rl.id_rl_colaborador_empresa,rl.data_admissao,DATE_FORMAT(rl.data_admissao,'%d/%m/%Y') data_admissao_mask,rl.matricula,rl.status,
            e.id_empresa, e.tipo_inscricao, e.nr_inscricao , e.razao_social
            FROM colaboradores c
            JOIN rl_colaboradores_empresas rl ON (rl.id_colaborador = c.id_colaborador AND rl.ativo = '1')
            JOIN empresas e ON (rl.id_empresa = e.id_empresa)
            WHERE c.ativo = 1
            AND rl.id_empresa = :id_empresa
            ORDER BY rl.status DESC , c.nome
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_empresa', $id_empresa);
        } elseif (isset($_GET["id_colaborador"]) && is_numeric($_GET["id_colaborador"])) {
            $id_colaborador = trim($_GET["id_colaborador"]);
            $sql = "
            SELECT c.*, 
                IF(LENGTH(nr_doc) = 11, INSERT( INSERT( INSERT( nr_doc, 10, 0, '-' ), 7, 0, '.' ), 4, 0, '.' ), null) nr_doc_format,
			    IF(LENGTH(rg) = 9, INSERT( INSERT( INSERT( rg, 9, 0, '-' ), 6, 0, '.' ), 3, 0, '.' ), 
			    IF(LENGTH(rg) = 8, INSERT( INSERT( rg, 6, 0, '.' ), 3, 0, '.' ), null)) rg_format,
            rl.id_rl_colaborador_empresa,rl.data_admissao,DATE_FORMAT(rl.data_admissao,'%d/%m/%Y') data_admissao_mask,rl.matricula,rl.status,
            e.id_empresa, e.tipo_inscricao, e.nr_inscricao , e.razao_social
            FROM colaboradores c
            JOIN rl_colaboradores_empresas rl ON (rl.id_colaborador = c.id_colaborador AND rl.ativo = '1')
            JOIN empresas e ON (rl.id_empresa = e.id_empresa)
            WHERE c.ativo = 1
            AND c.id_colaborador = :id_colaborador
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_colaborador', $id_colaborador);
        } else {
            $sql = "
            SELECT c.*, 
                IF(LENGTH(nr_doc) = 11, INSERT( INSERT( INSERT( nr_doc, 10, 0, '-' ), 7, 0, '.' ), 4, 0, '.' ), null) nr_doc_format,
			    IF(LENGTH(rg) = 9, INSERT( INSERT( INSERT( rg, 9, 0, '-' ), 6, 0, '.' ), 3, 0, '.' ), 
			    IF(LENGTH(rg) = 8, INSERT( INSERT( rg, 6, 0, '.' ), 3, 0, '.' ), null)) rg_format,
            rl.id_rl_colaborador_empresa,rl.data_admissao,DATE_FORMAT(rl.data_admissao,'%d/%m/%Y') data_admissao_mask,rl.matricula,rl.status,
            e.id_empresa, e.tipo_inscricao, e.nr_inscricao , e.razao_social
            FROM colaboradores c
            JOIN rl_colaboradores_empresas rl ON (rl.id_colaborador = c.id_colaborador AND rl.ativo = '1')
            JOIN empresas e ON (rl.id_empresa = e.id_empresa)
            WHERE c.ativo = 1
            ORDER BY rl.status DESC , c.nome
            ";
            $stmt = $conn->prepare($sql);
        }

        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        if ($stmt->rowCount() < 1) {
            $result = array(
                'status' => 'fail',
                'result' => 'Nenhum colaborador foi encontrado'
            );
        } elseif ($stmt->rowCount() == 1) {
            $dados = $stmt->fetch(PDO::FETCH_OBJ);
            $result = array(
                'status' => 'success',
                'result' => $dados
            );
        } else {
            $dados = $stmt->fetchAll(PDO::FETCH_OBJ);
            $result = array(
                'status' => 'success',
                'result' => $dados
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
