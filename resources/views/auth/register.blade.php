@extends('layouts.guest')

@section('title','Register')

@section('content')
<div class="card shadow-sm">
  <div class="card-body p-4">
    <h3 class="mb-3">Register</h3>

    <form method="POST" action="{{ route('register') }}">
      @csrf

      <div class="mb-3">
        <label class="form-label">Name</label>
        <input class="form-control" name="name" value="{{ old('name') }}" required autofocus>
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" value="{{ old('email') }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input class="form-control" type="password" name="password" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input class="form-control" type="password" name="password_confirmation" required>
      </div>

      <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('login') }}" class="small">Already registered?</a>
        <button class="btn btn-primary" type="submit">Register</button>
      </div>
    </form>
  </div>
</div>
@endsection
