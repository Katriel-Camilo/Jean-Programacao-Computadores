<?php
session_start();

if (!isset($_SESSION["id_user"])) {
    header("Location: /index.php");
    exit();
}

require '../../app/conexao.php'; // conexão $conn

// Verificar se foi enviada uma imagem e uma descrição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
    $id_usuario = $_SESSION["id_user"];
    if (!empty($_FILES['imagem']['name'])) {
        $pasta = 'uploads/';
        if (!is_dir($pasta)) {
            mkdir($pasta, 0755, true);
        }

        $nomeArquivo = basename($_FILES['imagem']['name']);
        $extensao = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));
        $nomeFinal = uniqid() . "." . $extensao;
        $caminhoCompleto = $pasta . $nomeFinal;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoCompleto)) {
            $stmt = $conn->prepare("INSERT INTO tb_postagens (id_usuario, descricao, url_imagem) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $id_usuario, $descricao, $caminhoCompleto);
            
            if ($stmt->execute()) {
                header("Location: ../../feed.php");
                exit();
            } else {
                echo "Erro ao inserir no banco: " . $stmt->error;
            }
        } else {
            echo "Erro ao fazer upload da imagem.";
        }
    } else {
        echo "Por favor, selecione uma imagem.";
    }

    $conn->close();
}
?>
