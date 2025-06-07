const form = document.getElementById("form");
const nome = document.getElementById("nomeChess");
const sobrenome = document.getElementById("sobrenomeChess");
const email = document.getElementById("emailChess");
const senha = document.getElementById("senhaChess");
const confirmarsenha = document.getElementById("confirmarChess");
const button = document.querySelector(".acessar");
let cadastro = [];
let count = cadastro.length;


form.addEventListener("submit", (event) => {
    var teste = `${nome.value}-${sobrenome.value}`;
    fetch(`https://api.chess.com/pub/player/${teste}`).then(res => res.json()).then(data =>
        console.log(data)
    );
    checkForm();
    
})
email.addEventListener("blur",() => {
checkInputEmail();
})
nome.addEventListener("blur",() => {
checkInputUsername();
})
sobrenome.addEventListener("blur",() => {
checkInputSobrenome();
})
senha.addEventListener("blur",() => {
checkInputPassword();
})
confirmarsenha.addEventListener("blur",() => {
checkInputPasswordConfirmation();
})
function checkInputUsername() {
    const usernameValue = nome.value;
    if (usernameValue === "") {
        errorInput(nome, "Preencha o nome.")
    } else {
        const formItem = nome.parentElement;
        formItem.className = "form-content";
    }
}
function checkInputSobrenome() {
    const sobrenomeValue = sobrenome.value;
    if (sobrenomeValue === "") {
        errorInput(sobrenome, "Preencha o nome.")
    } else {
        const formItem = sobrenome.parentElement;
        formItem.className = "form-content";
    }
}
function checkInputEmail() {
    const emailValue = email.value;
    if (emailValue === "") {
        errorInput(email, "O e-mail é obrigatorio.")
    } else {
        const formItem = email.parentElement;
        formItem.className = "form-content";
    }
}
function checkInputPassword() {
    const senhaValue = senha.value;
    if (senhaValue === "") {
        errorInput(senha, "A senha é obrigatoria.")
    } else if (senhaValue.length < 6){
        errorInput(senha, "A senha  precisa ter no mínimo 6 caracteres")
    } else {
        const formItem = senha.parentElement;
        formItem.className = "form-content";
    }
}
function checkInputPasswordConfirmation() {
    const senhaValue = senha.value;
    const confirmationSenhaValue = confirmarsenha.value
  if(confirmationSenhaValue === ""){
    errorInput(confirmarsenha , "A confirmação de senha é obrigatoria.");
  }
  else if(confirmationSenhaValue !== senhaValue){
    errorInput(confirmarsenha,"As senha são diferentes.")
  } else {
    const formItem = confirmarsenha.parentElement;
    formItem.className = "form-content";
  }
}
function checkForm(){
    checkInputUsername();
    checkInputSobrenome();
    checkInputEmail();
    checkInputPassword();
    checkInputPasswordConfirmation();
    const formItems = form.querySelectorAll(".form-content");
    const isValid = [...formItems].every( (item) => {
        return item.className === "form-content";
    });
    if(isValid){

    }
}
function errorInput(input, message) {
    const formItem = input.parentElement;

    const textMessage = formItem.querySelector("a");
    textMessage.innerText = message;
    formItem.className = "form-content error";
}
// ira receber o ultimo cadastro para jogar
//function painelCadastro(){ }
// function cadastro ira fazer a funcao de guardar dadoa e iniciar o painel cadastro que recebe o novo cadastro e ativa o 
//function Cadastro(){ painelCadastro(); }
