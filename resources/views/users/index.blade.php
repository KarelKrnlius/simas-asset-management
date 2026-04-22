<!DOCTYPE html>
<html>
<head>
<title>Data User</title>
</head>

<body style="padding:30px;">

@php
$title = "Data User";

$headers = ["No", "Nama", "Email", "Role"];

$rows = [
  [1, "Aidil", "aidil@gmail.com", "Admin"],
  [2, "Budi", "budi@gmail.com", "Staff"],
  [3, "Siti", "siti@gmail.com", "User"]
];
@endphp

@include('components.table')

