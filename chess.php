<div id="status"></div>
<div id="promotionDialog">
  <div>Escolha promoção:</div>
  <button data-piece="q">Dama</button>
  <button data-piece="r">Torre</button>
  <button data-piece="b">Bispo</button>
  <button data-piece="n">Cavalo</button>
</div>
<canvas id="chessCanvas" width="480" height="480"></canvas>


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
      alert("teste")
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

 // ... (código anterior do chess.php) ...

 canvas.addEventListener('click', function (e) {
      // Clear temporary highlights first to ensure player interaction is fresh
      tempHighlightedSquares = [];

      const x = Math.floor(e.offsetX / squareSize);
      const y = Math.floor(e.offsetY / squareSize);
      const square = String.fromCharCode('a'.charCodeAt(0) + x) + (8 - y);
      const piece = game.get(square);

      // --- VERIFICA SE ESTÁ NO MODO MULTIPLAYER ---
      const isMultiplayerMode = typeof window.handleMultiplayerMove === 'function';

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
                      if (isMultiplayerMode) {
                          // EM MODO MULTIPLAYER: Envia o movimento para o servidor
                          const moveSent = window.handleMultiplayerMove(from, square, promotionPiece);
                          if (moveSent) {
                              selectedSquare = null; // Limpa seleção local se enviado
                              validMoves = [];
                          }
                          // A renderização será feita quando o servidor confirmar (moveMade)
                      } else {
                          // MODO APRENDIZADO/SINGLE PLAYER: Faz o movimento localmente
                          const move = game.move({ from: from, to: square, promotion: promotionPiece });
                          if (move) {
                              selectedSquare = null;
                              validMoves = [];
                              render(); // Re-renderiza localmente
                          } else {
                              console.log('Invalid promotion move attempt.');
                              selectedSquare = null;
                              validMoves = [];
                              render();
                          }
                      }
                  });
              } else {
                  if (isMultiplayerMode) {
                      // EM MODO MULTIPLAYER: Envia o movimento para o servidor
                      const moveSent = window.handleMultiplayerMove(from, square);
                      if (moveSent) {
                          selectedSquare = null; // Limpa seleção local se enviado
                          validMoves = [];
                      }
                      // A renderização será feita quando o servidor confirmar (moveMade)
                  } else {
                      // MODO APRENDIZADO/SINGLE PLAYER: Faz o movimento localmente
                      const move = game.move({ from: from, to: square });
                      if (move) {
                          selectedSquare = null;
                          validMoves = [];
                          // Não precisa renderizar imediatamente aqui, pois o clique anterior já chamou render
                      } else {
                          // Se inválido no single-player, limpa seleção
                          selectedSquare = null;
                          validMoves = [];
                      }
                  }
              }
          }
      } else {
          // No piece selected, try to select one
          // Em multiplayer, só pode selecionar sua própria peça e se for seu turno.
          // A validação de turno e cor da peça será feita no servidor,
          // mas podemos dar um feedback visual preventivo.
          if (isMultiplayerMode && typeof window.isMyTurn !== 'undefined' && !window.isMyTurn) {
              console.log('Não é seu turno para selecionar peças.');
              // Opcional: mostrar uma mensagem para o usuário
              // window.multiplayerStatusEl.textContent = 'Não é o seu turno!';
          } else if (isMultiplayerMode && piece && piece.color !== window.myColor) {
              console.log('Você só pode selecionar suas próprias peças.');
              // Opcional: mostrar uma mensagem para o usuário
              // window.multiplayerStatusEl.textContent = 'Essa não é sua peça!';
          } else if (piece && piece.color === game.turn()) { // Permite seleção apenas se for seu turno E sua cor
              selectedSquare = [x, y];
              validMoves = game.moves({ square: square, verbose: true });
          }
      }
      // Sempre renderiza para atualizar a seleção/pontos de movimento no cliente
      // Exceto em multiplayer, onde a renderização final é pelo 'moveMade' do servidor.
      if (!isMultiplayerMode || (isMultiplayerMode && selectedSquare)) { // Rerenderiza seleção ou dots mesmo em multiplayer
          render();
      }
  });

  // ... (restante do código do chess.php, como render(), updateStatus(), etc.) ...


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