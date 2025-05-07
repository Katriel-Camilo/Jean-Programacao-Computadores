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
  <link rel="icon" type="image/svg+xml" href="imagens/comum/favicon.svg" /> 
  <title>Feed Social</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/feed.css">
</head>
<body>

  <header>
    <div>
      <h1>DevGram</h1>
      <a class="logout-link" href="/Sistema/logout.php">Logout</a>
    </div>
    <button class="btn-postar" id="btnNovaPostagem">+ Nova Publicação</button>
  </header>

  <div class="feed">

    <?php 

        require 'app/conexao.php'; // conexão $conn

        $sql_postagens = "SELECT * FROM tb_postagens ORDER BY publicado_em DESC LIMIT 10";
        $resultado_postagens = $conn->query($sql_postagens);

        if ($resultado_postagens->num_rows > 0) {
            while($postagem = $resultado_postagens->fetch_assoc()) {
                $id = $postagem['id'];
                $descricao = $postagem['descricao'];
                $url_imagem = $postagem['url_imagem'];
                $id_usuario = $postagem['id_usuario'];

                // Obter o nome de usuário do banco de dados (supondo que você tenha uma tabela de usuários)
                $sql_usuario = "SELECT nome, url_imagem FROM tb_usuarios WHERE id = $id_usuario";
                $resultado_usuario = $conn->query($sql_usuario);
                $usuario = $resultado_usuario->fetch_assoc();
                $usuario_nome = $usuario['nome'];
                $usuario_imagem = $usuario['url_imagem'];

                $sql_curtidas = "SELECT COUNT(*) as total_curtidas FROM tb_curtidas WHERE id_postagem = " . $postagem['id'];
                $resultado_curtidas = $conn->query($sql_curtidas);
                $curtidas = $resultado_curtidas->fetch_assoc()['total_curtidas'];
    ?>
    <div class="post" data-post-id="<?php echo $id ?>">
      <div class="post-header">
        <div class="avatar" style="background-image: url('<?php echo $usuario_imagem ?>')"></div>
        <span class="username"><?php echo htmlspecialchars($usuario_nome); ?></span>
      </div>
      <div class="post-image" style="background-image: url(<?php echo '\'Sistema/postagem/' . $url_imagem . '\'' ?>);"></div>
      <div class="post-caption">
        <p><?php echo htmlspecialchars($descricao); ?></p>
        <p><span class="contador-curtidas"><?php echo htmlspecialchars($curtidas) ; ?></span> curtidas</p>
      </div>
      <div class="actions">
        <span class="botao-curtir">Curtir</span>
        <span>Comentar</span>
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
        <input placeholder="Faça upload de sua imagem" type="file" name="imagem" accept="image/*" required>
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
    const botoesCurtir = document.querySelectorAll('.botao-curtir');

    btnNovaPostagem.onclick = () => modal.style.display = 'flex';
    btnCancelar.onclick = () => modal.style.display = 'none';

    window.onclick = (e) => {
      if (e.target === modal) {
        modal.style.display = 'none';
      }
      
    } 

    botoesCurtir.forEach( (botaoCurtir) => { botaoCurtir.onclick = () => {
      const idPostagem =  botaoCurtir.closest('.post').getAttribute('data-post-id') ; // ID da postagem
      const contadorCurtidas = botaoCurtir.closest('.post').querySelector('.contador-curtidas');

      fetch('Sistema/curtir.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          curtir: true,
          id_postagem: idPostagem
        })
      })
      .then(response => response.text())
      .then(data => {
        contadorCurtidas.innerHTML = data; 
      })
      .catch(error => console.error('Erro:', error));
    }
  
  });

  </script>

</body>
</html>
