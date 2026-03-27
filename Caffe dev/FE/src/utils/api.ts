import axios from "axios";

const api = axios.create({
  baseURL: "http://localhost:3000", // Alamat Backend kamu
});

// Ini biar kalau nanti kamu simpen Token Login, otomatis dikirim ke Backend
api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default api;