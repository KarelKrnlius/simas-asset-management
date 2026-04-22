<!DOCTYPE html>
<html>
<head>
<title>Data Asset</title>
</head>

<body style="padding:30px;">

@php
$title = "Data Asset";

$headers = ["No", "Nama Barang", "Kategori", "Kondisi", "Lokasi"];

$rows = [
  [1, "Laptop", "Elektronik", "Baik", "Ruang IT"],
  [2, "Printer", "Peralatan", "Rusak", "Ruang Admin"],
  [3, "Monitor", "Elektronik", "Baik", "Lab"]
];
@endphp

@include('components.table')

</body>
</html>