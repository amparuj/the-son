@extends('layouts.staff')

@section('title', 'Edit Option Group')

@section('content')
<div class="container py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">แก้ไข Group: {{ $group->name }}</h4>
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('staff.option-groups.index') }}">กลับ</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{ session('success') }</div>
  @endif

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('staff.option-groups.update', $group->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
  <label class="form-label">ชื่อกลุ่ม</label>
  <input type="text" name="name" class="form-control"
         value="{{ old('name', $group->name ?? '') }}"
         placeholder="เช่น ผัก / ท็อปปิ้ง / เครื่องปรุง" required>
  @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label class="form-label">Sort</label>
  <input type="number" min="0" name="sort" class="form-control"
         value="{{ old('sort', $group->sort ?? 0) }}">
  @error('sort')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>

<div class="mb-2 fw-semibold">เลือก Options ในกลุ่มนี้</div>
<div class="row">
  @foreach($options as $opt)
    <div class="col-12 col-md-6 col-lg-4">
      <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" name="option_ids[]"
               value="{{ $opt->id }}"
               id="opt_{{ $opt->id }}"
               {{ in_array($opt->id, $selected ?? []) ? 'checked' : '' }}>
        <label class="form-check-label" for="opt_{{ $opt->id }}">
          {{ $opt->name }}
          @if((float)$opt->base_price > 0)
            <span class="text-muted">(+{{ number_format((float)$opt->base_price, 2) }})</span>
          @endif
        </label>
      </div>
    </div>
  @endforeach
</div>


        <button class="btn btn-primary w-100 mt-3">บันทึก</button>
      </form>
    </div>
  </div>
</div>
@endsection
