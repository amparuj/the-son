@extends('layouts.staff')

@section('title', 'Product Options')

@section('content')
<div class="container py-3">
  <h4 class="mb-3">{{ $product->name }} - Options</h4>

  <form method="POST" action="{{ route('staff.products.options.update', $product) }}">
    @csrf

    <div class="d-flex gap-2 mb-3">
      <button type="button" class="btn btn-sm btn-outline-primary" onclick="enableAll(false)">
        เปิดทุก Group
      </button>
      <button type="button" class="btn btn-sm btn-outline-danger" onclick="enableAll(true)">
        บังคับเลือก 1 ทุก Group
      </button>
      <button class="btn btn-sm btn-success ms-auto">บันทึก</button>
    </div>

    @foreach($groups as $group)
      @php
        $pg = $productGroups->get($group->id);
        $enabled = $pg ? (bool)$pg->pivot->is_enabled : false;
        $min = $pg ? (int)$pg->pivot->min_select : 0;
        $max = $pg ? (int)$pg->pivot->max_select : 0;
      @endphp

      <div class="card mb-3">
        <div class="card-header d-flex align-items-center gap-3">
          <strong>{{ $group->name }}</strong>

          <label class="form-check mb-0 ms-auto">
            <input class="form-check-input group-enable" type="checkbox"
                   name="groups[{{ $group->id }}][enabled]" value="1"
                   {{ $enabled ? 'checked' : '' }}>
            ใช้ Group นี้
          </label>

          <span class="small text-muted">min</span>
          <input type="number" name="groups[{{ $group->id }}][min_select]"
                 value="{{ $min }}" min="0"
                 class="form-control form-control-sm" style="width:70px">

          <span class="small text-muted">max</span>
          <input type="number" name="groups[{{ $group->id }}][max_select]"
                 value="{{ $max }}" min="0"
                 class="form-control form-control-sm" style="width:70px">
        </div>

        <div class="card-body">
          @foreach($group->options as $opt)
            @php $po = $attachedOptions->get($opt->id); @endphp
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox"
                     name="options[{{ $opt->id }}][enabled]" value="1"
                     {{ $po ? 'checked' : '' }}>
              <label class="form-check-label">
                {{ $opt->name }}
                <span class="text-muted small">
                  (+{{ number_format($opt->base_price, 2) }})
                </span>
              </label>
            </div>
          @endforeach
        </div>
      </div>
    @endforeach
  </form>
</div>

@push('scripts')
<script>
function enableAll(forceOne){
  document.querySelectorAll('.group-enable').forEach(el => el.checked = true);
  if(forceOne){
    document.querySelectorAll('input[name$="[min_select]"]').forEach(el => el.value = 1);
    document.querySelectorAll('input[name$="[max_select]"]').forEach(el => el.value = 1);
  }
}
</script>
@endpush
@endsection
