<?php
class Database {
    private $db;

    public function __construct() {
        $this->db = new PDO('sqlite:../aluno-senai.db');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Método para executar uma consulta SQL (SELECT, INSERT, etc.)
    public function execute($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // Método para buscar todos os registros de uma consulta
    public function fetchAll($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para buscar um único registro
    public function fetch($sql, $params = []) {
        $stmt = $this->execute($sql, $params); // Corrigido para usar execute
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retorna um único registro
    } 
}
?>
