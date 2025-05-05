<?php 
    session_start();

    if (!isset($_SESSION["id_user"])) {
        header("Location: /index.php");
        exit();
    }

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Feed Social</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg-dark: #1e1b2e;
      --bg-card: #2a2540;
      --text-color: #e4e4e4;
      --accent: #8e6fff;
      --border: #3c3457;
    }

    body {
      margin: 0;
      background-color: var(--bg-dark);
      color: var(--text-color);
      font-family: 'Inter', sans-serif;
    }

    header {
      background-color: var(--bg-card);
      padding: 1rem 2rem;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    header h1 {
      font-size: 1.5rem;
      color: var(--accent);
      margin: 0;
    }

    .btn-postar {
      background-color: var(--accent);
      border: none;
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }

    .feed {
      max-width: 600px;
      margin: 2rem auto;
      display: flex;
      flex-direction: column;
      gap: 2rem;
    }

    .post {
      background-color: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .post-header {
      display: flex;
      align-items: center;
      padding: 1rem;
      gap: 1rem;
    }

    .avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: var(--accent);
    }

    .username {
      font-weight: 600;
    }

    .post-image {
      width: 100%;
      height: 300px;
      background-color: #444;
      background-size: cover;
      background-position: center;
    }

    .post-caption {
      padding: 1rem;
      font-size: 0.95rem;
    }

    .actions {
      display: flex;
      justify-content: space-between;
      padding: 0 1rem 1rem;
      font-size: 0.9rem;
      color: var(--accent);
      cursor: pointer;
    }

    /* Modal */
    .modal {
      position: fixed;
      top: 0; left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0,0,0,0.7);
      display: none;
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background-color: var(--bg-card);
      border: 1px solid var(--border);
      padding: 2rem;
      border-radius: 10px;
      width: 90%;
      max-width: 400px;
    }

    .modal-content h2 {
      margin-top: 0;
      color: var(--accent);
    }

    .modal-content textarea,
    .modal-content input[type="file"] {
      width: 100%;
      margin-bottom: 1rem;
      padding: 0.5rem;
      background-color: var(--bg-dark);
      color: var(--text-color);
      border: 1px solid var(--border);
      border-radius: 5px;
    }

    .modal-buttons {
      display: flex;
      justify-content: flex-end;
      gap: 0.5rem;
    }

    .modal-buttons button {
      padding: 0.5rem 1rem;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }

    .btn-cancel {
      background-color: #555;
      color: white;
    }

    .btn-submit {
      background-color: var(--accent);
      color: white;
    }
  </style>
</head>
<body>

  <header>
    <h1>DevGram</h1>
    <button class="btn-postar" id="btnNovaPostagem">+ Nova Publicação</button>
  </header>

  <div class="feed">

    <?php 

        require 'app/conexao.php'; // conexão $conn

        $sql_postagens = "SELECT * FROM tb_postagens ORDER BY publicado_em DESC LIMIT 10";
        $resultado_postagens = $conn->query($sql_postagens);

        if ($resultado_postagens->num_rows > 0) {
            while($postagem = $resultado_postagens->fetch_assoc()) {
                $descricao = $postagem['descricao'];
                $url_imagem = $postagem['url_imagem'];
                $id_usuario = $postagem['id_usuario'];

                // Obter o nome de usuário do banco de dados (supondo que você tenha uma tabela de usuários)
                $sql_usuario = "SELECT nome FROM tb_usuarios WHERE id = $id_usuario";
                $resultado_usuario = $conn->query($sql_usuario);
                $usuario = $resultado_usuario->fetch_assoc()['nome'];
    ?>
    <div class="post">
      <div class="post-header">
        <div class="avatar"></div>
        <span class="username"><?php echo htmlspecialchars($usuario); ?></span>
      </div>
      <div class="post-image" style="background-image: url(<?php echo '\'Sistema/postagem/' . $url_imagem . '\'' ?>);"></div>
      <div class="post-caption">
        <p><?php echo htmlspecialchars($descricao); ?></p>
      </div>
      <div class="actions">
        <span>Curtir</span>
        <span>Comentar</span>
        <span>Compartilhar</span>
      </div>
    </div>

    <?php } } ?>

  </div>

  <!-- Modal -->
  <div class="modal" id="modalPostagem">
    <div class="modal-content">
      <h2>Nova Publicação</h2>
      <form action="Sistema/postagem/criar_postagem.php" method="post" enctype="multipart/form-data">
        <textarea name="descricao" rows="4" placeholder="Escreva uma legenda..." required></textarea>
        <input type="file" name="imagem" accept="image/*" required>
        <div class="modal-buttons">
            <button type="button" class="btn-cancel" id="btnCancelar">Cancelar</button>
            <button type="submit" class="btn-submit">Publicar</button>
        </div>
    </form>
    </div>
  </div>

  <script>
    const modal = document.getElementById('modalPostagem');
    const btnNovaPostagem = document.getElementById('btnNovaPostagem');
    const btnCancelar = document.getElementById('btnCancelar');

    btnNovaPostagem.onclick = () => modal.style.display = 'flex';
    btnCancelar.onclick = () => modal.style.display = 'none';

    window.onclick = (e) => {
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    }
  </script>

</body>
</html>
