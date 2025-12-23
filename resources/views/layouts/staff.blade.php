<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Staff')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="{{ route('staff.orders.dashboard') }}">Restaurant POS</a>
    <div class="navbar-nav ms-3">
      <a class="nav-link text-white-50" href="{{ route('staff.monitor.submissions', ['status' => 'OPEN']) }}">Monitor</a>
    </div>

    <div class="ms-auto d-flex align-items-center gap-2">
      <span class="text-white-50 small">Logged in as {{ auth()->user()->name }}</span>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn btn-sm btn-outline-light" type="submit">Logout</button>
      </form>
    </div>
  </div>
</nav>

<main class="container py-4">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">มีข้อผิดพลาด</div>
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
