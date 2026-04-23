<!DOCTYPE html>
<html>
<head>
<title>Tambah User</title>
</head>

<body style="background:#f4f4f4; padding:30px;">

@php
$title = "Tambah User";

$action = "/users";

$fields = [
  ["label" => "Nama", "name" => "nama", "type" => "text"],
  ["label" => "Email", "name" => "email", "type" => "email"],
  ["label" => "Role", "name" => "role", "type" => "text"]
];
@endphp

@include('components.form')

</body>
</html>