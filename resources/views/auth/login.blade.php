<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login BRIN</title>

<style>  
* {
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:'Segoe UI',sans-serif;
}

body {
  height:100vh;
  background:#f4f4f4;
  display:flex;
  justify-content:center;
  align-items:center;
}

/* CARD */
.container {
  width:900px;
  height:500px;
  display:flex;
  background:#fff;
  border-radius:16px;
  overflow:hidden;
  box-shadow:0 10px 30px rgba(0,0,0,0.1);
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
}

.left img {
  width:140px;
  margin-bottom:20px;
  animation:float 3s ease-in-out infinite;
  filter: drop-shadow(0 0 8px rgba(255,255,255,0.8))
          drop-shadow(0 0 15px rgba(255,255,255,0.6));
}

@keyframes float {
  0%,100% {transform:translateY(0);}
  50% {transform:translateY(-8px);}
}

/* RIGHT */
.right {
  flex:1;
  padding:40px;
  display:flex;
  flex-direction:column;
  justify-content:center;
  align-items:center; /* 🔥 CENTER FIX */
}

/* 🔥 TITLE */
.title-box {
  text-align:center;
  margin-bottom:25px;
  animation:fadeSlide 0.6s ease;
}

.title-main {
  font-size:34px;
  font-weight:700;
  color:#b71c1c;
  letter-spacing:4px;
  text-shadow:0 0 8px rgba(183,28,28,0.25);
}

.divider {
  width:50px;
  height:3px;
  background:#b71c1c;
  margin:10px auto;
  border-radius:2px;
}

.title-sub {
  font-size:13px;
  color:#888;
  margin-bottom:8px;
}

.login-text {
  font-size:18px;
  color:#222;
  margin-bottom:15px;
}

/* 🔥 FORM BOX (BIAR RAPI TENGAH) */
.form-box {
  width:100%;
  max-width:320px;
}

/* INPUT */
.input-group {
  margin-bottom:15px;
  position:relative;
}

.input-group input {
  width:100%;
  padding:12px;
  border-radius:8px;
  border:1px solid #ddd;
  transition:0.2s;
}

.input-group input:focus {
  border-color:#b71c1c;
  box-shadow:0 0 0 2px rgba(183,28,28,0.15);
  outline:none;
}

/* TOGGLE */
.toggle {
  position:absolute;
  right:10px;
  top:50%;
  transform:translateY(-50%);
  cursor:pointer;
  font-size:12px;
  color:#666;
}

/* ROW */
.row-between {
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:20px;
}

.remember {
  display:flex;
  align-items:center;
  gap:6px;
  font-size:13px;
  color:#555;
}

.forgot a {
  font-size:13px;
  color:#b71c1c;
  text-decoration:none;
}

/* BUTTON */
.btn {
  width:100%;
  padding:12px;
  border:none;
  border-radius:8px;
  background:#b71c1c;
  color:white;
  cursor:pointer;
  transition:0.2s;
}

.btn:hover {
  background:#8e0000;
  transform:translateY(-2px);
}

.btn.loading {
  background:#999;
}

/* ERROR */
.error {
  background:#ffe5e5;
  color:#b71c1c;
  padding:10px;
  border-radius:8px;
  margin-bottom:10px;
  font-size:13px;
}

/* ANIMASI */
@keyframes fadeSlide {
  from {opacity:0; transform:translateY(15px);}
  to {opacity:1; transform:translateY(0);}
}
</style>
</head>

<body>

<div class="container">

  <!-- LEFT -->
  <div class="left">
    <img src="{{ asset('images/logo/logi.png') }}">
    <h2>BRIN</h2>
    <p>Badan Riset Inovasi Nasional</p>
  </div>

  <!-- RIGHT -->
  <div class="right">

    <!-- TITLE -->
    <div class="title-box">
      <h1 class="title-main">SIMAS</h1>
      <div class="divider"></div>
      <p class="title-sub">Sistem Manajemen Aset</p>
      <h2 class="login-text">Masuk ke Sistem</h2>
    </div>

    <!-- FORM -->
    <div class="form-box">

      @if ($errors->any())
        <div class="error">
          {{ $errors->first() }}
        </div>
      @endif

      <form id="loginForm" method="POST" action="{{ route('login') }}">
        @csrf

        <div class="input-group">
          <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="input-group">
          <input type="password" id="password" name="password" placeholder="Password" required>
          <span class="toggle" onclick="togglePassword()">Show</span>
        </div>

        <div class="row-between">
          <div class="remember">
            <input type="checkbox" name="remember">
            <label>Remember Me</label>
          </div>

          <div class="forgot">
            <a href="/forgot-password">Forgot Password?</a>
          </div>
        </div>

        <button type="submit" class="btn" id="btnLogin">Masuk</button>
      </form>

    </div>

  </div>

</div>

<script>
function togglePassword() {
  const pass = document.getElementById("password");
  const toggle = document.querySelector(".toggle");

  pass.type = pass.type === "password" ? "text" : "password";
  toggle.innerText = pass.type === "password" ? "Show" : "Hide";
}

document.getElementById("loginForm").addEventListener("submit", function(){
  const btn = document.getElementById("btnLogin");
  btn.classList.add("loading");
  btn.innerText = "Loading...";
});
</script>

</body>
</html>