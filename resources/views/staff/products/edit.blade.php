@extends('layouts.staff')

@section('title', 'Edit Product')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Edit Product</h3>
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

<form method="POST" action="{{ route('staff.products.update', $product) }}" enctype="multipart/form-data" class="card p-3">
  @csrf
  @method('PUT')

  <div class="row g-3">
    <div class="col-md-8">
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input class="form-control" name="name" value="{{ old('name', $product->name) }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Price</label>
        <input class="form-control" type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price) }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Replace Image</label>
        <input class="form-control" type="file" name="image" accept="image/*">
        <div class="form-text">Upload to replace current image.</div>
      </div>

      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
        <label class="form-check-label" for="active">Active</label>
      </div>

      @if($product->image_path)
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image">
          <label class="form-check-label" for="remove_image">Remove current image</label>
        </div>
      @endif

      <div class="d-flex gap-2">
        <button class="btn btn-primary" type="submit">Save</button>
        <a class="btn btn-outline-secondary" href="{{ route('staff.products.index') }}">Cancel</a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="border rounded p-2">
        <div class="small text-muted mb-2">Preview</div>
        @if($product->image_path)
          <img src="{{ asset('storage/'.$product->image_path) }}" class="img-fluid rounded" style="object-fit:cover; width:100%; max-height:280px;">
        @else
          <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="height:280px;">
            <span class="text-muted">No image</span>
          </div>
        @endif
      </div>
    </div>
  </div>
</form>
@endsection
