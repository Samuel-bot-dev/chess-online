<?php
// learn-chess.php
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprender Xadrez - Desafios Interativos</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./root.css">
    <link rel="stylesheet" href="./sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* ... Seus estilos CSS permanecem os mesmos aqui ... */
    </style>
</head>

<body class="background">
    <?php include_once('sidebar.php'); // Inclui o sidebar 
    ?>

    <div class="learn-wrapper">
        <header class="learn-header">
            <h1>Aprenda os Fundamentos do Xadrez!</h1>
            <p>Explore as peças, seus movimentos e conceitos estratégicos em nosso tabuleiro interativo.</p>
        </header>

        <div class="learn-content">
            <div class="learn-text-section">
                <h2>O que é Xadrez?</h2>
                <p>O xadrez é um jogo de estratégia e tática jogado por dois oponentes em um tabuleiro quadriculado. O objetivo é dar xeque-mate no rei adversário.</p>
                <div class="challenge-section">
                    <h3>O Rei</h3>
                    <p>O Rei é a peça mais importante do tabuleiro. O objetivo do jogo é dar xeque-mate no Rei adversário.</p>
                    <button id="showKingMovement">
                        <img src="./images/icons/wk_20x20.png" alt="Rei" style="height: 20px; vertical-align: middle; margin-right: 5px;">
                        Ver Movimento do Rei
                    </button>
                    <button id="kingChallenge">
                        <img src="./images/icons/wk_20x20.png" alt="Rei" style="height: 20px; vertical-align: middle; margin-right: 5px;">
                        Desafio do Rei
                    </button>
                </div>

                <div class="challenge-section">
                    <h3>A Rainha</h3>
                    <p>A Rainha é a peça mais poderosa do tabuleiro. Ela combina os movimentos de uma Torre e de um Bispo, podendo se mover qualquer número de casas em linha reta, horizontalmente, verticalmente ou diagonalmente.</p>
                    <button id="showQueenMovement">
                        <img src="./images/icons/wq_20x20.png" alt="Rainha" style="height: 20px; vertical-align: middle; margin-right: 5px;">
                        Ver Movimento da Rainha
                    </button>
                    <button id="queenChallenge">
                        <img src="./images/icons/wq_20x20.png" alt="Rainha" style="height: 20px; vertical-align: middle; margin-right: 5px;">
                        Desafio da Rainha
                    </button>
                </div>

                <div class="challenge-section">
                    <h3>A Torre</h3>
                    <p>As Torres são peças de longo alcance que se movem em linha reta, horizontalmente ou verticalmente, qualquer número de casas. São poderosas em posições abertas ou no final do jogo.</p>
                    <button id="showRookMovement">
                        <img src="./images/icons/wr_20x20.png" alt="Torre" style="height: 20px; vertical-align: middle; margin-right: 5px;">
                        Ver Movimento da Torre
                    </button>
                    <button id="rookChallenge">
                        <img src="./images/icons/wr_20x20.png" alt="Torre" style="height: 20px; vertical-align: middle; margin-right: 5px;">
                        Desafio da Torre
                    </button>
                </div>

                <div class="challenge-section">
                    <h3>O Bispo</h3>
                    <p>O Bispo se move em linha reta, diagonalmente, qualquer número de casas. Cada Bispo permanece na cor de casa em que começa (um em casas claras, outro em casas escuras), o que os torna poderosos em diagonal.</p>
                    <button id="showBishopMovement">
                        <img src="./images/icons/wb_20x20.png" alt="Bispo" style="height: 20px; vertical-align: middle; margin-right: 5px;">
                        Ver Movimento do Bispo
                    </button>
                    <button id="bishopChallenge">
                        <img src="./images/icons/wb_20x20.png" alt="Bispo" style="height: 20px; vertical-align: middle; margin-right: 5px;">
                        Desafio do Bispo
                    </button>
                </div>

                <div class="challenge-section">
                    <h3>O Cavalo</h3>
                    <p>O Cavalo tem um movimento único em forma de "L", que consiste em duas casas em uma direção (horizontal ou vertical) e uma casa na direção perpendicular. É a única peça que pode pular outras peças.</p>
                    <button id="showKnightMovement">
                        <img src="./images/icons/wn_20x20.png" alt="Cavalo" style="height: 20px; vertical-align: middle; margin-right: 5px;">
                        Ver Movimento do Cavalo
                    </button>
                    <button id="knightChallenge">
                        <img src="./images/icons/wn_20x20.png" alt="Cavalo" style="height: 20px; vertical-align: middle; margin-right: 5px;">
                        Desafio do Cavalo
                    </button>
                </div>

                <div class="challenge-section">
                    <h3>O Peão</h3>
                    <p>Os Peões são as peças mais numerosas e movem-se apenas para a frente, uma casa por vez (com uma exceção no primeiro movimento). Eles capturam apenas na diagonal e promovem ao alcançar a última fileira.</p>
                    <button id="showPawnMovement">
                        <img src="./images/icons/wp_20x20.png" alt="Peão" style="height: 20px; vertical-align: middle; margin-right: 5px;">
                        Ver Movimento do Peão
                    </button>
                    <button id="pawnChallenge">
                        <img src="./images/icons/wp_20x20.png" alt="Peão" style="height: 20px; vertical-align: middle; margin-right: 5px;">
                        Desafio do Peão
                    </button>
                </div>

            </div> <div class="learn-board-section">
                <p id="challenge-status" style="color: #333; font-weight: bold; margin-bottom: 15px; text-align: center;"></p>
                <?php include_once('./chess.php'); // INCLUI O TABULEIRO E SEU JS AQUI! ?>
            </div>

        </div> </div> <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script defer src="./login/app.js"></script>
    <script defer src="./sidebar.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for chess.php's script to fully load and expose its functions
            // Use a small timeout to ensure global objects are truly available
            setTimeout(() => {
                // Ensure the global objects are available
                if (typeof window.chessGame === 'undefined' || typeof window.renderChessBoard === 'undefined') {
                    console.error("ERRO CRÍTICO: Objeto chessGame ou renderChessBoard não encontrado. Verifique chess.php.");
                    return;
                }

                // Initial setup: clear any game state from previous interactions
                // and load the standard starting position for learning.
                window.chessGame.load('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1');
                window.renderChessBoard();
                window.clearLearnHighlights(); // Ensure no leftover highlights from previous page loads

                const challengeStatus = document.getElementById('challenge-status');

                // Helper to clear all highlights and reset to initial position
                function resetLearningBoard() {
                    window.chessGame.load('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1');
                    window.clearLearnHighlights();
                    window.renderChessBoard();
                    challengeStatus.textContent = '';
                }

                // --- Lógica para o Rei ---
                document.getElementById('showKingMovement').addEventListener('click', function() {
                    resetLearningBoard(); // Start with a clean slate
                    challengeStatus.textContent = 'Demonstração de Movimento do Rei: Ele pode se mover uma casa em qualquer direção.';
                    window.setBoardToLearnFEN('8/8/8/4K3/8/8/8/8 w - - 0 1'); // Rei Branco em E5
                    window.highlightSquaresForLearn(['e5', 'd4', 'd5', 'd6', 'e4', 'e6', 'f4', 'f5', 'f6']);
                });

                document.getElementById('kingChallenge').addEventListener('click', function() {
                    resetLearningBoard();
                    challengeStatus.textContent = 'Desafio do Rei: Mova o Rei para proteger o peão em F7! (Clique no Rei e na casa de destino)';
                    window.setBoardToLearnFEN('8/5p2/8/8/8/8/4K3/8 w - - 0 1'); // Rei Branco em E2, Peão Preto em F7
                    window.highlightSquaresForLearn(['f7', 'e2']);
                });

                // --- Lógica para a Rainha ---
                document.getElementById('showQueenMovement').addEventListener('click', function() {
                    resetLearningBoard();
                    challengeStatus.textContent = 'Demonstração de Movimento da Rainha: Ela pode se mover em qualquer linha reta.';
                    window.setBoardToLearnFEN('8/8/8/4Q3/8/8/8/8 w - - 0 1'); // Rainha Branca em E5
                    window.highlightSquaresForLearn([
                        'e5', 'e1', 'e2', 'e3', 'e4', 'e6', 'e7', 'e8', // vertical
                        'a5', 'b5', 'c5', 'd5', 'f5', 'g5', 'h5', // horizontal
                        'a1', 'b2', 'c3', 'd4', 'f6', 'g7', 'h8', // diagonal principal
                        'h2', 'g3', 'f4', 'd6', 'c7', 'b8' // diagonal secundária
                    ]);
                });

                document.getElementById('queenChallenge').addEventListener('click', function() {
                    resetLearningBoard();
                    challengeStatus.textContent = 'Desafio da Rainha: Capture o peão preto em D7 com a Rainha Branca!';
                    window.setBoardToLearnFEN('8/3p4/8/8/8/8/8/3Q4 w - - 0 1'); // Rainha Branca em D1, Peão Preto em D7
                    window.highlightSquaresForLearn(['d1', 'd7']);
                });

                // --- Lógica para a Torre ---
                document.getElementById('showRookMovement').addEventListener('click', function() {
                    resetLearningBoard();
                    challengeStatus.textContent = 'Demonstração de Movimento da Torre: Ela se move horizontalmente ou verticalmente.';
                    window.setBoardToLearnFEN('8/8/8/4R3/8/8/8/8 w - - 0 1'); // Torre Branca em E5
                    window.highlightSquaresForLearn([
                        'e5', 'e1', 'e2', 'e3', 'e4', 'e6', 'e7', 'e8', // vertical
                        'a5', 'b5', 'c5', 'd5', 'f5', 'g5', 'h5' // horizontal
                    ]);
                });

                document.getElementById('rookChallenge').addEventListener('click', function() {
                    resetLearningBoard();
                    challengeStatus.textContent = 'Desafio da Torre: Mova a Torre para proteger o peão em A7!';
                    window.setBoardToLearnFEN('8/p7/8/8/8/8/8/R7 w - - 0 1'); // Torre Branca em A1, Peão Preto em A7
                    window.highlightSquaresForLearn(['a1', 'a7']);
                });

                // --- Lógica para o Bispo ---
                document.getElementById('showBishopMovement').addEventListener('click', function() {
                    resetLearningBoard();
                    challengeStatus.textContent = 'Demonstração de Movimento do Bispo: Ele se move diagonalmente.';
                    window.setBoardToLearnFEN('8/8/8/4B3/8/8/8/8 w - - 0 1'); // Bispo Branco em E5
                    window.highlightSquaresForLearn([
                        'e5', 'a1', 'b2', 'c3', 'd4', 'f6', 'g7', 'h8', // diagonal 1
                        'h2', 'g3', 'f4', 'd6', 'c7', 'b8' // diagonal 2
                    ]);
                });

                document.getElementById('bishopChallenge').addEventListener('click', function() {
                    resetLearningBoard();
                    challengeStatus.textContent = 'Desafio do Bispo: Capture o peão preto em G7 com o Bispo Branco!';
                    window.setBoardToLearnFEN('8/6p1/8/8/8/8/8/2B5 w - - 0 1'); // Bispo Branco em C1, Peão Preto em G7
                    window.highlightSquaresForLearn(['c1', 'g7']);
                });

                // --- Lógica para o Cavalo ---
                document.getElementById('showKnightMovement').addEventListener('click', function() {
                    resetLearningBoard();
                    challengeStatus.textContent = 'Demonstração de Movimento do Cavalo: Ele se move em "L" e pode pular.';
                    window.setBoardToLearnFEN('8/8/8/4N3/8/8/8/8 w - - 0 1'); // Cavalo Branco em E5
                    window.highlightSquaresForLearn(['e5', 'c4', 'c6', 'd3', 'd7', 'f3', 'f7', 'g4', 'g6']); // Todos os 8 movimentos em L
                });

                document.getElementById('knightChallenge').addEventListener('click', function() {
                    resetLearningBoard();
                    challengeStatus.textContent = 'Desafio do Cavalo: Mova o Cavalo para D6!';
                    window.setBoardToLearnFEN('8/8/8/8/8/8/8/4N3 w - - 0 1'); // Cavalo Branco em E1
                    window.highlightSquaresForLearn(['e1', 'd6']);
                });

                // --- Lógica para o Peão ---
                document.getElementById('showPawnMovement').addEventListener('click', function() {
                    resetLearningBoard();
                    challengeStatus.textContent = 'Demonstração de Movimento do Peão: Move-se para frente, captura na diagonal.';
                    window.setBoardToLearnFEN('8/8/8/8/8/8/4P3/8 w - - 0 1'); // Peão Branco em E2
                    window.highlightSquaresForLearn(['e2', 'e3', 'e4']); // Movimento inicial de 1 ou 2 casas
                });

                document.getElementById('pawnChallenge').addEventListener('click', function() {
                    resetLearningBoard();
                    challengeStatus.textContent = 'Desafio do Peão: Avance o Peão Branco para a casa A4!';
                    window.setBoardToLearnFEN('8/8/8/8/8/8/P7/8 w - - 0 1'); // Peão Branco em A2
                    window.highlightSquaresForLearn(['a2', 'a4']);
                });
            }, 100); // Small delay to ensure chess.php script has run and exposed globals
        });
    </script>
</body>

</html>