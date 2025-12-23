@extends('layouts.staff')

@section('title', 'Product Options')

@section('content')
<div class="container py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">Options ของสินค้า: {{ $product->name }}</h4>
      <div class="text-muted small">ติ๊ก “ใช้” เพื่อเปิด option ให้สินค้านี้ และตั้งค่า override ได้</div>
    </div>
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('staff.products.index') }}">กลับไปสินค้า</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if($groups->count() === 0)
    <div class="alert alert-warning">
      ยังไม่มี Option Groups/Options ในระบบ
      <div class="small text-muted mt-1">
        แนะนำ: รัน seeder OptionSeeder (อยู่ในแพ็กนี้) เพื่อสร้างตัวเลือกเริ่มต้น
      </div>
    </div>
  @endif

  <form method="POST" action="{{ route('staff.products.options.update', $product->id) }}">
    @csrf

    @foreach($groups as $group)
      <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>{{ $group->name }}</strong>
          <small class="text-muted">แสดงเฉพาะ options ที่ active</small>
        </div>

        <div class="card-body">
          @if($group->options->count() === 0)
            <div class="text-muted">กลุ่มนี้ยังไม่มี options</div>
          @else
            <div class="table-responsive">
              <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th style="width:70px;">ใช้</th>
                    <th>Option</th>
                    <th style="width:110px;">Allowed</th>
                    <th style="width:110px;">Default</th>
                    <th style="width:170px;">ราคา override</th>
                    <th style="width:140px;">Max qty</th>
                    <th style="width:120px;">Sort</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($group->options as $opt)
                    @php
                      $p = $attached->get($opt->id);
                      $enabled = $p !== null;

                      $isAllowed = $enabled ? (bool)$p->pivot->is_allowed : true;
                      $isDefault = $enabled ? (bool)$p->pivot->is_default : false;
                      $priceOverride = $enabled ? $p->pivot->price_override : null;
                      $maxQty = $enabled ? $p->pivot->max_qty : null;
                      $sort = $enabled ? $p->pivot->sort : 0;
                    @endphp

                    <tr>
                      <td>
                        <input class="form-check-input"
                          type="checkbox"
                          name="options[{{ $opt->id }}][enabled]"
                          value="1"
                          {{ $enabled ? 'checked' : '' }}>
                      </td>

                      <td>
                        <div class="fw-semibold">{{ $opt->name }}</div>
                        <div class="text-muted small">base: {{ number_format($opt->base_price, 2) }}</div>
                      </td>

                      <td>
                        <input class="form-check-input"
                          type="checkbox"
                          name="options[{{ $opt->id }}][is_allowed]"
                          value="1"
                          {{ $isAllowed ? 'checked' : '' }}>
                      </td>

                      <td>
                        <input class="form-check-input"
                          type="checkbox"
                          name="options[{{ $opt->id }}][is_default]"
                          value="1"
                          {{ $isDefault ? 'checked' : '' }}>
                      </td>

                      <td>
                        <input type="number" step="0.01" min="0"
                          class="form-control form-control-sm"
                          name="options[{{ $opt->id }}][price_override]"
                          value="{{ old('options.'.$opt->id.'.price_override', $priceOverride) }}"
                          placeholder="ไม่ใส่ = base">
                      </td>

                      <td>
                        <input type="number" min="1"
                          class="form-control form-control-sm"
                          name="options[{{ $opt->id }}][max_qty]"
                          value="{{ old('options.'.$opt->id.'.max_qty', $maxQty) }}"
                          placeholder="ไม่จำกัด">
                      </td>

                      <td>
                        <input type="number" min="0"
                          class="form-control form-control-sm"
                          name="options[{{ $opt->id }}][sort]"
                          value="{{ old('options.'.$opt->id.'.sort', $sort) }}">
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>
    @endforeach

    <button class="btn btn-primary w-100">บันทึก</button>
  </form>
</div>
@endsection
