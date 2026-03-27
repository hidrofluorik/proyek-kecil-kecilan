const loginForm = document.getElementById('loginForm');
const messageElement = document.getElementById('message');

loginForm.addEventListener('submit', async (e) => {
    e.preventDefault(); // Mencegah halaman refresh

    // 1. Ambil data dari input
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    try {
        // 2. Kirim data ke Backend temanmu (authRouter.post)
        const response = await fetch('http://localhost:3000/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });

        const result = await response.json();

        if (response.ok) {
            // 3. Jika berhasil, simpan TOKEN yang dibuat oleh loginController
            localStorage.setItem('token', result.data.token);
            alert('Login Berhasil!');
            window.location.href = 'dashboard.html'; // Pindah halaman
        } else {
            // 4. Jika gagal (misal: "password salah"), tampilkan pesan dari backend
            messageElement.textContent = result.message;
        }
    } catch (error) {
        messageElement.textContent = "Gagal terhubung ke server backend.";
    }
});