<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password</title>

<style>
* {
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:'Segoe UI',sans-serif;
}

body {
  min-height:100vh;
  background:#f4f4f4;
  display:flex;
  justify-content:center;
  align-items:center;
}

/* CARD */
.container {
  width:90%;
  max-width:900px;
  height:500px;
  display:flex;
  background:#fff;
  border-radius:16px;
  overflow:hidden;

  /* SHADOW PREMIUM */
  box-shadow:
    0 20px 40px rgba(0,0,0,0.15),
    0 8px 16px rgba(0,0,0,0.1);

  transition:0.3s;
}

/* HOVER EFFECT */
.container:hover {
  transform: translateY(-4px);
  box-shadow:
    0 25px 50px rgba(0,0,0,0.2),
    0 12px 25px rgba(0,0,0,0.15);
}

/* RESPONSIVE */
@media (max-width:768px){
  .container {
    flex-direction:column;
    height:auto;
  }
}

/* LEFT */
.left {
  flex:1;
  background:#b71c1c;
  color:white;
  display:flex;
  flex-direction:column;
  justify-content:center;
  align-items:center;
  text-align:center;
  padding:20px;
}

.left img {
  width:140px;
  margin-bottom:20px;
  animation:float 3s ease-in-out infinite;

  /* GLOW PUTIH */
  filter: drop-shadow(0 0 8px rgba(255,255,255,0.8))
          drop-shadow(0 0 15px rgba(255,255,255,0.6));
}

.left h2 {
  margin-bottom:5px;
}

.left p {
  font-size:14px;
}

.left .sub {
  font-size:12px;
  margin-top:10px;
  opacity:0.8;
}

/* RIGHT */
.right {
  flex:1;
  padding:50px;
  display:flex;
  flex-direction:column;
  justify-content:center;
  border-right:1px solid #eee;
}

.right h2 {
  margin-bottom:5px;
  color:#333;
}

.desc {
  font-size:13px;
  color:#666;
  margin-bottom:20px;
}

/* INPUT */
.input-group {
  margin-bottom:15px;
  position:relative;
}

.input-group input {
  width:100%;
  padding:12px 12px 12px 40px;
  border-radius:8px;
  border:1px solid #ddd;
}

.input-group input:focus {
  border-color:#b71c1c;
  outline:none;
}

.icon {
  position:absolute;
  left:12px;
  top:50%;
  transform:translateY(-50%);
  color:#999;
}

/* BUTTON */
.btn {
  padding:12px;
  border:none;
  border-radius:8px;
  background:#b71c1c;
  color:white;
  cursor:pointer;
  transition:0.2s;
  margin-top:10px;
}

.btn:hover {
  background:#8e0000;
  transform:translateY(-1px);
}

.btn.loading {
  background:#999;
  cursor:not-allowed;
}

/* BACK */
.back {
  margin-top:15px;
  text-align:center;
}

.back a {
  font-size:13px;
  color:#b71c1c;
  text-decoration:none;
}

.back a:hover {
  text-decoration:underline;
}

/* ALERT */
.alert-success {
  background:#e6ffed;
  color:#2e7d32;
  padding:10px;
  border-radius:8px;
  margin-bottom:10px;
  font-size:13px;
}

/* FLOAT */
@keyframes float {
  0%,100% {transform:translateY(0);}
  50% {transform:translateY(-8px);}
}

/* SLIDE */
.slide-in {
  animation: slideIn 0.5s ease forwards;
}

.slide-out-right {
  animation: slideOutRight 0.4s ease forwards;
}

@keyframes slideIn {
  from {opacity:0; transform:translateX(-50px);}
  to {opacity:1; transform:translateX(0);}
}

@keyframes slideOutRight {
  from {opacity:1; transform:translateX(0);}
  to {opacity:0; transform:translateX(50px);}
}
</style>
</head>

<body>

<div class="container slide-in">

  <!-- FORM -->
  <div class="right">
    <h2>Forgot Password</h2>
    <p class="desc">
      Masukkan email kamu untuk mendapatkan link reset password
    </p>

    @if (session('status'))
      <div class="alert-success">
        {{ session('status') }}
      </div>
    @endif

    <form id="forgotForm" method="POST" action="/forgot-password">
      @csrf

      <div class="input-group">
        <input type="email" name="email" placeholder="Masukkan Email" required>
        <span class="icon">📧</span>
      </div>

      <button class="btn" id="btnForgot">Kirim Link Reset</button>
    </form>

    <p style="font-size:12px; color:#888; margin-top:10px;">
      *Link reset akan dikirim jika email terdaftar
    </p>

    <div class="back">
      <a href="/login" onclick="goLogin(event)">← Kembali ke Login</a>
    </div>
  </div>

  <!-- MERAH -->
  <div class="left">
    <img src="{{ asset('images/logo/logi.png') }}">
    <h2>BRIN</h2>
    <p>Badan Riset Inovasi Nasional</p>
    <p class="sub">Sistem Manajemen Aset</p>
  </div>

</div>

<script>
// ANIMASI BALIK
function goLogin(e){
  e.preventDefault();

  const container = document.querySelector(".container");
  container.classList.remove("slide-in");
  container.classList.add("slide-out-right");

  setTimeout(()=>{
    window.location.href = "/login";
  }, 400);
}

// LOADING BUTTON
document.getElementById("forgotForm").addEventListener("submit", function(){
  const btn = document.getElementById("btnForgot");
  btn.classList.add("loading");
  btn.innerText = "Loading...";
});
</script>

</body>
</html>