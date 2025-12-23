@extends('layouts.guest')

@section('title','Login')

@section('content')
<div class="card shadow-sm">
  <div class="card-body p-4">
    <h3 class="mb-3">Login</h3>

    <form method="POST" action="{{ route('login') }}">
      @csrf

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input class="form-control" type="password" name="password" required>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="remember" id="remember">
          <label class="form-check-label" for="remember">Remember</label>
        </div>
        <a href="{{ route('register') }}" class="small">Create account</a>
      </div>

      <button class="btn btn-primary w-100" type="submit">Login</button>
    </form>
  </div>
</div>
@endsection
