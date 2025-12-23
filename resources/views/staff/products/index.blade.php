@extends('layouts.staff')

@section('title', 'Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Products</h3>
  <a class="btn btn-primary" href="{{ route('staff.products.create') }}">+ Add Product</a>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th style="width:90px">Image</th>
          <th>Name</th>
          <th class="text-end" style="width:140px">Price</th>
          <th style="width:120px">Active</th>
          <th style="width:180px"></th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $p)
          <tr>
            <td>
              @if($p->image_path)
                <img src="{{ asset('storage/'.$p->image_path) }}" class="rounded" style="width:72px;height:72px;object-fit:cover;">
              @else
                <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="width:72px;height:72px;">
                  <span class="text-muted small">No image</span>
                </div>
              @endif
            </td>
            <td>
              <div class="fw-semibold">{{ $p->name }}</div>
              <div class="small text-muted">ID: {{ $p->id }}</div>
            </td>
            <td class="text-end">{{ number_format($p->price, 2) }}</td>
            <td>
              @if($p->is_active)
                <span class="badge text-bg-success">ACTIVE</span>
              @else
                <span class="badge text-bg-secondary">INACTIVE</span>
              @endif
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="{{ route('staff.products.edit', $p) }}">Edit</a>
              <form class="d-inline" method="POST" action="{{ route('staff.products.destroy', $p) }}"
                    onsubmit="return confirm('ลบสินค้านี้?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted py-4">No products</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">
  {{ $products->links() }}
</div>
@endsection
