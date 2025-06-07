<?php
// play-online.php
// Esta será a página para jogar online contra outro jogador.
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jogar Xadrez Online</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./root.css">
    <link rel="stylesheet" href="./sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Estilos básicos para centralizar o tabuleiro e mensagens */
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background-color: var(--bg-color); /* Use sua cor de fundo */
            color: var(--text-color);
        }
        .game-container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            padding: 25px;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        #multiplayerStatus {
            font-size: 1.2em;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 10px;
            text-align: center; /* Centraliza o texto */
        }
        #opponentStatus {
             font-size: 1em;
             color: #666;
             text-align: center; /* Centraliza o texto */
        }
        #gameEndMessage {
            font-size: 1.5em;
            color: red;
            font-weight: bold;
            margin-top: 20px;
            text-align: center; /* Centraliza o texto */
        }
        /* Estilos para o canvas do tabuleiro, similar ao seu chess.php */
        canvas {
            max-width: 100%; /* Garante responsividade */
            height: auto;
            border: 2px solid #555;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.4);
        }

        /* Ajustes para o layout com sidebar */
        body.sidebar-open .game-container {
            margin-left: 320px; /* Empurra o conteúdo principal para a direita */
            transition: margin-left 0.3s ease-in-out;
        }

        @media (max-width: 1019px) {
            body.sidebar-open .game-container {
                margin-left: auto; /* Remove o empurrão em mobile */
            }
        }
        #status {
      margin: 15px;
      font-size: 18px;
      font-weight: bold;
    }
    #promotionDialog {
      display: none;
      background: white;
      border: 2px solid #333;
      padding: 10px;
      border-radius: 6px;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 10;
    }
    #promotionDialog button {
      font-size: 20px;
      margin: 5px;
      padding: 5px 12px;
      cursor: pointer;
    }
    </style>
</head>
<body class="background">
    <?php include_once('sidebar.php'); // Inclui o sidebar ?>

    <div class="game-container">
         
        <b id="multiplayerStatus">Conectando ao servidor...</b>
          <b id="opponentStatus"></b> 
        <b id="gameEndMessage"></b>
        <?php include_once('./chess.php'); // Inclui o tabuleiro e seu JS ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.0.0/socket.io.min.js"></script>
    <script defer src="./login/app.js"></script>
    <script defer src="./sidebar.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Garante que o tabuleiro de xadrez do chess.php esteja carregado
            // Aumentei o timeout para 500ms para maior compatibilidade
            setTimeout(() => {
                if (typeof window.chessGame === 'undefined' || typeof window.renderChessBoard === 'undefined') {
                    console.error("ERRO CRÍTICO: Objeto chessGame ou renderChessBoard não encontrado. Verifique chess.php.");
                    return;
                }

                // Configuração do Socket.IO
                const socket = io('http://localhost:3000'); // Conecta ao seu servidor Node.js
                let myGameId = null;
                // Estas variáveis serão definidas pelo servidor e passadas globalmente para chess.php
                window.myColor = null; // 'w' for white, 'b' for black
                window.isMyTurn = false;

                const multiplayerStatusEl = document.getElementById('multiplayerStatus');
                const opponentStatusEl = document.getElementById('opponentStatus');
                const gameEndMessageEl = document.getElementById('gameEndMessage');

                // Reseta e renderiza o tabuleiro inicial.
                window.chessGame.load('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1');
                window.renderChessBoard();
                window.clearLearnHighlights(); // Garante que não há destaques de aprendizado da página learn-chess.php

                // --- Funções de Ajuda para o Estado do Jogo ---
                function updateStatusDisplay() {
                    if (!myGameId) {
                        multiplayerStatusEl.textContent = 'Conectado. Aguardando oponente...';
                        opponentStatusEl.textContent = '';
                        return;
                    }

                    let statusText = ` Você é as ${window.myColor === 'w' ? 'Brancas' : 'Pretas'}.`;
                    if (window.isMyTurn) {
                        statusText += ' É o seu turno!';
                        multiplayerStatusEl.style.color = 'var(--primary-color)'; // Cor para seu turno
                    } else {
                        statusText += ' Aguardando o movimento do oponente.';
                        multiplayerStatusEl.style.color = '#666'; // Cor para turno do oponente
                    }
                    multiplayerStatusEl.textContent = statusText;
                }


                // --- Lógica de Conexão e Emparelhamento ---
                socket.on('connect', () => {
                    multiplayerStatusEl.textContent = 'Conectado ao servidor. Buscando partida...';
                    gameEndMessageEl.textContent = ''; // Limpa mensagens anteriores
                    console.log('Connected to game server.');
                });

                socket.on('waitingForOpponent', (message) => {
                    multiplayerStatusEl.textContent = message;
                    opponentStatusEl.textContent = '';
                });

                socket.on('matchFound', (data) => {
                    myGameId = data.gameId;
                    window.myColor = data.playerColor; // 'w' ou 'b'
                    window.isMyTurn = (window.myColor === 'w'); // Branco começa

                    gameEndMessageEl.textContent = ''; // Limpa mensagens anteriores

                    // Carrega o tabuleiro inicial para a partida online
                    window.chessGame.load('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1'); // Posição inicial
                    window.renderChessBoard();

                    updateStatusDisplay();
                    opponentStatusEl.textContent = `Oponente: ${data.opponentId.substring(0, 5)}...`; // Mostra um ID curto do oponente
                });

                // --- Lógica de Movimento (Função Global para ser chamada de chess.php) ---
                window.handleMultiplayerMove = function(from, to, promotion) {
                    if (!myGameId) {
                        multiplayerStatusEl.textContent = 'Não há partida ativa. Aguarde um oponente ou reconecte.';
                        console.warn('Não há partida ativa para fazer movimento.');
                        return false;
                    }
                    if (!window.isMyTurn) {
                        multiplayerStatusEl.textContent = 'Não é o seu turno!';
                        console.warn('Não é o seu turno para mover.');
                        return false;
                    }

                    // Pre-validação do movimento no cliente (apenas para feedback rápido, o servidor valida de verdade)
                    const tempGame = new Chess(window.chessGame.fen());
                    const moveAttempt = tempGame.move({ from, to, promotion });

                    if (!moveAttempt) {
                        multiplayerStatusEl.textContent = 'Movimento inválido! Tente novamente.';
                        window.renderChessBoard(); // Re-renderiza para limpar seleções, etc.
                        return false;
                    }

                    // Se o movimento parece válido localmente, envia para o servidor
                    socket.emit('move', {
                        gameId: myGameId,
                        from: from,
                        to: to,
                        promotion: promotion,
                        playerColor: window.myColor
                    });

                    window.isMyTurn = false; // Assume que o movimento foi enviado, aguarda confirmação do servidor
                    multiplayerStatusEl.textContent = 'Movimento enviado. Aguardando oponente...';
                    return true; // Indica que o movimento foi enviado para processamento
                };

                socket.on('moveMade', (data) => {
                    window.chessGame.load(data.fen); // Carrega o FEN atualizado do servidor
                    window.renderChessBoard(); // Renderiza o tabuleiro com o novo estado

                    if (window.chessGame.turn() === window.myColor) {
                        window.isMyTurn = true;
                    } else {
                        window.isMyTurn = false;
                    }
                    updateStatusDisplay();
                });

                // --- Mensagens de erro/fim de jogo ---
                socket.on('invalidMove', (message) => {
                    multiplayerStatusEl.textContent = `Erro: ${message}`;
                    // Após um movimento inválido, ainda é seu turno
                    window.isMyTurn = true;
                    window.renderChessBoard(); // Re-renderiza para limpar seleções, etc.
                    updateStatusDisplay(); // Atualiza o status para "É o seu turno!" novamente
                });

                socket.on('gameOver', (data) => {
                    multiplayerStatusEl.textContent = `Partida Terminada!`;
                    if (data.winner === 'draw') {
                        gameEndMessageEl.textContent = 'Empate!';
                    } else if (data.winner === window.myColor) {
                        gameEndMessageEl.textContent = 'Você Venceu!';
                    } else {
                        gameEndMessageEl.textContent = 'Você Perdeu!';
                    }
                    window.isMyTurn = false;
                    myGameId = null; // Encerra a partida no cliente
                    opponentStatusEl.textContent = ''; // Limpa o status do oponente
                    console.log('Game Over:', data);
                });

                socket.on('opponentDisconnected', (message) => {
                    multiplayerStatusEl.textContent = message;
                    gameEndMessageEl.textContent = 'Partida Encerrada: O oponente desconectou.';
                    window.isMyTurn = false;
                    myGameId = null;
                    opponentStatusEl.textContent = '';
                    console.log('Opponent disconnected.');
                });

                socket.on('disconnect', () => {
                    multiplayerStatusEl.textContent = 'Desconectado do servidor. Tentando reconectar...';
                    opponentStatusEl.textContent = '';
                    gameEndMessageEl.textContent = '';
                    window.isMyTurn = false;
                    myGameId = null;
                    console.log('Disconnected from game server.');
                });

            }, 500); // Aumentei o delay para 500ms
        });
    </script>
</body>
</html>