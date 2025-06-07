const botao = document.querySelector(".botao-menu");
const menuLateral = document.querySelector(".menu-lateral");
const logo = document.querySelector(".logo"); // Logo também pode precisar de classe 'remove'

// Função para gerenciar a visibilidade do menu e botão com base na largura da tela
function handleMenuVisibility() {
    if (window.innerWidth < 1020) {
        // Em telas menores que 1020px
        if (botao) {
            botao.classList.add('flex');     // Mostra o botão
            botao.classList.remove('none');
        }
        menuLateral.classList.add('remove'); // Esconde o menu por padrão em mobile
        // menuLateral.classList.add('hoverable'); // Esta classe pode ser opcional se o CSS lidar com hover
        if (logo) {
            logo.innerHTML = "<img src='./images/pieces/white/knight.png' alt='Knight'>";
            logo.classList.add('remove'); // Opcional: Se o logo também deve se mover/esconder com o menu
        }
    } else {
        // Em telas maiores ou iguais a 1020px
        if (botao) {
            botao.classList.add('none');     // Esconde o botão
            botao.classList.remove('flex');
        }
        menuLateral.classList.remove('remove');    // Garante que o menu esteja visível
        // menuLateral.classList.remove('hoverable');
        if (logo) {
            logo.innerHTML = "<img src='./images/Chess.png' alt='Chess Logo'>";
            logo.classList.remove('remove'); // Opcional: Remove a classe 'remove' do logo
        }
    }
}

// Função para alternar a visibilidade da sidebar ao clicar no botão
const toggleSidebar = () => {
    // Alterne a classe 'remove' para mostrar/esconder o menu
    menuLateral.classList.toggle('remove');

    // Opcional: Se o logo também deve se mover/esconder com a sidebar
    if (logo) {
        logo.classList.toggle('remove');
    }
};

// Executa na carga inicial da página
document.addEventListener('DOMContentLoaded', handleMenuVisibility);

// Executa sempre que a janela for redimensionada
window.addEventListener('resize', handleMenuVisibility);

// Adiciona o listener de clique ao botão
if (botao) {
    botao.addEventListener("click", toggleSidebar);
}