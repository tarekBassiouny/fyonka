import axios from 'axios';

document.getElementById('loginForm')?.addEventListener('submit', async function (e) {
    e.preventDefault();

    const errorMessage = document.getElementById('errorMessage');
    errorMessage.textContent = '';

    try {
        const form = new FormData(this);
        const response = await axios.post('/login/authenticate', form);
        setTimeout(() => { window.location.href = '/'; }, 1000);
    } catch (error) {
        errorMessage.textContent = error.response?.data?.message || 'Login failed';
    }
});
