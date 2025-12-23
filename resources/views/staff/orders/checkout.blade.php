@extends('layouts.staff')

@section('title','Checkout '.$order->order_no)

@section('content')
<div class="d-flex justify-content-between align-items-start mb-3">
  <div>
    <h3 class="mb-1">Checkout: {{ $order->order_no }}</h3>
    <div class="text-muted">
      Channel: <span class="fw-semibold">{{ $order->channel }}</span>
      @if($order->channel === 'DINE_IN' && $order->table)
        | Table: <span class="fw-semibold">{{ $order->table->number }}</span>
      @endif
      | Total: <span class="fw-bold">{{ number_format($order->total,2) }}</span>
    </div>
  </div>
  <a class="btn btn-outline-secondary" href="{{ route('staff.orders.show', $order) }}">Back</a>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header fw-semibold">Payments (Split allowed)</div>
      <div class="card-body">
        <form method="POST" action="{{ route('staff.checkout.pay', $order) }}" id="payForm">
          @csrf

          <div id="paymentsWrap" class="d-flex flex-column gap-2">
            <div class="border rounded p-2 payment-row">
              <div class="row g-2 align-items-end">
                <div class="col-4">
                  <label class="form-label">Method</label>
                  <select class="form-select" name="payments[0][method]">
                    <option value="CASH">CASH</option>
                    <option value="TRANSFER">TRANSFER</option>
                    <option value="QR">QR</option>
                    <option value="CARD">CARD</option>
                  </select>
                </div>
                <div class="col-4">
                  <label class="form-label">Amount</label>
                  <input class="form-control amount-input" name="payments[0][amount]" type="number" step="0.01" min="0.01" required>
                </div>
                <div class="col-4">
                  <label class="form-label">Ref (optional)</label>
                  <input class="form-control" name="payments[0][ref_no]">
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex gap-2 mt-2">
            <button type="button" class="btn btn-outline-primary" id="addPayBtn">Add payment</button>
            <button type="button" class="btn btn-outline-danger" id="removePayBtn">Remove last</button>
          </div>

          <hr>

          <div class="d-flex justify-content-between">
            <div>Total Due</div>
            <div class="fw-bold" id="due">{{ number_format($order->total,2) }}</div>
          </div>
          <div class="d-flex justify-content-between">
            <div>Total Paid</div>
            <div class="fw-semibold" id="paid">0.00</div>
          </div>
          <div class="d-flex justify-content-between">
            <div>Change (est.)</div>
            <div class="fw-semibold" id="change">0.00</div>
          </div>

          <button class="btn btn-primary w-100 mt-3" type="submit"
            onclick="return confirm('ยืนยันชำระเงินและปิดบิล?')">
            Confirm & Close Bill
          </button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card">
      <div class="card-header fw-semibold">Order Summary</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Item</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Total</th>
              </tr>
            </thead>
            <tbody>
              @foreach($order->items as $it)
                <tr>
                  <td>{{ $it->product_name }}</td>
                  <td class="text-end">{{ $it->qty }}</td>
                  <td class="text-end">{{ number_format($it->line_total,2) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <hr>
        <div class="d-flex justify-content-between">
          <div>Subtotal</div><div class="fw-semibold">{{ number_format($order->subtotal,2) }}</div>
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
        <div class="d-flex justify-content-between fs-5 mt-2">
          <div>Total</div><div class="fw-bold">{{ number_format($order->total,2) }}</div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
(function(){
  const due = parseFloat("{{ (float) $order->total }}");
  const paymentsWrap = document.getElementById('paymentsWrap');
  const addBtn = document.getElementById('addPayBtn');
  const removeBtn = document.getElementById('removePayBtn');
  const paidEl = document.getElementById('paid');
  const changeEl = document.getElementById('change');

  function recalc(){
    let totalPaid = 0;
    let cashPaid = 0;
    paymentsWrap.querySelectorAll('.payment-row').forEach(row => {
      const method = row.querySelector('select').value;
      const amt = parseFloat(row.querySelector('.amount-input').value || '0');
      totalPaid += amt;
      if(method === 'CASH') cashPaid += amt;
    });
    paidEl.textContent = totalPaid.toFixed(2);
    const ch = (cashPaid > 0) ? Math.max(0, totalPaid - due) : 0;
    changeEl.textContent = ch.toFixed(2);
  }

  paymentsWrap.addEventListener('input', recalc);

  addBtn.addEventListener('click', () => {
    const idx = paymentsWrap.querySelectorAll('.payment-row').length;
    const div = document.createElement('div');
    div.className = 'border rounded p-2 payment-row';
    div.innerHTML = `
      <div class="row g-2 align-items-end">
        <div class="col-4">
          <label class="form-label">Method</label>
          <select class="form-select" name="payments[${idx}][method]">
            <option value="CASH">CASH</option>
            <option value="TRANSFER">TRANSFER</option>
            <option value="QR">QR</option>
            <option value="CARD">CARD</option>
          </select>
        </div>
        <div class="col-4">
          <label class="form-label">Amount</label>
          <input class="form-control amount-input" name="payments[${idx}][amount]" type="number" step="0.01" min="0.01" required>
        </div>
        <div class="col-4">
          <label class="form-label">Ref (optional)</label>
          <input class="form-control" name="payments[${idx}][ref_no]">
        </div>
      </div>
    `;
    paymentsWrap.appendChild(div);
  });

  removeBtn.addEventListener('click', () => {
    const rows = paymentsWrap.querySelectorAll('.payment-row');
    if(rows.length <= 1) return;
    rows[rows.length - 1].remove();
    recalc();
  });

  recalc();
})();
</script>
@endpush
@endsection
