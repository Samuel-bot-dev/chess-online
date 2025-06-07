<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Xadrez com Destaque no Quadrado Selecionado</title>
  <style>
    body {
      display: flex;
      flex-direction: column;
      align-items: center;
      background-color: #f0f0f0;
      margin: 0;
      height: 100vh;
      justify-content: center;
    }
    canvas {
      border: 2px solid #333;
      cursor: pointer;
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
<body>
<div id="status"></div>
<canvas id="chessCanvas" width="480" height="480"></canvas>

<div id="promotionDialog">
  <div>Escolha promoção:</div>
  <button data-piece="q">Dama</button>
  <button data-piece="r">Torre</button>
  <button data-piece="b">Bispo</button>
  <button data-piece="n">Cavalo</button>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/chess.js/0.12.0/chess.min.js"></script>
<script>
  const canvas = document.getElementById('chessCanvas');
  const ctx = canvas.getContext('2d');
  const game = new Chess(); // The chess.js game instance
  const squareSize = 60;

  const statusEl = document.getElementById('status');
  const promotionDialog = document.getElementById('promotionDialog');
  let promotionCallback = null;

  let selectedSquare = null; // [col, row]
  let validMoves = [];

  // --- IMAGENS DAS PEÇAS ---
  const pieceImages = {};
  const pieceMap = {
      'wp': 'wp.png', 'wr': 'wr.png', 'wn': 'wn.png', 'wb': 'wb.png', 'wq': 'wq.png', 'wk': 'wk.png',
      'bp': 'bp.png', 'br': 'br.png', 'bn': 'bn.png', 'bb': 'bb.png', 'bq': 'bq.png', 'bk': 'bk.png'
  };
  let allPiecesLoaded = false;
  let loadImagesPromise; // To hold the promise of image loading

  function loadPieceImages() {
      let loadedCount = 0;
      const totalPieces = Object.keys(pieceMap).length;

      return new Promise((resolve, reject) => {
          if (totalPieces === 0) { // Handle case with no pieces to load
              allPiecesLoaded = true;
              resolve();
              return;
          }
          Object.keys(pieceMap).forEach(pieceKey => {
              const img = new Image();
              img.src = `./images/pieces/${pieceMap[pieceKey]}`; // Adjust path if necessary
              img.onload = () => {
                  pieceImages[pieceKey] = img;
                  loadedCount++;
                  if (loadedCount === totalPieces) {
                      allPiecesLoaded = true;
                      resolve();
                  }
              };
              img.onerror = () => {
                  console.error(`Erro ao carregar imagem da peça: ${img.src}`);
                  // Even if an image fails, try to resolve after all attempts
                  loadedCount++;
                  if (loadedCount === totalPieces) {
                      allPiecesLoaded = true;
                      resolve();
                  }
              };
          });
      });
  }

  // --- Variável global para destaques temporários para o modo de aprendizado ---
  let tempHighlightedSquares = [];

  // --- FUNÇÕES DE DESENHO ---
  function drawBoard() {
      for (let row = 0; row < 8; row++) {
          for (let col = 0; col < 8; col++) {
              let isHighlighted = false;
              const squareName = String.fromCharCode('a'.charCodeAt(0) + col) + (8 - row);

              // Check if the square is in the temporary highlight list (for learn mode)
              if (tempHighlightedSquares.includes(squareName)) {
                  isHighlighted = true;
              }
              // Check if the square is currently selected by the player
              if (selectedSquare && selectedSquare[0] === col && selectedSquare[1] === row) {
                  isHighlighted = true;
              }

              if (isHighlighted) {
                  ctx.fillStyle = 'rgba(255, 255, 0, 0.4)'; // Yellow semi-transparent for highlights
              } else {
                  ctx.fillStyle = (row + col) % 2 === 0 ? '#f0d9b5' : '#b58863';
              }
              ctx.fillRect(col * squareSize, row * squareSize, squareSize, squareSize);
          }
      }
  }

  function drawPieces() {
      if (!allPiecesLoaded) {
          // If images aren't loaded, try to render with text symbols as a fallback
          // This should ideally not happen if loadPieceImages() is awaited.
          console.warn('Imagens das peças ainda não carregadas, usando símbolos de texto.');
          const board = game.board();
            for (let row = 0; row < 8; row++) {
                for (let col = 0; col < 8; col++) {
                    const piece = board[row][col];
                    if (piece) {
                        ctx.fillStyle = piece.color === 'w' ? '#fff' : '#000';
                        ctx.font = '40px Arial';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(getPieceSymbol(piece), col * squareSize + squareSize / 2, row * squareSize + squareSize / 2);
                    }
                }
            }
          return;
      }

      const board = game.board();
      for (let row = 0; row < 8; row++) {
          for (let col = 0; col < 8; col++) {
              const piece = board[row][col];
              if (piece) {
                  const pieceKey = piece.color + piece.type;
                  const img = pieceImages[pieceKey];
                  if (img) {
                      ctx.drawImage(img, col * squareSize, row * squareSize, squareSize, squareSize);
                  } else {
                      console.warn(`Imagem para a peça "${pieceKey}" não encontrada. Usando símbolo.`);
                      // Fallback to text symbol if image is missing for a specific piece
                      ctx.fillStyle = piece.color === 'w' ? '#fff' : '#000';
                      ctx.font = '40px Arial';
                      ctx.textAlign = 'center';
                      ctx.textBaseline = 'middle';
                      ctx.fillText(getPieceSymbol(piece), col * squareSize + squareSize / 2, row * squareSize + squareSize / 2);
                  }
              }
          }
      }
  }

  function getPieceSymbol(piece) {
      const symbols = { p: '♟', r: '♜', n: '♞', b: '♝', q: '♛', k: '♚' };
      return piece.color === 'w' ? symbols[piece.type].toUpperCase() : symbols[piece.type];
  }

  function drawMoveDots() {
      // Draw valid moves for the currently selected piece
      if (selectedSquare) {
          ctx.fillStyle = 'rgba(0, 0, 255, 0.5)'; // Blue translucent dot
          validMoves.forEach(move => {
              const col = move.to.charCodeAt(0) - 'a'.charCodeAt(0);
              const row = 8 - parseInt(move.to[1]);
              const cx = col * squareSize + squareSize / 2;
              const cy = row * squareSize + squareSize / 2;
              const radius = squareSize / 8;

              const targetPiece = game.get(move.to);
              if (targetPiece && targetPiece.color !== game.turn()) {
                  // Capture indication (red border)
                  ctx.strokeStyle = 'rgba(255, 0, 0, 0.7)';
                  ctx.lineWidth = 3;
                  ctx.beginPath();
                  ctx.arc(cx, cy, squareSize / 2 - 5, 0, 2 * Math.PI);
                  ctx.stroke();
              } else {
                  // Empty square move (blue dot)
                  ctx.beginPath();
                  ctx.arc(cx, cy, radius, 0, 2 * Math.PI);
                  ctx.fill();
              }
          });
      }
  }

  function updateStatus() {
      let status = '';
      if (game.in_checkmate()) {
          status = `Xeque-mate! Vitória do ${game.turn() === 'w' ? 'preto' : 'branco'}`;
      } else if (game.in_stalemate()) {
          status = 'Empate por afogamento (Stalemate)';
      } else if (game.in_draw()) {
          status = 'Empate';
      } else if (game.in_check()) {
          status = `Xeque para o ${game.turn() === 'w' ? 'branco' : 'preto'}`;
      } else {
          status = `Turno do ${game.turn() === 'w' ? 'branco' : 'preto'}`;
      }
      statusEl.textContent = status;
  }

  function askPromotion(from, to, callback) {
      promotionDialog.style.display = 'block';
      promotionCallback = (piece) => {
          promotionDialog.style.display = 'none';
          callback(piece);
      };
  }

  promotionDialog.querySelectorAll('button').forEach(btn => {
      btn.addEventListener('click', () => {
          if (promotionCallback) {
              promotionCallback(btn.getAttribute('data-piece'));
              // The render() call is handled by the promotion callback in canvas click
          }
      });
  });

  canvas.addEventListener('click', function (e) {
      // Clear temporary highlights first to ensure player interaction is fresh
      tempHighlightedSquares = [];

      const x = Math.floor(e.offsetX / squareSize);
      const y = Math.floor(e.offsetY / squareSize);
      const square = String.fromCharCode('a'.charCodeAt(0) + x) + (8 - y);
      const piece = game.get(square);

      if (selectedSquare) {
          const from = String.fromCharCode('a'.charCodeAt(0) + selectedSquare[0]) + (8 - selectedSquare[1]);

          // If clicking a piece of the same color, change selection
          if (piece && piece.color === game.turn()) {
              selectedSquare = [x, y];
              validMoves = game.moves({ square: square, verbose: true });
          } else {
              // Attempt to move to the clicked square
              const isPromotionMove = game.moves({ square: from, verbose: true }).some(m => m.to === square && m.promotion);
              if (isPromotionMove) {
                  askPromotion(from, square, (promotionPiece) => {
                      const move = game.move({ from: from, to: square, promotion: promotionPiece });
                      if (move) {
                          selectedSquare = null;
                          validMoves = [];
                          render(); // Re-render after successful promotion move
                      } else {
                          // Handle invalid promotion move (e.g., if somehow promotionPiece is wrong)
                          console.log('Invalid promotion move attempt.');
                          selectedSquare = null; // Clear selection
                          validMoves = [];
                          render(); // Re-render to clear dots
                      }
                  });
              } else {
                  const move = game.move({ from: from, to: square });
                  if (move) {
                      selectedSquare = null;
                      validMoves = [];
                      // If it's a valid move, after making the move, it's the next player's turn.
                      // We might want to check for check/checkmate here before rendering.
                  } else {
                      // If invalid move, clear selection
                      selectedSquare = null;
                      validMoves = [];
                  }
              }
          }
      } else {
          // No piece selected, try to select one
          if (piece && piece.color === game.turn()) {
              selectedSquare = [x, y];
              validMoves = game.moves({ square: square, verbose: true });
          }
      }
      render(); // Always render after a click to update the board state visually
  });


  // --- MAIN RENDER FUNCTION ---
  // This is the core function that redraws everything.
  function render() {
      drawBoard();
      drawMoveDots();
      drawPieces();
      updateStatus();
  }

  // --- Expose Global Functions for learn-chess.php ---
  // We'll expose the game object directly, and a function to trigger render
  // and a function to highlight specific squares for learning.
  window.chessGame = game; // Exposes the chess.js game object
  window.renderChessBoard = render; // Exposes the render function

  /**
   * Sets the board to a specific FEN position for learning purposes.
   * Disables player interaction temporarily by clearing selection.
   * @param {string} fen - The FEN string to load.
   */
  window.setBoardToLearnFEN = function(fen) {
      game.load(fen); // Load the FEN into the chess.js game object
      selectedSquare = null; // Clear any player selection
      validMoves = []; // Clear any player move dots
      tempHighlightedSquares = []; // Ensure previous highlights are cleared before new ones
      render(); // Render the board with the new FEN
      console.log('Tabuleiro de aprendizado configurado para FEN:', fen);
  };

  /**
   * Highlights specific squares on the board for learning purposes.
   * Does not affect game logic, only visual.
   * @param {string[]} squares - An array of square names (e.g., ['e4', 'f5']).
   */
  window.highlightSquaresForLearn = function(squares) {
      tempHighlightedSquares = squares; // Set the temporary highlights
      render(); // Re-render to show highlights
  };

  /**
   * Clears all learning-specific highlights from the board.
   */
  window.clearLearnHighlights = function() {
      tempHighlightedSquares = [];
      render(); // Re-render to clear highlights
  };


  // --- Initial Game Setup ---
  // Load images and then render the initial board state.
  // This will be called when chess.php is included.
  loadImagesPromise = loadPieceImages(); // Start loading images
  loadImagesPromise.then(() => {
      console.log('Todas as imagens das peças carregadas.');
      render(); // Initial render after images are loaded
  }).catch(error => {
      console.error('Falha ao carregar imagens das peças:', error);
      render(); // Still try to render with fallbacks if images fail
  });

</script>
</body>
</html>









