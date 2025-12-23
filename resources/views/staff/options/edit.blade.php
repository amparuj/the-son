@extends('layouts.staff')

@section('title', 'Edit Option')

@section('content')
<div class="container py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">แก้ไข Option: {{ $option->name }}</h4>
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('staff.options.index') }}">กลับ</a>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('staff.options.update', $option->id) }}">
        @csrf
        @method('PUT')
        @php
  $isEdit = isset($option);
@endphp

<div class="mb-3">
  <label class="form-label">ชื่อ Option</label>
  <input type="text" name="name" class="form-control"
         value="{{ old('name', $isEdit ? $option->name : '') }}"
         placeholder="เช่น คะน้า / ถั่วงอก / เลือดไก่" required>
  @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label class="form-label">Base ราคา</label>
  <input type="number" step="0.01" min="0" name="base_price" class="form-control"
         value="{{ old('base_price', $isEdit ? $option->base_price : 0) }}">
  @error('base_price')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  <div class="text-muted small mt-1">ถ้าเมนูไหนคิดราคาไม่เท่ากัน ให้ไป override ที่ Product → Options</div>
</div>

<div class="mb-3">
  <label class="form-label">Sort</label>
  <input type="number" min="0" name="sort" class="form-control"
         value="{{ old('sort', $isEdit ? $option->sort : 0) }}">
  @error('sort')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>

<div class="form-check mb-3">
  <input class="form-check-input" type="checkbox" name="is_active" value="1"
         id="is_active"
         {{ old('is_active', $isEdit ? ($option->is_active ? 1 : 0) : 1) ? 'checked' : '' }}>
  <label class="form-check-label" for="is_active">
    เปิดใช้งาน (Active)
  </label>
  @error('is_active')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>

        <button class="btn btn-primary w-100">บันทึก</button>
      </form>
    </div>
  </div>
</div>
@endsection
