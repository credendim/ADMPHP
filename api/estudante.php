<?php
header("Content-Type: application/json");
require 'db.php';

$db = new Database();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Buscar todos os estudantes
        $result = $db->fetchAll("SELECT * FROM Estudante");
        echo json_encode($result);
        break;

    case 'POST':
        // Adicionar um novo estudante
        $data = json_decode(file_get_contents('php://input'), true);
        $rm = $data['RM'] ?? 0;
        $primeiroNome = $data['PrimeiroNome'] ?? '';
        $nomeMeio = $data['NomeMeio'] ?? '';
        $ultimoNome = $data['UltimoNome'] ?? '';
        $dataNasc = $data['DataNasc'] ?? '';
        $senha = $data['Senha'] ?? '';
        $foto = $data['Foto'] ?? null;
        $siglaCurso = $data['SiglaCurso'] ?? '';

        // Adiciona novo estudante
        $db->execute("INSERT INTO Estudante (RM, PrimeiroNome, NomeMeio, UltimoNome, DataNasc, Senha, Foto, SiglaCurso)
                      VALUES (:rm, :primeiroNome, :nomeMeio, :ultimoNome, :dataNasc, :senha, :foto, :siglaCurso)", [
            ':rm' => $rm,
            ':primeiroNome' => $primeiroNome,
            ':nomeMeio' => $nomeMeio,
            ':ultimoNome' => $ultimoNome,
            ':dataNasc' => $dataNasc,
            ':senha' => $senha,
            ':foto' => $foto,
            ':siglaCurso' => $siglaCurso
        ]);
        echo json_encode(["message" => "Estudante criado com sucesso!"]);
        break;

    case 'PUT':
        // Atualizar um estudante
        $data = json_decode(file_get_contents('php://input'), true);
        $rm = $data['RM'] ?? 0;
        
        // Verifica se o RM está presente
        if ($rm === 0) {
            http_response_code(400);
            echo json_encode(["message" => "RM é necessário para atualizar."]);
            exit;
        }

        $primeiroNome = $data['PrimeiroNome'] ?? '';
        $nomeMeio = $data['NomeMeio'] ?? '';
        $ultimoNome = $data['UltimoNome'] ?? '';
        $dataNasc = $data['DataNasc'] ?? '';
        $senha = $data['Senha'] ?? '';
        $foto = $data['Foto'] ?? null;
        $siglaCurso = $data['SiglaCurso'] ?? '';

        // Atualiza estudante
        $db->execute("UPDATE Estudante SET PrimeiroNome = :primeiroNome, NomeMeio = :nomeMeio, UltimoNome = :ultimoNome, 
                      DataNasc = :dataNasc, Senha = :senha, Foto = :foto, SiglaCurso = :siglaCurso WHERE RM = :rm", [
            ':rm' => $rm,
            ':primeiroNome' => $primeiroNome,
            ':nomeMeio' => $nomeMeio,
            ':ultimoNome' => $ultimoNome,
            ':dataNasc' => $dataNasc,
            ':senha' => $senha,
            ':foto' => $foto,
            ':siglaCurso' => $siglaCurso
        ]);
        echo json_encode(["message" => "Estudante atualizado com sucesso!"]);
        break;

    case 'DELETE':
        // Deletar um estudante
        $data = json_decode(file_get_contents('php://input'), true);
        $rm = $data['RM'] ?? 0;

        // Verifica se o RM está presente
        if ($rm === 0) {
            http_response_code(400);
            echo json_encode(["message" => "RM é necessário para deletar."]);
            exit;
        }

        // Deleta estudante
        $db->execute("DELETE FROM Estudante WHERE RM = :rm", [
            ':rm' => $rm
        ]);
        echo json_encode(["message" => "Estudante deletado com sucesso!"]);
        break;

    default:
        http_response_code(405); // Método não permitido
        echo json_encode(["message" => "Método não suportado"]);
        break;
}
?>
