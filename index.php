<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Aplicativo de Xadrez</title>
<style>
  /* Reset básico */
  :root {
    --theme-background-image: url('https://images.chesscomfiles.com/uploads/v1/images_users/tiny_mce/Ciadoxadrez-Youtube/phpICaWvH.jpeg');
    --theme-board-style-coordinate-color-dark: #222;
    --text-color: #fff;
    --hover-bg: rgba(255, 255, 255, 0.1);
  }
  * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Inter', sans-serif; /* Usando Inter como padrão */
      list-style: none;
      text-decoration: none;
      color: inherit; /* Garante que a cor do texto seja herdada, facilitando controle */
  }

  /* Importa a fonte Inter */
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

  body {
      padding-top: 70px; /* Espaço pro header fixo */
      overflow-x: hidden;
      background: url("https://assets-themes.chess.com/image/drtgi/background.jpg"); /* Fundo mais claro */
      color: #374151; /* Texto mais escuro para contraste */
      font-size: 1.1rem; /* Ajuste para um tamanho de fonte base agradável */
      line-height: 1.83;
  }

  header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 70px;
      background-color: #ffffff; /* Fundo do cabeçalho branco */
      z-index: 1000;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 2rem;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Sombra mais suave */
  }

  header img {
      width: 180px;
      height: auto;
  }

  header nav ul {
      display: flex;
      gap: 20px;
  }

  header nav ul li a {
      color: #374151; /* Cor do texto dos links do cabeçalho */
      font-weight: 600;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      transition: background-color 0.3s ease;
  }

  header nav ul li a:hover {
      background-color: #e5e7eb; /* Hover mais suave */
  }

  section {
      min-height: calc(100vh - 70px);
      padding: 1.5rem 1rem; /* Mais padding no topo para espaçamento */
      max-width: 1080px;
      margin: 0 auto;
  }

  section .componentes {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 1.6rem;
      width: 100%;
      overflow-x: auto;
      padding-bottom: 1rem;
      scroll-padding: 1rem;
      scroll-behavior: smooth;
      user-select: none;
      scroll-snap-type: x mandatory; /* Para rolagem horizontal controlada */
      margin-top: 2rem; /* Espaço para o cabeçalho fixo */
  }

  .coluna {
      /* Este agora é apenas um container flexível para o link A */
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      min-width: 250px;
      scroll-snap-align: start;
      /* Remove os estilos de background e shadow daqui, eles vão para o link A */
  }

  .shadownew {
      display: flex; /* Usar flex para alinhar ícone e texto */
      flex-direction: row; /* Alinhar na horizontal */
      gap: 0.8rem; /* Espaço entre ícone e texto */
      width: 100%; /* Ajuste a largura para ser responsiva no grid */
      height: 5rem;
      font-weight: 600;
      border-radius: 15px;
      font-size: 1.5rem;
      padding: 1.2rem 1rem;
      background-color: #ffffff; /* Fundo branco para o shadownew */
      color: #374151; /* Cor de texto para o shadownew */
      transition: background-color 0.3s ease, transform 0.2s ease;
      text-align: left; /* Alinhar o texto à esquerda dentro do botão */
      box-shadow: 0 0.5rem 0 0 rgba(0, 0, 0, .1); /* Sombra para o efeito "novo" */
      border: none; /* Remove qualquer borda padrão */
      cursor: pointer; /* Indica que é clicável */
      text-decoration: none; /* Remove sublinhado padrão dos links */
  }

  .shadownew:hover {
      background-color: #e5e7eb; /* Hover mais suave */
      transform: translateY(-3px);
      box-shadow: 0 0.6rem 0 0 rgba(0, 0, 0, .08); /* Sombra um pouco maior no hover */
  }

  .iconchess {
      height: 3rem;
      width: 3rem;
      transform: translateY(0) scale(1); /* Resetar transformações específicas */
      flex-shrink: 0; /* Impede que o ícone encolha */
      display: flex; /* Usar flex para centralizar o conteúdo (a imagem SVG) */
      align-items: center;
      justify-content: center;
  }

  .iconchess::before {
      display: block; /* Garante que a pseudo-classe ocupe espaço */
      height: 100%;
      width: 100%;
      background-repeat: no-repeat;
      background-position: center;
      background-size: contain; /* Garante que a imagem caiba dentro do .iconchess */
      content: ''; /* O conteúdo agora é definido pelo background-image */
  }

  .newpartida .iconchess::before {
      background-image: url('https://www.chess.com/bundles/web/images/color-icons/playwhite.cea685ba.svg');
  }

  .bots .iconchess::before {
      background-image: url('https://www.chess.com/bundles/web/images/color-icons/computer.f36f0d84.svg');
  }

  .min10 .iconchess::before {
      background-image: url('https://www.chess.com/bundles/web/images/color-icons/rapid.d3940770.svg');
  }

  .info {
      flex-grow: 1; /* Permite que o texto cresça e ocupe o espaço restante */
      text-align: left;
      display: flex;
      align-items: center; /* Alinha o texto verticalmente ao centro */
      white-space: nowrap; /* Evita que o texto quebre */
      overflow: hidden; /* Oculta o texto que excede o limite */
      text-overflow: ellipsis; /* Adiciona "..." ao texto excedente */
  }

  .info .title {
      font-weight: 600;
      font-size: 1.5rem; /* Ajustado para melhor legibilidade */
  }

  .previewWrapper {
      display: block;
      position: relative;
      overflow: hidden;
      height: 350px;
      width: 100%;
      border-radius: 15px; /* Arredonda as bordas da pré-visualização */
      box-shadow: 0 4px 6px rgba(0,0,0,0.05); /* Sombra para o wrapper */
  }

  .preview {
      background-image: url('https://placehold.co/600x400/9ca3af/ffffff?text=Xadrez+Preview');
      width: 100%;
      padding-bottom: 56.25%; /* Proporção 16:9 para vídeos */
      position: relative;
      background-position: center;
      background-size: cover;
      border-radius: 15px; /* Garante que a imagem tenha bordas arredondadas */
  }

  .previewBottom {
      color: #374151; /* Cor do texto da legenda da pré-visualização */
      font-size: 1.5rem;
      font-weight: 600;
      min-height: 4.5rem;
      padding: 1.1rem;
      text-align: center;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      background-color: #ffffff; /* Fundo branco para a legenda */
      border-bottom-left-radius: 15px; /* Arredonda as bordas inferiores */
      border-bottom-right-radius: 15px;
      margin-top: -15px; /* Move para cima para sobrepor um pouco o preview */
      position: relative; /* Para garantir que o z-index funcione se houver sobreposição */
  }

  /* Responsividade */
  @media (max-width: 1024px) {
      section .componentes {
          grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
          margin-top: 1.5rem;
      }
      header {
          padding: 0 1.5rem;
      }
  }

  @media (max-width: 768px) {
      header {
          padding: 0 1rem;
          height: 60px;
      }
      body {
          padding-top: 60px;
          font-size: 1rem;
      }
      header img {
          width: 140px;
      }
      section .componentes {
          grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
          gap: 1rem;
          margin-top: 1rem;
          padding: 0.5rem;
      }
      .shadownew {
          height: 4.5rem;
          font-size: 1.3rem;
          padding: 1rem 0.8rem;
      }
      .iconchess {
          transform: translateY(0) scale(0.8);
          height: 2.5rem;
          width: 2.5rem;
      }
      .info .title {
          font-size: 1.3rem;
      }
      .previewWrapper {
          height: 280px;
      }
      .previewBottom {
          font-size: 1.3rem;
          min-height: 4rem;
          padding: 0.8rem;
      }
  }

  @media (max-width: 480px) {
      header {
          height: 50px;
          padding: 0 0.8rem;
      }
      body {
          padding-top: 50px;
          font-size: 0.9rem;
      }
      header img {
          width: 120px;
      }
      header nav ul {
          gap: 10px;
      }
      header nav ul li a {
          padding: 0.4rem 0.8rem;
          font-size: 0.9rem;
      }
      section {
          padding: 1rem 0.5rem;
      }
      section .componentes {
          grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
          gap: 0.8rem;
          margin-top: 0.5rem;
      }
      .shadownew {
          height: 4rem;
          font-size: 1.1rem;
          padding: 0.8rem 0.6rem;
      }
      .iconchess {
          transform: translateY(0) scale(0.7);
          height: 2rem;
          width: 2rem;
      }
      .info .title {
          font-size: 1.1rem;
      }
      .previewWrapper {
          height: 220px;
      }
      .previewBottom {
          font-size: 1.1rem;
          min-height: 3.5rem;
          padding: 0.6rem;
      }
  }
</style>
</head>
<body class="pt-16 overflow-x-hidden">
  <header>
    <img src="https://placehold.co/180x50/374151/ffffff?text=Logo" alt="Logo" onerror="this.onerror=null;this.src='https://placehold.co/180x50/374151/ffffff?text=Logo+Fallback';" />
    <nav>
      <ul>
        <li><a href="#">Início</a></li>
        <li><a href="#">Jogos</a></li>
        <li><a href="#">Ranking</a></li>
        <li><a href="#">Perfil</a></li>
      </ul>
    </nav>
  </header>
  <section>
    <div class="componentes">
      <div class="coluna">
        <a href="./chess-init.php" class="newpartida shadownew">
          <div class="iconchess"></div>
          <div class="info">
            <div class="title">Nova Partida</div>
          </div>
        </a>
      </div>

      <div class="coluna">
        <a href="#" class="bots shadownew">
          <div class="iconchess"></div>
          <div class="info">
            <div class="title">Jogos contra bots</div>
          </div>
        </a>
      </div>

      <div class="coluna">
        <a href="#" class="min10 shadownew">
          <div class="iconchess"></div>
          <div class="info">
            <div class="title">Minuto 10</div>
          </div>
        </a>
      </div>

       
    </div>
  </section>
</body>
</html>