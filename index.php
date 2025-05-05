<?php

session_start();

//Logado
if (!empty($_SESSION["id_user"])) {
    header('location:feed.php');
}
    
$email = $senha = "";
$ErroEmail = $ErroSenha = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["submit"]) && $_POST["submit"] === "btn-login-submit") {
        //verificar preenchimento email
        if (empty($_POST["inputemaillogin"])) {
            $ErroEmailLogin = "invalido";
        } else {
            $email = test_input($_POST["inputemaillogin"]);
        }

        if (empty($_POST["inputsenhalogin"])) {
            $ErroSenhaLogin = "invalido";
        } else {
            $senha = test_input($_POST["inputsenhalogin"]);
        }

        if (empty($ErroEmailLogin) && empty($ErroSenhaLogin)) {

            require 'app/conexao.php';
            
            if(logar($email, $senha, $conn)) {
                header("location:feed.php");
            }
                
            $conn->close();
        }
            
    }
    else if(isset($_POST['submit']) && $_POST['submit'] === 'btn-cadastrar-submit') {

        if (empty($_POST["inputnomeregistro"])) {
            $ErroNomeRegistro = "invalido";
        } else {
            $nome = test_input($_POST["inputnomeregistro"]);
        }
    
        if (empty($_POST["inputemailregistro"])) {
            $ErroEmailRegistro = "invalido";
        } else {
            $email = test_input($_POST["inputemailregistro"]);
        }
    
        if (empty($_POST["inputsenharegistro"])) {
            $ErroSenhaRegistro = "invalido";
        } else {
            $senha = test_input($_POST["inputsenharegistro"]);
        }

        if (empty($ErroNomeRegistro) && empty($ErroEmailRegistro) && empty($ErroSenhaRegistro)) {

            require 'app/conexao.php';
    
            $hash_da_senha = md5($senha);
            $sql = "INSERT INTO tb_usuarios (nome, email, senha)
            VALUES ('$nome', '$email', '$hash_da_senha')";
            if ($conn->query($sql) != TRUE) {
                echo "Error:  $sql <br>" . $conn->error;
            } else {
                if(logar($email, $senha, $conn)) {
                    header("location:feed.php");
                }
            }
            $conn->close();
        }
    }
}

$lingua = "pt-br";

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function logar($email, $senha, $conn) {
    $hash_senha = md5($senha);
    
    $sql = "SELECT * FROM tb_usuarios WHERE email = '$email' AND senha = '$hash_senha';";

    $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            //Pode fazer Login
            while ($row = $result->fetch_assoc()) {
            //carregar as variaveis de Sessão do usuário
            $_SESSION["id_user"] = $row["id"];
            $_SESSION["email"] = $row["email"];
            $_SESSION["nome"] = $row["nome"];
            return true;
        }
        echo 'Não logou';
        return false;
    }
}

?>

<!DOCTYPE html>
<html lang="<?php echo $lingua; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <title>Entrar</title>
</head>

<body>

    <div class="main-area">
        <div class="forms-container">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="login-form">
                <div class="form-item">
                    <label for="inputemaillogin">Email</label>
                    <input class="form-input" type="email" name="inputemaillogin" id="inputemaillogin" placeholder="Email">
                </div>
                <div class="form-item">
                    <label for="inputsenhalogin">Senha</label>
                    <input class="form-input" type="password" name="inputsenhalogin" id="inputsenhalogin" placeholder="Senha">
                </div>
                <div class="btn-container">
                    <button type="submit" name="submit" class="btn-main" id="btn-login-submit" value="btn-login-submit">Entrar</button>
                    <button class="btn-secondary" id="btn-cadastrar">Cadastrar-se</button>
                </div>
            </form>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="cadastrar-form" style="display: none;">
                <div class="form-item">
                        <label for="inputnomeregistro">Nome</label>
                        <input class="form-input" type="text" name="inputnomeregistro" id="inputnomeregistro" placeholder="Nome">
                </div>
                <div class="form-item">
                    <label for="inputemailregistro">Email</label>
                    <input class="form-input" type="email" name="inputemailregistro" id="inputemailregistro" placeholder="Email">
                </div>
                <div class="form-item">
                    <label for="inputsenharegistro">Senha</label>
                    <input class="form-input" type="password" name="inputsenharegistro" id="inputsenharegistro" placeholder="Senha">
                </div>
                <div class="btn-container">
                    <button type="submit" name="submit" class="btn-main" id="btn-cadastrar-submit" value="btn-cadastrar-submit">Cadastrar-se</button>
                    <button class="btn-secondary" id="btn-login">Entrar</button>
                </div>
            </form>  
        </div>
    </div>

</body>

</html>

<style>

    .main-area {
        height: 100vh;
        width: 100vw;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--secondary-bg-color);
    }

    .forms-container {
        max-width: 500px;
        max-height: 500px;
        width: 80vw;
        padding: 50px;
        background-color: var(--main-bg-color);
        border-radius: 1rem;
    }

    .btn-container {
        margin-top: 2rem;
    }

    @media (min-width: 700px){
        .login-container {
            width: 50vw;
        }
    }

</style>

<script>

    const btnCadastrar = document.querySelector('#btn-cadastrar');
    const btnLogin = document.querySelector('#btn-login');

    btnCadastrar.addEventListener('click', (evt) => {
        const loginForm = document.querySelector('#login-form');
        const cadastrarForm = document.querySelector('#cadastrar-form');
        loginForm.style.display = 'none';
        cadastrarForm.style.display = 'block'

        evt.preventDefault();
        
    });

    btnLogin.addEventListener('click', (evt) => {
        const loginForm = document.querySelector('#login-form');
        const cadastrarForm = document.querySelector('#cadastrar-form');
        loginForm.style.display = 'block';
        cadastrarForm.style.display = 'none';

        evt.preventDefault();
        
    });


</script>