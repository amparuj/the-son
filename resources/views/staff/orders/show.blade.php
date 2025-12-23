@extends('layouts.staff')

@section('title', 'Order '.$order->order_no)

@section('content')
<div class="d-flex justify-content-between align-items-start mb-3">

  @php
    $pendingStaff = $order->items()->whereNull('order_submission_id')->where('created_via','STAFF')->where('status','OPEN')->count();
  @endphp
  <div class="text-end">
    @if($order->status === 'OPEN')
      <div class="small text-muted mb-1">Pending (not sent): <span class="fw-semibold">{{ $pendingStaff }}</span></div>
      <div class="d-flex gap-2 justify-content-end">
        <form method="POST" action="{{ route('staff.orders.submitStay', $order) }}">
          @csrf
          <button class="btn btn-outline-primary" type="submit" {{ $pendingStaff === 0 ? 'disabled' : '' }}>
            Send (stay)
          </button>
        </form>
        <form method="POST" action="{{ route('staff.orders.submit', $order) }}">
          @csrf
          <button class="btn btn-primary" type="submit" {{ $pendingStaff === 0 ? 'disabled' : '' }}>
            Send + Monitor
          </button>
        </form>
      </div>
    @endif
  </div>

  <div>
    <h3 class="mb-1">Order: {{ $order->order_no }}</h3>
    <div class="text-muted">
      Channel: <span class="fw-semibold">{{ $order->channel }}</span>
      @if($order->channel === 'DINE_IN' && $order->table)
        | Table: <span class="fw-semibold">{{ $order->table->number }}</span>
      @endif
      | Status: <span class="badge {{ $order->status === 'OPEN' ? 'text-bg-warning' : 'text-bg-success' }}">{{ $order->status }}</span>
    </div>
    @if(session('change'))
      <div class="alert alert-info mt-2 mb-0">Change: {{ session('change') }}</div>
    @endif
  </div>

  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary" href="{{ route('staff.orders.dashboard') }}">Back</a>
    @if($order->status === 'OPEN')
      <a class="btn btn-primary" href="{{ route('staff.checkout.show', $order) }}">Checkout</a>
    @endif
  </div>
</div>

@if($order->channel === 'DELIVERY')
  <div class="card mb-3">
    <div class="card-header fw-semibold">Delivery Details</div>
    <div class="card-body">
      <form method="POST" action="{{ route('staff.orders.delivery.update', $order) }}">
        @csrf
        @method('PATCH')
        <div class="row g-2">
          <div class="col-md-6">
            <label class="form-label">Customer name</label>
            <input class="form-control" name="customer_name" value="{{ optional($order->delivery)->customer_name }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input class="form-control" name="phone" value="{{ optional($order->delivery)->phone }}">
          </div>
          <div class="col-12">
            <label class="form-label">Address</label>
            <textarea class="form-control" name="address" rows="2">{{ optional($order->delivery)->address }}</textarea>
          </div>
          <div class="col-12">
            <label class="form-label">Note</label>
            <input class="form-control" name="note" value="{{ optional($order->delivery)->note }}">
          </div>
        </div>

        <div class="mt-3 text-end">
          <button class="btn btn-outline-primary" type="submit" {{ $order->status !== 'OPEN' ? 'disabled' : '' }}>Save</button>
        </div>
      </form>
    </div>
  </div>
@endif

<div class="row g-3">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header fw-semibold">Items</div>
      <div class="card-body">
        @if($order->items->isEmpty())
          <div class="text-muted">No items yet.</div>
        @else
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>Item</th>
                  <th class="text-end">Price</th>
                  <th class="text-center">Qty</th>
                  <th class="text-end">Total</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @foreach($order->items as $it)
                  <tr>
                    <td>
                      <div class="fw-semibold">{{ $it->product_name }} @if(($it->status ?? 'OPEN') === 'DONE')<span class="badge text-bg-success ms-1">DONE</span>@endif</div>
                      <div class="small text-muted">
                        @if($it->created_via === 'QR')
                          <span class="badge text-bg-info">QR</span>
                        @else
                          <span class="badge text-bg-secondary">STAFF</span>
                        @endif
                        @if($it->note)
                          | Note: {{ $it->note }}
                        @endif
                      </div>
                    </td>
                    <td class="text-end">{{ number_format($it->unit_price,2) }}</td>
                    <td class="text-center" style="width:160px;">
                      <form class="d-flex gap-2 justify-content-center" method="POST" action="{{ route('staff.orders.items.qty', $it) }}">
                        @csrf
                        @method('PATCH')
                        <input type="number" min="1" max="99" class="form-control form-control-sm" name="qty" value="{{ $it->qty }}" {{ $order->status !== 'OPEN' ? 'disabled' : '' }}>
                        <button class="btn btn-sm btn-outline-primary" type="submit" {{ $order->status !== 'OPEN' ? 'disabled' : '' }}>OK</button>
                      </form>
                    </td>
                    <td class="text-end">{{ number_format($it->line_total,2) }}</td>
                    <td class="text-end">
                      <form method="POST" action="{{ route('staff.orders.items.remove', $it) }}" onsubmit="return confirm('ลบรายการนี้?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" type="submit" {{ ($order->status !== 'OPEN' || ($it->status ?? 'OPEN') === 'DONE') ? 'disabled' : '' }}>Delete</button>
                      </form>
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

  <div class="col-lg-5">
    <div class="card mb-3">
      <div class="card-header fw-semibold">Add Item (Staff)</div>
      <div class="card-body">
        <form method="POST" action="{{ route('staff.orders.items.add', $order) }}">
          @csrf
          <div class="mb-2">
            <label class="form-label">Product</label>
            <select class="form-select" name="product_id" {{ $order->status !== 'OPEN' ? 'disabled' : '' }}>
              @foreach($products as $p)
                <option value="{{ $p->id }}">{{ $p->name }} ({{ number_format($p->price,2) }})</option>
              @endforeach
            </select>
          </div>
          <div class="row g-2">
            <div class="col-4">
              <label class="form-label">Qty</label>
              <input type="number" class="form-control" name="qty" min="1" max="99" value="1" {{ $order->status !== 'OPEN' ? 'disabled' : '' }}>
            </div>
            <div class="col-8">
              <label class="form-label">Note</label>
              <input class="form-control" name="note" placeholder="optional" {{ $order->status !== 'OPEN' ? 'disabled' : '' }}>
            </div>
          </div>
          <button class="btn btn-primary w-100 mt-3" type="submit" {{ $order->status !== 'OPEN' ? 'disabled' : '' }}>Add</button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header fw-semibold">Totals / Discount</div>
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>Subtotal</div>
          <div class="fw-semibold">{{ number_format($order->subtotal,2) }}</div>
        </div>
        <div class="d-flex justify-content-between">
          <div>Discount</div>
          <div class="fw-semibold">
            @if($order->discount_type === 'NONE')
              0.00
            @elseif($order->discount_type === 'AMOUNT')
              -{{ number_format($order->discount_value,2) }}
            @else
              {{ number_format($order->discount_value,2) }}%
            @endif
          </div>
        </div>
        <hr>
        <div class="d-flex justify-content-between fs-5">
          <div>Total</div>
          <div class="fw-bold">{{ number_format($order->total,2) }}</div>
        </div>

        <form class="mt-3" method="POST" action="{{ route('staff.orders.discount', $order) }}">
          @csrf
          @method('PATCH')
          <div class="row g-2 align-items-end">
            <div class="col-5">
              <label class="form-label">Type</label>
              <select class="form-select" name="discount_type" {{ $order->status !== 'OPEN' ? 'disabled' : '' }}>
                <option value="NONE" {{ $order->discount_type==='NONE'?'selected':'' }}>NONE</option>
                <option value="AMOUNT" {{ $order->discount_type==='AMOUNT'?'selected':'' }}>AMOUNT</option>
                <option value="PERCENT" {{ $order->discount_type==='PERCENT'?'selected':'' }}>PERCENT</option>
              </select>
            </div>
            <div class="col-4">
              <label class="form-label">Value</label>
              <input class="form-control" name="discount_value" value="{{ $order->discount_value }}" {{ $order->status !== 'OPEN' ? 'disabled' : '' }}>
            </div>
            <div class="col-3">
              <button class="btn btn-outline-primary w-100" type="submit" {{ $order->status !== 'OPEN' ? 'disabled' : '' }}>Apply</button>
            </div>
          </div>
          <div class="small text-muted mt-2">
            AMOUNT = บาท, PERCENT = %
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
