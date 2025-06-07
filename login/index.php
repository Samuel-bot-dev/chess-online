<?php
if(isset($_POST['acessar']))
{
    include_once('config.php');
    $nome = $_POST['nomeChess'];
    $sobrenome = $_POST['sobrenomeChess'];
    $email = $_POST['emailChess'];
    $senha = $_POST['senhaChess'];
    $confirmarsenha = $_POST['confirmarChess'];
    $result = mysqli_query($conexao,"INSERT INTO login_chess(nome,sobrenome,email,senha,confirmacao)  
    VALUES('$nome','$sobrenome','$email','$senha','$confirmarsenha')");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="../root.css">
</head>
<body>
        <div class="conta">
            <section class="header"><h2>Cadastro</h2></section>
            <form id="form" class="form" action="index.php" method="post">
    
                <div class="form-content">
                    <label for="nomeChess">Nome de Usuario</label>
                    <input type="text" name="nomeChess" id="nomeChess" class="nomeChess" placeholder="Digite o nome de usuario...">
                    <a>Preencha o usuario</a>
                </div>
                <div class="form-content">
                    <label for="sobrenomeChess">Sobrenome</label>
                    <input type="text" name="sobrenomeChess" id="sobrenomeChess" class="sobrenomeChess" placeholder="Digite o sobrenome...">
                    <a>Preencha o sobrenome</a>
                </div>
                <div class="form-content">
                    <label for="emailChess">E-mail</label>
                    <input type="email" name="emailChess" id="emailChess" class="emailChess" placeholder="Digite o e-mail...">
                    <a>"O E-mail é obrigatorio</a>
                </div>
                <div class="form-content">
                    <label for="senhaChess">Senha</label>
                    <input type="password" name="senhaChess" id="senhaChess" class="senhaChess" placeholder="Digite a senha...">
                    <a>As senha são diferentes</a>
                </div> 
                <div class="form-content">
                    <label for="confirmarChess">Confirmação de Senha</label>
                    <input type="password" name="confirmarChess" id="confirmarChess" class="confirmarChess" placeholder="Digite a senha...">
                    <a>Preencha a Confirmação</a>
                </div> 
                <button type="submit" name="acessar" class="acessar">Cadastrar</button>
            </form>
            
        </div>
        <script src="./app.js"></script>
</body>
</html>