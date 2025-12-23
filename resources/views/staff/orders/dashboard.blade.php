@extends('layouts.staff')

@section('title','Orders Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Orders Dashboard</h3>

  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newDeliveryModal">
    New Delivery Order
  </button>
</div>

<ul class="nav nav-tabs mb-3" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabTables" type="button" role="tab">
      Tables (12)
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabDelivery" type="button" role="tab">
      Delivery
    </button>
  </li>
</ul>

<div class="tab-content">
  <div class="tab-pane fade show active" id="tabTables" role="tabpanel">
    <div class="row g-3">
      @foreach($tables as $t)
        @php $open = $openByTable->get($t->id); @endphp
        <div class="col-6 col-md-3 col-lg-2">
          <div class="card {{ $open ? 'border-warning' : 'border-success' }}">
            <div class="card-body p-2">
              <div class="d-flex justify-content-between align-items-center">
                <div class="fw-semibold">โต๊ะ {{ $t->number }}</div>
                <span class="badge {{ $open ? 'text-bg-warning' : 'text-bg-success' }}">
                  {{ $open ? 'OPEN' : 'FREE' }}
                </span>
              </div>

              <div class="small text-muted mt-1">
                QR: <span class="text-break">/t/{{ $t->public_uuid }}</span>
              </div>

              @if($open)
                <div class="mt-2 small">
                  บิล: <span class="fw-semibold">{{ $open->order_no }}</span><br>
                  ยอด: <span class="fw-semibold">{{ number_format($open->total,2) }}</span>
                </div>
                <a class="btn btn-sm btn-outline-primary w-100 mt-2" href="{{ route('staff.orders.show', $open) }}">
                  Open
                </a>
              @else
                <form method="POST" action="{{ route('staff.orders.open.dinein', $t) }}" class="mt-2">
                  @csrf
                  <button class="btn btn-sm btn-primary w-100" type="submit" {{ !$t->is_active ? 'disabled' : '' }}>
                    Open Bill
                  </button>
                </form>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <div class="tab-pane fade" id="tabDelivery" role="tabpanel">
    <div class="card">
      <div class="card-body">
        @if($openDeliveries->isEmpty())
          <div class="text-muted">No open delivery orders.</div>
        @else
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>Order</th>
                  <th>Opened</th>
                  <th>Total</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @foreach($openDeliveries as $o)
                  <tr>
                    <td class="fw-semibold">{{ $o->order_no }}</td>
                    <td>{{ optional($o->opened_at)->format('H:i') }}</td>
                    <td>{{ number_format($o->total,2) }}</td>
                    <td class="text-end">
                      <a class="btn btn-sm btn-outline-primary" href="{{ route('staff.orders.show', $o) }}">Open</a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- New Delivery Modal -->
<div class="modal fade" id="newDeliveryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('staff.orders.open.delivery') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">New Delivery Order</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2">
          <div class="col-md-6">
            <label class="form-label">Customer name (optional)</label>
            <input class="form-control" name="customer_name">
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone (optional)</label>
            <input class="form-control" name="phone">
          </div>
          <div class="col-12">
            <label class="form-label">Address (optional but recommended)</label>
            <textarea class="form-control" name="address" rows="2"></textarea>
          </div>
          <div class="col-12">
            <label class="form-label">Note</label>
            <input class="form-control" name="note" placeholder="เช่น โทรก่อนถึง">
          </div>
        </div>
        <div class="alert alert-secondary mt-3 mb-0">
          หมายเหตุ: รายละเอียดส่งเป็น optional แต่แนะนำให้กรอกเพื่อกันส่งผิด
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" type="submit">Create</button>
      </div>
    </form>
  </div>
</div>

@endsection
