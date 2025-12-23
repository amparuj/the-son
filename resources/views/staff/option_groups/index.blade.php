@extends('layouts.staff')

@section('title', 'Option Groups')

@section('content')
<div class="container py-3">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Option Groups</h4>

    <a href="{{ route('staff.option-groups.create') }}" class="btn btn-primary btn-sm">
      + เพิ่ม Group
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
            <th>ชื่อกลุ่ม</th>
            <th style="width:120px;">จำนวน Options</th>
            <th style="width:100px;">Sort</th>
            <th class="text-end" style="width:220px;">จัดการ</th>
          </tr>
        </thead>
        <tbody>
          @forelse($groups as $g)
            <tr>
              <td>{{ $g->id }}</td>
              <td class="fw-semibold">{{ $g->name }}</td>
              <td>{{ $g->options_count }}</td>
              <td>{{ $g->sort }}</td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary"
                   href="{{ route('staff.option-groups.edit', $g->id) }}">
                  แก้ไข
                </a>

                <form method="POST"
                      action="{{ route('staff.option-groups.destroy', $g->id) }}"
                      class="d-inline"
                      onsubmit="return confirm('ยืนยันการลบกลุ่มนี้?');">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger ms-1">ลบ</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted py-4">ยังไม่มี Group</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-3">
    {{ $groups->links() }}
  </div>

</div>
@endsection
