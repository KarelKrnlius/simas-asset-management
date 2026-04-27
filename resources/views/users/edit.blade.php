@extends('layouts.app')

@section('content')
<div class="p-6">
<form action="{{ route('users.update', $user->id) }}" method="POST" class="bg-white p-6 rounded-xl">
@csrf
@method('PUT')

<input name="name" value="{{ $user->name }}" class="border p-2 w-full mb-2">
<input name="email" value="{{ $user->email }}" class="border p-2 w-full mb-2">

<select name="role_id" class="border p-2 w-full mb-2">
<option value="1" {{ $user->role_id == 1 ? 'selected' : '' }}>Admin</option>
<option value="2" {{ $user->role_id == 2 ? 'selected' : '' }}>Staff</option>
</select>

<button class="bg-yellow-500 text-white px-4 py-2 rounded">Update</button>

</form>
</div>
@endsection