@extends('layouts.staff')

@section('title', 'Edit Option Group')

@section('content')
<div class="container py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">แก้ไข Group: {{ $group->name }}</h4>
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('staff.option-groups.index') }}">กลับ</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('staff.option-groups.update', $group->id) }}" id="groupForm">
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label class="form-label">ชื่อกลุ่ม</label>
          <input type="text" name="name" class="form-control"
                 value="{{ old('name', $group->name) }}" required>
          @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Sort</label>
          <input type="number" min="0" name="sort" class="form-control"
                 value="{{ old('sort', $group->sort ?? 0) }}">
          @error('sort')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="alert alert-info">
          ลากเพื่อเรียงลำดับ option ในกลุ่มนี้ (ลำดับนี้จะถูกใช้ในหน้า QR และหน้า Product Options)
        </div>

        <div class="row g-2" id="groupOptionsSortable">
          @foreach($options as $opt)
            @php $isChecked = in_array($opt->id, $selected ?? []); @endphp
            <div class="col-12 col-md-6 col-lg-4 option-card" data-option-id="{{ $opt->id }}">
              <div class="border rounded p-2 bg-white d-flex align-items-center justify-content-between">
                <div class="form-check mb-0">
                  <input class="form-check-input" type="checkbox" name="option_ids[]"
                         value="{{ $opt->id }}" id="opt_{{ $opt->id }}"
                         {{ $isChecked ? 'checked' : '' }}>
                  <label class="form-check-label" for="opt_{{ $opt->id }}">
                    {{ $opt->name }}
                    @if((float)$opt->base_price > 0)
                      <span class="text-muted">(+{{ number_format((float)$opt->base_price, 2) }})</span>
                    @endif
                  </label>
                </div>
                <span class="btn btn-sm btn-outline-secondary drag-handle" type="button">≡</span>
              </div>
            </div>
          @endforeach
        </div>

        <div id="orderInputs"></div>

        <button class="btn btn-primary w-100 mt-3">บันทึก</button>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
(function() {
  const el = document.getElementById('groupOptionsSortable');
  const holder = document.getElementById('orderInputs');

  function rebuild() {
    holder.innerHTML = '';
    const cards = Array.from(el.querySelectorAll('.option-card'));
    cards.forEach((card) => {
      const id = card.dataset.optionId;
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'option_order[]';
      input.value = id;
      holder.appendChild(input);
    });
  }

  Sortable.create(el, {
    handle: '.drag-handle',
    animation: 150,
    onSort: rebuild
  });
  rebuild();
})();
</script>
@endpush
