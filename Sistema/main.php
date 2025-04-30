<?php
//é obrigatório ser a primeira linha da págia que usa session
session_start();
//se existe alguma session criada, o usuario pode entrar
if (empty($_SESSION["id_user"])) {
    //se veio vazio, redireciono o usuário de volta para Login(index.php)
    header("location:../index.php?erro=sem session");
}
//codigo do cadastro da COISA
//variáveis 
$exibeMensagem = "";
$textoMensagem = "";
$titulo = $descricao = $foto = $valor = $cor = "";
//variáveis para controlar o que não foi preenchido
$vazioTitulo = $vazioDescricao = "";
//verificar se foi POST (POST é um clique no botão submit)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //verificar se foi botão postar_coisa que foi clicado
    if (isset($_POST["botao_postar"])) {
        //verificar de preencheu tudo que precisa
        if (empty($_POST["inputTitulo"])) {
            $vazioTitulo = " Esta vazio Titulo";
            $exibeMensagem = " show";
            $textoMensagem = "Não preencheu o Título";
        } else {
            $titulo = $_POST["inputTitulo"];
        }
        //verifica se descricao está preenchida
        if (empty($_POST["inputDescricao"])) {
            $vazioDescricao = " Esta vazio Descricao";
            $exibeMensagem = " show";
            $textoMensagem = "Não preencheu a Descrição";
        } else {
            $descricao = $_POST["inputDescricao"];
        }
        $foto = "sem_foto.png";
        $valor = $_POST["inputValor"];
        $cor = $_POST["inputCor"];
        //se o $vazioTitulo e $vazioDescricao estão vazios pode gravar 
        if (empty($vazioTitulo) || empty($vazioDescricao)) {
            //conectar com banco 
            require '../app/conexao.php';
            //pegando o id do usuario da session
            $idusuario = $_SESSION["id_user"];
            //inserir no banco com SQL
            $sql = "INSERT INTO `tb_coisa`(`id_usuario`, `titulo`, `imagem`, `descricao`, `valor`, `cor`) 
            VALUES ($idusuario,'$titulo','$foto','$descricao',$valor,'$cor')";

            if ($conn->query($sql) === TRUE) {
                // echo "New record created successfully";
                $exibeMensagem = " show";
                $textoMensagem = "Seu POST foi realizado com sucesso!";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

            $conn->close();
        }
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed</title>
    <form action="./logout.php" method="POST">
        <button type="submit">Sair</button>
    </form>
</head>

<body>
    <h1>HOME PAGE</h1>
</body>