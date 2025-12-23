@extends('layouts.staff')

@section('title', 'Add Product')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Add Product</h3>
  <a class="btn btn-outline-secondary" href="{{ route('staff.products.index') }}">Back</a>
</div>

@if($errors->any())
  <div class="alert alert-danger">
    <div class="fw-semibold mb-1">Please fix the following:</div>
    <ul class="mb-0">
      @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('staff.products.store') }}" enctype="multipart/form-data" class="card p-3">
  @csrf
  <div class="mb-3">
    <label class="form-label">Name</label>
    <input class="form-control" name="name" value="{{ old('name') }}" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Price</label>
    <input class="form-control" type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Image</label>
    <input class="form-control" type="file" name="image" accept="image/*">
    <div class="form-text">PNG/JPG/WEBP, max 4MB</div>
  </div>

  <div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active" {{ old('is_active', 1) ? 'checked' : '' }}>
    <label class="form-check-label" for="active">Active</label>
  </div>

  <div class="d-flex gap-2">
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn btn-outline-secondary" href="{{ route('staff.products.index') }}">Cancel</a>
  </div>
</form>
@endsection
