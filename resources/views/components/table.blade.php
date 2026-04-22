<div class="card">

  <!-- HEADER -->
  <div class="header">
    <h2>{{ $title }}</h2>
    <button class="btn-add" onclick="openModal()">+ Tambah</button>
  </div>

  <!-- SEARCH -->
  <input type="text" id="searchInput" placeholder="Cari data..." class="search">

  <!-- TABLE -->
  <div class="table-wrapper">
    <table id="dataTable">
      <thead>
        <tr>
          @foreach ($headers as $header)
            <th>{{ $header }}</th>
          @endforeach
          <th>Aksi</th>
        </tr>
      </thead>

      <tbody id="tableBody">
        @foreach ($rows as $row)
        <tr>
          @foreach ($row as $cell)
            <td>{{ $cell }}</td>
          @endforeach
          <td>
            <button class="btn edit" onclick="editRow(this)">Edit</button>
            <button class="btn delete" onclick="deleteRow(this)">Hapus</button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

</div>

<!-- MODAL -->
<div id="modal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h3 id="modalTitle">Tambah Data</h3>

    <form id="formData">
      @foreach ($headers as $header)
        @if($header != 'No')
          <input type="text" placeholder="{{ $header }}" class="input-modal">
        @endif
      @endforeach

      <button type="submit" class="btn-add">Simpan</button>
    </form>
  </div>
</div>

<style>
html, body {
  height:auto;
  overflow:auto;
  font-family:'Segoe UI';
  background:#f4f4f4;
}

.card {
  background:#fff;
  padding:20px;
  border-radius:12px;
  box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

.header {
  display:flex;
  justify-content:space-between;
  margin-bottom:15px;
}

.btn-add {
  background:#b71c1c;
  color:white;
  padding:8px 12px;
  border:none;
  border-radius:8px;
  cursor:pointer;
}

.btn-add:hover {
  background:#8e0000;
}

.search {
  width:100%;
  padding:10px;
  margin-bottom:15px;
  border-radius:8px;
  border:1px solid #ddd;
}

.table-wrapper {
  overflow:visible;
}

table {
  width:100%;
  border-collapse:collapse;
}

th {
  background:#b71c1c;
  color:white;
  padding:10px;
}

td {
  padding:10px;
  border-bottom:1px solid #eee;
}

.btn {
  padding:5px 10px;
  border:none;
  border-radius:6px;
  cursor:pointer;
}

.edit { background:#1976d2; color:white; }
.delete { background:#d32f2f; color:white; }

/* 🔥 FIX MODAL */
.modal {
  display:none;
  position:fixed;
  top:0;
  left:0;
  width:100%;
  height:100%;
  background:rgba(0,0,0,0.4);

  justify-content:center;
  align-items:center;

  overflow-y:auto;   /* penting */
  padding:20px;
}

.modal-content {
  background:white;
  padding:20px;
  border-radius:12px;
  width:350px;

  max-height:90vh;   /* biar gak kepotong */
  overflow-y:auto;

  animation:popup 0.3s ease;
}

@keyframes popup {
  from {transform:scale(0.9); opacity:0;}
  to {transform:scale(1); opacity:1;}
}

.close {
  float:right;
  cursor:pointer;
  font-size:18px;
}

.input-modal {
  width:100%;
  padding:10px;
  margin-bottom:10px;
}
</style>

<script>
let editTarget = null;

// SEARCH
document.getElementById("searchInput").addEventListener("keyup", function() {
  let filter = this.value.toLowerCase();
  let rows = document.querySelectorAll("#tableBody tr");

  rows.forEach(row => {
    row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
  });
});

// MODAL
function openModal() {
  editTarget = null;
  document.getElementById("modalTitle").innerText = "Tambah Data";
  document.getElementById("modal").style.display = "flex";
}

function closeModal() {
  document.getElementById("modal").style.display = "none";
}

// SUBMIT
document.getElementById("formData").addEventListener("submit", function(e) {
  e.preventDefault();

  let inputs = document.querySelectorAll(".input-modal");
  let values = [];

  inputs.forEach(input => values.push(input.value));

  if (editTarget) {
    let cells = editTarget.querySelectorAll("td");
    values.forEach((val, i) => {
      cells[i+1].innerText = val;
    });
  } else {
    let table = document.getElementById("tableBody");

    let tr = document.createElement("tr");

    let tdNo = document.createElement("td");
    tdNo.innerText = table.rows.length + 1;
    tr.appendChild(tdNo);

    values.forEach(val => {
      let td = document.createElement("td");
      td.innerText = val;
      tr.appendChild(td);
    });

    let tdAksi = document.createElement("td");
    tdAksi.innerHTML = `
      <button class="btn edit" onclick="editRow(this)">Edit</button>
      <button class="btn delete" onclick="deleteRow(this)">Hapus</button>
    `;
    tr.appendChild(tdAksi);

    table.appendChild(tr);

    tr.scrollIntoView({ behavior: "smooth" });
  }

  this.reset();
  closeModal();
});

// EDIT
function editRow(btn) {
  editTarget = btn.closest("tr");
  let cells = editTarget.querySelectorAll("td");
  let inputs = document.querySelectorAll(".input-modal");

  inputs.forEach((input, i) => {
    input.value = cells[i+1].innerText;
  });

  document.getElementById("modalTitle").innerText = "Edit Data";
  document.getElementById("modal").style.display = "flex";
}

// DELETE
function deleteRow(btn) {
  if (confirm("Yakin hapus data?")) {
    btn.closest("tr").remove();
  }
}

// klik luar modal
window.onclick = function(e) {
  let modal = document.getElementById("modal");
  if (e.target == modal) modal.style.display = "none";
}
</script>