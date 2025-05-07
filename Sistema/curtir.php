<?php 

session_start();

if (isset($_SESSION["id_user"])) {

    $dados = json_decode(file_get_contents("php://input"), true);

    $id_postagem = $dados["id_postagem"];
    $id_usuario = $_SESSION["id_user"];
    
    require '../app/conexao.php';

    $sql_select = "SELECT * FROM tb_curtidas WHERE id_usuario = ? AND id_postagem = ?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param("ii", $id_usuario, $id_postagem);
    $stmt_select->execute();
    if (!$stmt_select->get_result()->num_rows > 0) {
        $sql_insert = "INSERT INTO tb_curtidas (id_usuario, id_postagem) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ii", $id_usuario, $id_postagem);
        if (!$stmt_insert->execute()) {
            echo "ERRO! Não foi possível curtir a postagem: " . $stmt_insert->error;
        }

        $stmt_insert->close();

    } else {
        
        $sql_delete = "DELETE FROM tb_curtidas WHERE id_usuario = ? AND id_postagem = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("ii", $id_usuario, $id_postagem);
        if (!$stmt_delete->execute()) {
            echo "ERRO! Não foi possível descurtir a postagem: " . $stmt_delete->error;
        } 
        
        $stmt_delete->close();
        
    }

    $sql_count = "SELECT count(*) FROM tb_curtidas WHERE id_postagem = ?";
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->bind_param("i", $id_postagem);
    $stmt_count->execute();
    $resultado = $stmt_count->get_result();
    $total_curtidas = $resultado->fetch_array()[0];

    $stmt_select->close();
    $conn->close();
    echo $total_curtidas;
}

?>