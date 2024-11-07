<?php
header("Content-Type: application/json");
require 'db.php';

$db = new Database();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Buscar todos os cursos
        $result = $db->fetchAll("SELECT * FROM Curso");
        echo json_encode($result);
        break;

    case 'POST':
        // Adicionar um novo curso
        $data = json_decode(file_get_contents('php://input'), true);
        $sigla = $data['SiglaCurso'] ?? '';
        $descricao = $data['Descricao'] ?? '';

        if (empty($sigla) || empty($descricao)) {
            http_response_code(400);
            echo json_encode(["message" => "SiglaCurso e Descricao são necessários."]);
            exit;
        }

        $db->execute("INSERT INTO Curso (SiglaCurso, Descricao) VALUES (:sigla, :descricao)", [
            ':sigla' => $sigla,
            ':descricao' => $descricao
        ]);
        echo json_encode(["message" => "Curso criado com sucesso!"]);
        break;

    case 'PUT':
        // Atualizar um curso
        $data = json_decode(file_get_contents('php://input'), true);
        $sigla = $data['SiglaCurso'] ?? '';
        $descricao = $data['Descricao'] ?? '';

        if (empty($sigla) || empty($descricao)) {
            http_response_code(400);
            echo json_encode(["message" => "SiglaCurso e Descricao são necessários."]);
            exit;
        }

        $db->execute("UPDATE Curso SET Descricao = :descricao WHERE SiglaCurso = :sigla", [
            ':sigla' => $sigla,
            ':descricao' => $descricao
        ]);
        echo json_encode(["message" => "Curso atualizado com sucesso!"]);
        break;

    case 'DELETE':
        // Deletar um curso
        $data = json_decode(file_get_contents('php://input'), true);
        $sigla = $data['SiglaCurso'] ?? '';

        if (empty($sigla)) {
            http_response_code(400);
            echo json_encode(["message" => "SiglaCurso é necessário."]);
            exit;
        }

        $db->execute("DELETE FROM Curso WHERE SiglaCurso = :sigla", [
            ':sigla' => $sigla
        ]);
        echo json_encode(["message" => "Curso deletado com sucesso!"]);
        break;

    default:
        http_response_code(405); // Método não permitido
        echo json_encode(["message" => "Método não suportado"]);
        break;
}
?>
