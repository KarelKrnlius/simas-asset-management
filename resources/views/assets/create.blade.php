<!DOCTYPE html>
<html>
<head>
<title>Tambah Asset</title>
</head>

<body style="background:#f4f4f4; padding:30px;">

@php
$title = "Tambah Asset";

$action = "/assets";

$fields = [
  ["label" => "Nama Barang", "name" => "nama", "type" => "text"],
  ["label" => "Kategori", "name" => "kategori", "type" => "text"],
  ["label" => "Kondisi", "name" => "kondisi", "type" => "text"],
  ["label" => "Lokasi", "name" => "lokasi", "type" => "text"]
];
@endphp

@include('components.form')

</body>
</html>