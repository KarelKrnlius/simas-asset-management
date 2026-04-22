<div class="card">

  <h2 style="margin-bottom:20px;">{{ $title }}</h2>

  <form method="POST" action="{{ $action }}">
    @csrf

    @foreach ($fields as $field)
      <div class="form-group">
        <label>{{ $field['label'] }}</label>

        <input 
          type="{{ $field['type'] }}" 
          name="{{ $field['name'] }}" 
          placeholder="{{ $field['label'] }}"
          required
        >
      </div>
    @endforeach

    <button type="submit" class="btn-submit">Simpan</button>
  </form>

</div>

<style>
.card {
  background:#fff;
  padding:25px;
  border-radius:12px;
  box-shadow:0 5px 15px rgba(0,0,0,0.1);
  max-width:500px;
  margin:auto;
  animation:fadeIn 0.5s ease;
}

@keyframes fadeIn {
  from {opacity:0; transform:translateY(10px);}
  to {opacity:1; transform:translateY(0);}
}

.form-group {
  margin-bottom:15px;
}

label {
  display:block;
  margin-bottom:5px;
  font-size:14px;
}

input {
  width:100%;
  padding:10px;
  border-radius:8px;
  border:1px solid #ddd;
}

input:focus {
  border-color:#b71c1c;
  outline:none;
}

.btn-submit {
  width:100%;
  padding:12px;
  background:#b71c1c;
  color:white;
  border:none;
  border-radius:8px;
  cursor:pointer;
}

.btn-submit:hover {
  background:#8e0000;
}
</style>