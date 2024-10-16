function toggleForms() {
    const loginContainer = document.getElementById('login-container');
    const registerContainer = document.getElementById('register-container');
    loginContainer.style.display = loginContainer.style.display === 'none' ? 'block' : 'none';
    registerContainer.style.display = registerContainer.style.display === 'none' ? 'block' : 'none';
}
