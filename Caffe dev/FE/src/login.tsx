import { useState } from 'react';
import api from './utils/api';

function Login() {
  const [isLogin, setIsLogin] = useState(true);
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [message, setMessage] = useState('');
const handleSubmit = async (e: React.FormEvent) => {
  e.preventDefault();
  setMessage('Tunggu sebentar...');
  
  try {
    // KARENA REGISTER GAK ADA, KITA PAKSA LOGIN AJA DULU
    const endpoint = '/auth/login'; 
    
    const res = await api.post(endpoint, { email, password });
    
    // Sesuaikan dengan struktur Swagger kamu: res.data.data.token
    const token = res.data.data?.token;

    if (token) {
      localStorage.setItem('token', token);
      setMessage('Login berhasil! Token tersimpan.');
      // Di sini nanti bisa kamu arahkan ke Dashboard
    } else {
      setMessage('Login sukses, tapi token gak ketemu.');
    }

  } catch (error: any) {
    // Ambil pesan error dari struktur: error.response.data.message
    const pesanError = error.response?.data?.message || "Password kependekan atau format email salah!";
    setMessage("Gagal: " + pesanError);
  }
};

  return (
    <div style={{ maxWidth: '400px', margin: '50px auto', padding: '20px', border: '1px solid #ccc', fontFamily: 'sans-serif' }}>
      <h2>{isLogin ? 'Login Pegawai' : 'Register Pegawai'}</h2>
      
      <form onSubmit={handleSubmit} style={{ display: 'flex', flexDirection: 'column', gap: '15px' }}>
        <input 
          type="email" 
          placeholder="Masukkan Email" 
          value={email} 
          onChange={(e) => setEmail(e.target.value)} 
          required 
          style={{ padding: '10px' }}
        />
        <input 
          type="password" 
          placeholder="Masukkan Password" 
          value={password} 
          onChange={(e) => setPassword(e.target.value)} 
          required 
          style={{ padding: '10px' }}
        />
        <button type="submit" style={{ padding: '10px', background: '#333', color: '#fff', cursor: 'pointer' }}>
          {isLogin ? 'Masuk' : 'Daftar'}
        </button>
      </form>

      <p style={{ color: 'red', marginTop: '10px' }}>{message}</p>

      <button onClick={() => setIsLogin(!isLogin)} style={{ background: 'none', border: 'none', color: 'blue', cursor: 'pointer', marginTop: '10px', padding: '0' }}>
        {isLogin ? 'Belum punya akun? Bikin dulu (Register)' : 'Sudah punya akun? Langsung Login'}
      </button>
    </div>
  );
}

export default Login;