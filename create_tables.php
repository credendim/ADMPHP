<?php
// Conexão com o banco de dados
try {
    // Conecta ao banco de dados SQLite
    $db = new PDO('sqlite:../alunoSenai.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Criando a tabela Curso
    $db->exec("DROP TABLE IF EXISTS Curso");
    $db->exec("CREATE TABLE Curso (
        SiglaCurso CHAR(7) NOT NULL PRIMARY KEY,
        Descricao TEXT
    )");

    // Criando a tabela Estudante
    $db->exec("DROP TABLE IF EXISTS Estudante");
    $db->exec("CREATE TABLE Estudante (
        RM INT NOT NULL PRIMARY KEY,
        PrimeiroNome VARCHAR(30) NOT NULL,
        NomeMeio VARCHAR(30),
        UltimoNome VARCHAR(30) NOT NULL,
        DataNasc DATE,
        Senha VARCHAR(20) NOT NULL,
        Foto BLOB,
        SiglaCurso CHAR(7) NOT NULL,
        CONSTRAINT FK_Curso_Estudante FOREIGN KEY (SiglaCurso)
        REFERENCES Curso(SiglaCurso)
    )");

    echo "Tabelas criadas com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao criar tabelas: " . $e->getMessage();
}
?>
