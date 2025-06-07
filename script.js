 // script.js

// --- Variáveis Globais e Configurações ---
let canvas;
let ctx;
let boardSize = 400; // Tamanho do tabuleiro (largura e altura do canvas)
let squareSize; // Tamanho de cada casa
const pieces = {}; // Objeto para armazenar as imagens das peças carregadas

// Mapeamento de FEN para o caminho da imagem da peça
const pieceMap = {
    'k': 'bk', 'q': 'bq', 'r': 'br', 'b': 'bb', 'n': 'bn', 'p': 'bp',
    'K': 'wk', 'Q': 'wq', 'R': 'wr', 'B': 'wb', 'N': 'wn', 'P': 'wp'
};

// Posição FEN atual do tabuleiro
let currentFEN = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1'; // Posição inicial padrão

// Array para armazenar as casas atualmente destacadas
let highlightedSquares = [];
const highlightColor = 'rgba(255, 255, 0, 0.4)'; // Amarelo semi-transparente para destaque

// --- Funções Auxiliares para Carregar Imagens ---
function loadPieceImages() {
    return new Promise(resolve => {
        let loadedCount = 0;
        const totalPieces = Object.keys(pieceMap).length;

        Object.entries(pieceMap).forEach(([fenChar, imgName]) => {
            const img = new Image();
            img.src = `./images/pieces/${imgName}.png`; // Ajuste o caminho da imagem se necessário
            img.onload = () => {
                pieces[fenChar] = img;
                loadedCount++;
                if (loadedCount === totalPieces) {
                    resolve(); // Todas as imagens foram carregadas
                }
            };
            img.onerror = () => {
                console.error(`Falha ao carregar imagem: ${img.src}`);
                loadedCount++; // Ainda assim, conte como carregado para não travar
                if (loadedCount === totalPieces) {
                    resolve();
                }
            };
        });
    });
}


// --- Funções de Desenho do Tabuleiro e Peças ---

// Desenha o tabuleiro vazio (quadrados)
function drawBoard() {
    if (!ctx) return;
    squareSize = boardSize / 8;

    for (let row = 0; row < 8; row++) {
        for (let col = 0; col < 8; col++) {
            // Casas claras e escuras
            const isLightSquare = (row + col) % 2 === 0;
            ctx.fillStyle = isLightSquare ? '#f0d9b5' : '#b58863'; // Cores padrão de tabuleiro
            ctx.fillRect(col * squareSize, row * squareSize, squareSize, squareSize);
        }
    }
}

// Desenha as peças com base no FEN atual
function drawPieces() {
    if (!ctx || Object.keys(pieces).length === 0) return;

    // Apenas a parte da posição do FEN (antes do primeiro espaço)
    const boardFen = currentFEN.split(' ')[0];
    let row = 0;
    let col = 0;

    for (let i = 0; i < boardFen.length; i++) {
        const char = boardFen[i];
        if (char === '/') {
            row++;
            col = 0;
        } else if (/\d/.test(char)) { // Se for um número (casas vazias)
            col += parseInt(char, 10);
        } else { // Se for uma peça
            const pieceImg = pieces[char];
            if (pieceImg) {
                ctx.drawImage(pieceImg, col * squareSize, row * squareSize, squareSize, squareSize);
            } else {
                console.warn(`Imagem para a peça "${char}" não carregada ou não encontrada.`);
            }
            col++;
        }
        if (col >= 8) { // Proteção para caso de FEN malformado, embora / já faça isso
            col = 0;
            row++;
        }
    }
}

// --- Funções de Manipulação do Tabuleiro (API para learn-chess.php) ---

/**
 * Define a posição das peças no tabuleiro usando uma string FEN.
 * Redesenha o tabuleiro e as peças.
 * @param {string} fen - A string FEN da nova posição.
 */
function setBoardPosition(fen) {
    currentFEN = fen;
    drawBoard(); // Redesenha as casas
    drawPieces(); // Redesenha as peças
    clearHighlights(); // Limpa quaisquer destaques anteriores
    console.log('Tabuleiro configurado para FEN:', fen);
}

/**
 * Destaca um array de casas no tabuleiro.
 * Limpa destaques anteriores automaticamente.
 * @param {string[]} squares - Um array de strings de casas (ex: ['e4', 'f5', 'g7']).
 */
function highlightSquares(squares) {
    clearHighlights(); // Limpa destaques anteriores
    highlightedSquares = squares; // Armazena as novas casas a serem destacadas

    if (!ctx) return;

    ctx.globalAlpha = 1.0; // Restaura a opacidade para desenho de destaque

    squares.forEach(square => {
        const file = square.charCodeAt(0) - 'a'.charCodeAt(0); // 'a' a 'h' -> 0 a 7
        const rank = 8 - parseInt(square[1], 10); // '1' a '8' -> 7 a 0

        if (file >= 0 && file < 8 && rank >= 0 && rank < 8) {
            ctx.fillStyle = highlightColor;
            ctx.fillRect(file * squareSize, rank * squareSize, squareSize, squareSize);
        }
    });

    // Redesenha as peças por cima dos destaques para que não fiquem apagadas
    drawPieces();
}

/**
 * Limpa todos os destaques do tabuleiro.
 */
function clearHighlights() {
    highlightedSquares = []; // Reseta o array de destaques
    drawBoard(); // Redesenha as casas para remover destaques
    drawPieces(); // Redesenha as peças
}

// --- FUNÇÃO initializeChessBoard ADICIONADA ---
/**
 * Inicializa o canvas e desenha o tabuleiro na posição FEN padrão.
 * Deve ser chamada uma vez para configurar o tabuleiro.
 */
async function initializeChessBoard() {
    canvas = document.getElementById('chessBoardCanvas');
    if (!canvas) {
        console.error('Elemento canvas com id "chessBoardCanvas" não encontrado.');
        return;
    }
    ctx = canvas.getContext('2d');

    canvas.width = boardSize;
    canvas.height = boardSize;

    await loadPieceImages(); // Garante que as imagens estejam carregadas
    setBoardPosition(currentFEN); // Desenha o tabuleiro na posição inicial
}


// --- Inicialização do Canvas e do Jogo (Chamada inicial) ---
// Removemos a lógica de inicialização daqui e a movemos para initializeChessBoard()
// document.addEventListener('DOMContentLoaded', async () => { ... });
// O DOMContentLoaded agora será tratado pelo script em learn-chess.php que chamará initializeChessBoard

// Exemplo de como você pode adicionar um listener de clique no tabuleiro
canvas.addEventListener('click', (event) => {
    // ... sua lógica de clique para movimentos ou desafios ...
    if (!canvas || !ctx || !squareSize) return; // Garante que o canvas está inicializado

    const rect = canvas.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;

    const col = Math.floor(x / squareSize);
    const row = Math.floor(y / squareSize);

    const clickedSquare = String.fromCharCode('a'.charCodeAt(0) + col) + (8 - row);
    console.log('Casa clicada:', clickedSquare);

    // Se você tiver uma função de desafio que escuta cliques, chame-a aqui
    // Ex: if (typeof handleChallengeClick === 'function') { handleChallengeClick(clickedSquare); }
});


// Exponha as funções globalmente para que learn-chess.php possa usá-las
window.initializeChessBoard = initializeChessBoard;
window.setBoardPosition = setBoardPosition;
window.highlightSquares = highlightSquares;
window.clearHighlights = clearHighlights;