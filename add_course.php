<?php
// Conexão com o banco de dados
require 'api/db.php';

$db = new Database();
$message = '';

// Se a requisição for POST, processa a adição do curso
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtém os dados do formulário
    $sigla = $_POST['SiglaCurso'] ?? '';
    $descricao = $_POST['Descricao'] ?? '';

    // Verifica se os campos estão preenchidos
    if (empty($sigla) || empty($descricao)) {
        $message = "SiglaCurso e Descricao são necessários.";
    } else {
        // Insere o curso no banco de dados
        try {
            $db->execute("INSERT INTO Curso (SiglaCurso, Descricao) VALUES (:sigla, :descricao)", [
                ':sigla' => $sigla,
                ':descricao' => $descricao
            ]);
            $message = "Curso adicionado com sucesso!";
        } catch (PDOException $e) {
            $message = "Erro ao adicionar curso: " . $e->getMessage();
        }
    }
}

// Recupera todos os cursos para exibição
$cursos = $db->fetchAll("SELECT * FROM Curso");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Curso</title>
</head>
<body>
    <h1>Adicionar Curso</h1>
    <a href="index.php">Voltar</a> <!-- Link para voltar à página principal -->
    <?php if (!empty($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form action="add_course.php" method="post">
        <label for="SiglaCurso">Sigla do Curso:</label>
        <input type="text" id="SiglaCurso" name="SiglaCurso" required>
        <br>
        <label for="Descricao">Descrição:</label>
        <input type="text" id="Descricao" name="Descricao" required>
        <br>
        <button type="submit">Adicionar Curso</button>
    </form>

    <h2>Cursos Cadastrados:</h2>
    <table>
        <thead>
            <tr>
                <th>Sigla do Curso</th>
                <th>Descrição</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cursos as $curso): ?>
                <tr>
                    <td><?php echo htmlspecialchars($curso['SiglaCurso']); ?></td>
                    <td><?php echo htmlspecialchars($curso['Descricao']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
