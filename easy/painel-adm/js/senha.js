function toggleSenha(inputId, buttonId) {
    const input = document.getElementById(inputId);
    const button = document.getElementById(buttonId);
    const novoTipo = input.getAttribute('type') === 'password' ? 'text' : 'password';
    input.setAttribute('type', novoTipo);
    // Altera o ícone conforme o tipo
    button.innerHTML = novoTipo === 'password' ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
}