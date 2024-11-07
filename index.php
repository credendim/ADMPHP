<?php
// Incluindo o arquivo de conexão ao banco de dados
require 'api/db.php';

$db = new Database();

// Inicializa variáveis para manter os dados do formulário
$rm = $_POST['rm'] ?? '';
$primeiroNome = $_POST['primeiroNome'] ?? '';
$nomeMeio = $_POST['nomeMeio'] ?? '';
$ultimoNome = $_POST['ultimoNome'] ?? '';
$dataNasc = $_POST['dataNasc'] ?? '';
$senha = $_POST['senha'] ?? '';
$siglaCurso = $_POST['siglaCurso'] ?? '';

$message = '';

$searchType = $_GET['searchType'] ?? '';
$searchQuery = $_GET['searchQuery'] ?? '';
$estudantesPesquisados = [];

// Verifica se o formulário foi enviado para adicionar um estudante
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    // Verifica se o curso existe
    $existingCourse = $db->fetch("SELECT * FROM Curso WHERE siglaCurso = ?", [$siglaCurso]);
    if (!$existingCourse) {
        $message = 'Curso não existe!';
    } else {
        // Verifica se o RM é único
        $existingStudent = $db->fetch("SELECT * FROM Estudante WHERE RM = ?", [$rm]);
        if ($existingStudent) {
            $message = 'Estudante com RM já existe!';
        } else {
            // Insere o novo estudante no banco de dados
            $db->execute("INSERT INTO Estudante (RM, PrimeiroNome, NomeMeio, UltimoNome, DataNasc, Senha, SiglaCurso) VALUES (?, ?, ?, ?, ?, ?, ?)", [
                $rm, $primeiroNome, $nomeMeio, $ultimoNome, $dataNasc, $senha, $siglaCurso
            ]);
            // Limpa os campos após inserção bem-sucedida
            $rm = $primeiroNome = $nomeMeio = $ultimoNome = $dataNasc = $senha = $siglaCurso = '';
        }
    }
}

// Verifica se a pesquisa foi feita
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($searchQuery)) {
    // Define a consulta SQL com base no tipo de pesquisa
    $sql = "SELECT RM, PrimeiroNome || ' ' || NomeMeio || ' ' || UltimoNome AS NomeCompleto, SiglaCurso FROM Estudante WHERE 1=1";
    $params = [];

    if ($searchType === 'RM') {
        $sql .= " AND RM = :searchQuery";
        $params[':searchQuery'] = $searchQuery;
    } elseif ($searchType === 'Curso') {
        $sql .= " AND SiglaCurso LIKE :searchQuery";
        $params[':searchQuery'] = '%' . $searchQuery . '%';
    }

    // Busca os estudantes de acordo com a pesquisa
    $estudantesPesquisados = $db->fetchAll($sql, $params);
}

// Recupera todos os estudantes
$estudantes = $db->fetchAll("SELECT * FROM Estudante");

// Verifica se o formulário para remover um estudante foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove') {
    $rmToRemove = $_POST['rmToRemove'] ?? null;

    if ($rmToRemove) {
        // Remove o estudante do banco de dados
        $db->execute("DELETE FROM Estudante WHERE RM = ?", [$rmToRemove]);
    }
}

// Recupera todos os estudantes
$estudantes = $db->fetchAll("SELECT * FROM Estudante");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Estudantes</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=search,visibility,visibility_off" />    
    <style>
        .alert {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 10px;
            margin: 20px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
        <script>
        function togglePassword(element) {
            const passwordField = element.previousElementSibling;
            if (passwordField.type === "password") {
                passwordField.type = "text";
                element.textContent = "visibility_off";
            } else {
                passwordField.type = "password";
                element.textContent = "visibility";
            }
        }
    </script>
</head>
<body>
    
    <a href="add_course.php">Voltar</a> 
    <div class="firstContainer">
        <div>   
            <!-- Formulario para adicionar estudante -->
            <h1>Lista de Estudantes</h1>
            
            <form method="POST" class="forms">
                <div>
                    <input type="number" name="rm" placeholder="RM" value="<?php echo htmlspecialchars($rm); ?>" required>
                    <input type="date" name="dataNasc" placeholder="Data de Nascimento" value="<?php echo htmlspecialchars($dataNasc); ?>">
                    <input type="text" name="siglaCurso" placeholder="Sigla do Curso" value="<?php echo htmlspecialchars($siglaCurso); ?>" required>
                </div>
                <div>
                    <input type="text" name="primeiroNome" placeholder="Primeiro Nome" value="<?php echo htmlspecialchars($primeiroNome); ?>" required>
                    <input type="text" name="nomeMeio" placeholder="Nome do Meio" value="<?php echo htmlspecialchars($nomeMeio); ?>">
                    <input type="text" name="ultimoNome" placeholder="Último Nome" value="<?php echo htmlspecialchars($ultimoNome); ?>" required>
                </div>
                <input type="password" name="senha" placeholder="Senha" value="<?php echo htmlspecialchars($senha); ?>" required>
                <input type="hidden" name="action" value="add">
                <button type="submit">Adicionar Estudante</button>
            </form>

            <?php if ($message): ?>
                <div class="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button onclick="this.parentElement.style.display='none';">✖</button>
                </div>
            <?php endif; ?>
        </div>
        <div class="pesquisa">
            <h1>Pesquisa de Estudantes</h1>
            <!-- Formulário de pesquisa -->
            <form class="pequisa" method="GET" action="">
                <select name="searchType" id="searchType" required>
                    <option value="">Selecione</option>
                    <option value="RM" <?php echo $searchType === 'RM' ? 'selected' : ''; ?>>RM</option>
                    <option value="Curso" <?php echo $searchType === 'Curso' ? 'selected' : ''; ?>>Curso</option>
                </select>
                <input type="text" name="searchQuery" placeholder="Digite sua pesquisa" required>
                <button type="submit"><span class="material-symbols-outlined">search</span></button>
            </form>

            <?php if (!empty($estudantesPesquisados)): ?>
                <h2>Resultados da Pesquisa:</h2>
                <div class="tabelaPesquisa">
                    <table>
                        <thead>
                            <tr>
                                <th>RM</th>
                                <th>Nome</th>
                                <th>Curso</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($estudantesPesquisados as $estudante): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($estudante['RM']); ?></td>
                                    <td><?php echo htmlspecialchars($estudante['NomeCompleto']); ?></td>
                                    <td><?php echo htmlspecialchars($estudante['SiglaCurso']); ?></td>
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="rmToRemove" value="<?php echo htmlspecialchars($estudante['RM']); ?>">
                                            <input type="hidden" name="action" value="remove">
                                            <button type="submit" onclick="return confirm('Tem certeza que deseja remover este estudante?');">✖</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($searchQuery): ?>
                <p>Nenhum estudante encontrado.</p>
            <?php endif; ?>
        </div>
    </div>
    
    

    <h2>Estudantes Cadastrados:</h2>
    <div class="container">
        <div>
            <div>RM:</div>
            <div>Nome:</div>
            <div>Nascimento:</div>
            <div>Senha:</div>
            <div>Curso:</div>
        </div>
        <?php foreach ($estudantes as $estudante): ?>
        <div class="alunos">
            <div><?php echo htmlspecialchars($estudante['RM']); ?></div>
            <div><?php echo htmlspecialchars($estudante['PrimeiroNome'] . ' ' . ($estudante['NomeMeio'] ? $estudante['NomeMeio'] . ' ' : '') . $estudante['UltimoNome']); ?></div>
            <div><?php echo htmlspecialchars($estudante['DataNasc']); ?></div>
            <div>
                <input type="password" value="<?php echo htmlspecialchars($estudante['Senha']); ?>" readonly>
                <span id="password-toggle" class="material-symbols-outlined" onclick="togglePassword(this)">visibility</span>
            </div>
            <div><?php echo htmlspecialchars($estudante['SiglaCurso']); ?></div>
            <form class="alunoRemove" method="POST" style="display:inline;">
                <input type="hidden" name="rmToRemove" value="<?php echo htmlspecialchars($estudante['RM']); ?>">
                <input type="hidden" name="action" value="remove">
                <button type="submit" onclick="return confirm('Tem certeza que deseja remover este estudante?');">✖</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
</body>
<style>
    h1, h2, h3 {
        text-align: center;
    }

    .firstContainer {
        display: flex;
        justify-content: space-around;
    }

    .firstContainer > div {
        width: 40%;
    }

    .firstContainer > div:first-child > form {
        display: flex;
        align-items: center;
        flex-direction: column;
    }

    .firstContainer > div:first-child > form > div {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
    }

    input {
        border-radius: 15px;
        height: 40px;
    }

    .forms {
        width: ;
    }

    .pesquisa > form > input {
        height: 20px;
    }

    .pesquisa > form > button {
        background-color: transparent;
        border: none;
        vertical-align: middle;
        margin-left: -40px;
    }

    .tabelaPesquisa {
        height: 150px;
        overflow-y: scroll;
    }

    .tabelaPesquisa::-webkit-scrollbar {
        width: 10px;
    }

    .tabelaPesquisa::-webkit-scrollbar-track {
        background-color: lightgray;
        border-radius: 5px;
    }

    .tabelaPesquisa::-webkit-scrollbar-thumb {
        background: red; 
        border-radius: 10px;
    }

    .tabelaPesquisa::-webkit-scrollbar-thumb:hover {
        background: lightcoral; 
    }
    
    .container {
        border-radius: 20px;
        display: flex;
        flex-direction: column;
        margin-left: 200px;
        margin-right: 200px;
        box-shadow: 0px 22px 15px 2px rgba(0,0,0,0.1);
    }

    .container > div:first-child {
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        gap: 0;
        height: 30px;
        background-color: red;
        display: flex;
        align-items: center;
        padding-right: 39px;
        color: white;
    }

    .alunos > div:nth-child(2)  {
        border-left: 1px solid lightgray;
        border-right: 1px solid lightgray;
    }

    .alunos > div:nth-child(4)  {
        border-left: 1px solid lightgray;
        border-right: 1px solid lightgray;
    }

    .container > div:first-child > div {
        width: 500px;
        display: flex;
        justify-content: center;
    }

    .alunos {
        display: flex;
        border-bottom: 1px solid lightgray;
    }

    .alunos > div {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 30px;
        width: 500px;
    }

    .alunos > div > input {
        border: 0;
        background-color: transparent;
        width: 150px;
        outline: none;
    }

    .alunos > div > span {
        user-select: none; /* Para navegadores modernos */
        -webkit-user-select: none; /* Para Safari */
        -moz-user-select: none; /* Para Firefox */
        -ms-user-select: none; /* Para Internet Explorer/Edge */
    }

    .alunos:last-child {
        border: 0;
    }

    .alunos > form {
        display: flex;
        align-content: center;
    }

    .alunoRemove > button {
        background-color: lightcoral;
        margin-right: 15px;
        border: 0;
        border-radius: 5px;
    }
</style>
</html>
