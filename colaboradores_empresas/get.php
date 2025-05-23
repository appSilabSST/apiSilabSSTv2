<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($_GET["id_empresa_reservado"]) && is_numeric($_GET["id_empresa_reservado"]) &&
            isset($_GET["id_colaborador"]) && is_numeric($_GET["id_colaborador"])
        ) {
            $id_empresa_reservado = trim($_GET["id_empresa_reservado"]);
            $id_colaborador = trim($_GET["id_colaborador"]);
            $sql = "
            SELECT c.*,c.nr_doc as nr_doc_colaborador,
            rl.id_rl_colaborador_empresa,rl.data_admissao,rl.matricula,rl.status,
            e.id_empresa, e.razao_social,e.nr_doc as nr_doc_empresa
            FROM colaboradores c
            JOIN rl_colaboradores_empresas rl ON (rl.id_colaborador = c.id_colaborador AND rl.ativo = '1')
            JOIN empresas e ON (rl.id_empresa = e.id_empresa)
            WHERE c.ativo = 1
            AND rl.id_empresa = :id_empresa
            AND rl.id_colaborador = :id_colaborador
            ORDER BY rl.status DESC , c.nome
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_empresa', $id_empresa_reservado);
            $stmt->bindParam(':id_colaborador', $id_colaborador);
        } else if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_rl_colaborador_empresa = trim($_GET["id"]);
            $sql = "
            SELECT c.*, 
                IF(LENGTH(c.nr_doc) = 11, INSERT( INSERT( INSERT( c.nr_doc, 10, 0, '-' ), 7, 0, '.' ), 4, 0, '.' ), null) nr_doc_format,
			    IF(LENGTH(rg) = 9, INSERT( INSERT( INSERT( rg, 9, 0, '-' ), 6, 0, '.' ), 3, 0, '.' ), 
			    IF(LENGTH(rg) = 8, INSERT( INSERT( rg, 6, 0, '.' ), 3, 0, '.' ), null)) rg_format,
            rl.id_rl_colaborador_empresa,rl.data_admissao,DATE_FORMAT(rl.data_admissao,'%d/%m/%Y') data_admissao_mask,rl.matricula,rl.status,
            e.id_empresa, e.nr_doc as nr_doc_empresa, e.razao_social
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
                IF(LENGTH(c.nr_doc) = 11, INSERT( INSERT( INSERT( c.nr_doc, 10, 0, '-' ), 7, 0, '.' ), 4, 0, '.' ), null) nr_doc_format,
			    IF(LENGTH(rg) = 9, INSERT( INSERT( INSERT( rg, 9, 0, '-' ), 6, 0, '.' ), 3, 0, '.' ), 
			    IF(LENGTH(rg) = 8, INSERT( INSERT( rg, 6, 0, '.' ), 3, 0, '.' ), null)) rg_format,
            rl.id_rl_colaborador_empresa,rl.data_admissao,DATE_FORMAT(rl.data_admissao,'%d/%m/%Y') data_admissao_mask,rl.matricula,rl.status,rl.data_demissao,
            e.id_empresa,  e.nr_doc as nr_doc_empresa,  e.razao_social
            FROM colaboradores c
            JOIN rl_colaboradores_empresas rl ON (rl.id_colaborador = c.id_colaborador AND rl.ativo = '1')
            JOIN empresas e ON (rl.id_empresa = e.id_empresa)
            WHERE c.ativo = 1
            AND c.nr_doc = :nr_doc
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nr_doc', $nr_doc);
        } elseif (isset($_GET["id_empresa"]) && is_numeric($_GET["id_empresa"])) {
            $id_empresa = trim($_GET["id_empresa"]);
            $sql = "
            SELECT c.*,c.nr_doc as nr_doc_colaborador,
            rl.id_rl_colaborador_empresa,rl.data_admissao,rl.matricula,rl.status,rl.data_demissao,
            e.id_empresa, e.razao_social,e.nr_doc as nr_doc_empresa
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
                IF(LENGTH(c.nr_doc) = 11, INSERT( INSERT( INSERT( c.nr_doc, 10, 0, '-' ), 7, 0, '.' ), 4, 0, '.' ), null) nr_doc_format,
			    IF(LENGTH(rg) = 9, INSERT( INSERT( INSERT( rg, 9, 0, '-' ), 6, 0, '.' ), 3, 0, '.' ), 
			    IF(LENGTH(rg) = 8, INSERT( INSERT( rg, 6, 0, '.' ), 3, 0, '.' ), null)) rg_format,
            rl.id_rl_colaborador_empresa,rl.id_rl_setor_funcao,rl.data_admissao,DATE_FORMAT(rl.data_admissao,'%d/%m/%Y') data_admissao_mask,rl.matricula,rl.status,rl.data_demissao,
            e.id_empresa, e.nr_doc as nr_doc_empresa,  e.razao_social
            FROM colaboradores c
            JOIN rl_colaboradores_empresas rl ON (rl.id_colaborador = c.id_colaborador AND rl.ativo = '1')
            JOIN empresas e ON (rl.id_empresa = e.id_empresa)
            WHERE c.ativo = 1
            AND c.id_colaborador = :id_colaborador
            ORDER BY rl.status DESC
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_colaborador', $id_colaborador);
        } else {
            $sql = "
            SELECT c.*,
                IF(LENGTH(c.nr_doc) = 11, INSERT( INSERT( INSERT( c.nr_doc, 10, 0, '-' ), 7, 0, '.' ), 4, 0, '.' ), null) nr_doc_format,
			    IF(LENGTH(rg) = 9, INSERT( INSERT( INSERT( rg, 9, 0, '-' ), 6, 0, '.' ), 3, 0, '.' ), 
			    IF(LENGTH(rg) = 8, INSERT( INSERT( rg, 6, 0, '.' ), 3, 0, '.' ), null)) rg_format,
            rl.id_rl_colaborador_empresa,rl.data_admissao,DATE_FORMAT(rl.data_admissao,'%d/%m/%Y') data_admissao_mask,rl.matricula,rl.status,rl.data_demissao,
            e.id_empresa,  e.nr_doc as nr_doc_empresa,  e.razao_social
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

        $result =  getResult($stmt);
        // $result =  $sql;
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
