@extends('layouts.staff')

@section('title', 'Options')

@section('content')
<div class="container py-3">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Options (กลาง)</h4>

    <a href="{{ route('staff.options.create') }}" class="btn btn-primary btn-sm">
      + เพิ่ม Option
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:70px;">#</th>
            <th>ชื่อ</th>
            <th style="width:140px;">Base ราคา</th>
            <th style="width:120px;">สถานะ</th>
            <th style="width:100px;">Sort</th>
            <th class="text-end" style="width:220px;">จัดการ</th>
          </tr>
        </thead>
        <tbody>
          @forelse($options as $opt)
            <tr>
              <td>{{ $opt->id }}</td>
              <td class="fw-semibold">{{ $opt->name }}</td>
              <td>{{ number_format((float)$opt->base_price, 2) }}</td>
              <td>
                @if($opt->is_active)
                  <span class="badge bg-success">Active</span>
                @else
                  <span class="badge bg-secondary">Inactive</span>
                @endif
              </td>
              <td>{{ $opt->sort }}</td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary"
                   href="{{ route('staff.options.edit', $opt->id) }}">
                  แก้ไข
                </a>

                <form method="POST"
                      action="{{ route('staff.options.destroy', $opt->id) }}"
                      class="d-inline"
                      onsubmit="return confirm('ยืนยันการลบ option นี้?');">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger ms-1">ลบ</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">ยังไม่มี Option</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-3">
    {{ $options->links() }}
  </div>

</div>
@endsection
