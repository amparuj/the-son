@extends('layouts.staff')

@section('title','Monitor - Submissions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Monitor: Submissions</h3>
  <a class="btn btn-outline-secondary" href="{{ route('staff.orders.dashboard') }}">Back</a>
</div>

<ul class="nav nav-tabs mb-3">
  <li class="nav-item">
    <a class="nav-link {{ $statusGroup === 'OPEN' ? 'active' : '' }}"
       href="{{ route('staff.monitor.submissions', ['status' => 'OPEN']) }}">
      OPEN
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ $statusGroup === 'DONE' ? 'active' : '' }}"
       href="{{ route('staff.monitor.submissions', ['status' => 'DONE']) }}">
      DONE
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ $statusGroup === 'CLOSED' ? 'active' : '' }}"
       href="{{ route('staff.monitor.submissions', ['status' => 'CLOSED']) }}">
      CLOSE
    </a>
  </li>
</ul>

<div class="card">
  <div class="card-body">
    @if($statusGroup === 'OPEN')
      <div class="small text-muted mb-2">เรียง: เก่า → ใหม่ (submitted_at ASC)</div>
    @else
      <div class="small text-muted mb-2">เรียง: ใหม่ → เก่า (submitted_at DESC)</div>
    @endif

    @if($submissions->isEmpty())
      <div class="text-muted">No submissions.</div>
    @else
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th style="width:140px;">Time</th>
              <th style="width:110px;">Source</th>
              <th style="width:160px;">Table/Channel</th>
              <th style="width:160px;">Order</th>
              <th>Items</th>
              <th class="text-end" style="width:130px;">Submit Total</th>
              <th class="text-end" style="width:210px;"></th>
            </tr>
          </thead>
          <tbody>
          @foreach($submissions as $s)
            @php
              $order = $s->order;
              $submitTotal = $s->items->sum('line_total');
              $tableNo = optional(optional($order)->table)->number;
            @endphp
            <tr>
              <td class="fw-semibold">
                {{ optional($s->submitted_at)->format('H:i:s') }}
                <div class="small text-muted">{{ optional($s->submitted_at)->format('d/m/Y') }}</div>
              </td>
              <td>
                <span class="badge {{ $s->source === 'QR' ? 'text-bg-info' : 'text-bg-secondary' }}">{{ $s->source }}</span>
                <div class="small text-muted">#{{ $s->id }}</div>
              </td>
              <td>
                @if($order?->channel === 'DINE_IN')
                  <div class="fw-semibold">โต๊ะ {{ $tableNo }}</div>
                @else
                  <div class="fw-semibold">DELIVERY</div>
                @endif
                <div class="small text-muted">{{ $order?->status }}</div>
              </td>
              <td>
                <div class="fw-semibold">{{ $order?->order_no }}</div>
                <div class="small text-muted">Bill: {{ number_format((float)($order?->total ?? 0),2) }}</div>
              </td>
              <td>
                @foreach($s->items as $it)
                  <div>
                    <span class="fw-semibold">{{ $it->product_name }}</span>
                    <span class="text-muted">x{{ $it->qty }}</span>
                    <span class="text-muted">({{ number_format((float)$it->line_total,2) }})</span>
                    @if($it->note)
                      <span class="text-muted">- {{ $it->note }}</span>
                    @endif
                  </div>
                @endforeach
              </td>
              <td class="text-end fw-semibold">{{ number_format((float)$submitTotal,2) }}</td>
              <td class="text-end">
                <div class="d-flex justify-content-end gap-2">
                  @if($order)
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('staff.orders.show', $order) }}">Open</a>
                  @endif

                  @if($statusGroup === 'OPEN' && $s->status === 'OPEN')
                    <form method="POST" action="{{ route('staff.monitor.submissions.done', $s) }}"
                          onsubmit="return confirm('Mark submission #{{ $s->id }} as DONE?')">
                      @csrf
                      @method('PATCH')
                      <button class="btn btn-sm btn-success" type="submit">Mark DONE</button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>

      {{ $submissions->links() }}
    @endif
  </div>
</div>

@push('scripts')
<script>
  setTimeout(() => window.location.reload(), 5000);
</script>
@endpush
@endsection
