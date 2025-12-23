@extends('layouts.qr')

@section('title', 'Menu')

@section('content')
  <div class="container py-3">

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="alert alert-warning mb-3">
      ส่งรายการแล้วแก้ไขไม่ได้ หากต้องการแก้ไข กรุณาเรียกพนักงาน
    </div>

    <div class="mb-3">
      <input class="form-control" id="search" placeholder="ค้นหาเมนู...">
    </div>

    {{-- ฟอร์มเดียว ครอบทั้งหน้า เพื่อส่งรวมทีเดียว --}}
    <form method="POST" action="{{ route('qr.submit', $table->public_uuid) }}" id="qrForm">
      @csrf

      <div class="row g-3" id="menuList">
        @foreach($products as $product)
          @php
            $img = !empty($product->image_path)
              ? asset('storage/'.$product->image_path)
              : asset('images/no-image.jpg');
          @endphp

          <div class="col-6 col-md-4 col-lg-3 menu-item"
               data-name="{{ mb_strtolower($product->name ?? '') }}">

            <div class="menu-card">

              <div class="menu-img-wrap">
                <img
                        src="{{ $img }}"
                        alt="{{ $product->name }}"
                        loading="lazy"
                        onerror="this.onerror=null;this.src='{{ asset('images/no-image.jpg') }}';"
                >
              </div>

              <div class="menu-body">
                <div class="menu-name">{{ $product->name }}</div>
                <div class="menu-price">{{ number_format($product->price) }} บาท</div>

                {{-- ที่เก็บ hidden inputs ของสินค้านี้ --}}
                <div class="selected-items" data-product-id="{{ $product->id }}"></div>

                <div class="mt-2 d-grid gap-2">
                  <button
                          type="button"
                          class="btn btn-sm btn-dark add-btn"
                          data-product-id="{{ $product->id }}"
                  >
                    เพิ่ม
                  </button>
                </div>

                <div class="mt-2 small text-muted">
                  จำนวนที่เลือก: <span class="qty-badge" data-product-id="{{ $product->id }}">0</span>
                </div>
              </div>

            </div>
          </div>
        @endforeach
      </div>

      {{-- แถบส่งรายการล่างสุด --}}
      <div class="sticky-submit">
        <button type="submit" class="btn btn-primary w-100 py-2" id="submitBtn" disabled>
          ส่งรายการ (<span id="totalItems">0</span>)
        </button>
      </div>

    </form>
  </div>

  <style>
    /* ===== MENU CARD ===== */
    .menu-card {
      background: #ffffff;
      border-radius: 14px;
      overflow: hidden;
      height: 100%;
      box-shadow: 0 4px 10px rgba(0,0,0,.08);
      transition: transform .15s ease;
    }
    .menu-card:active { transform: scale(.98); }

    /* ===== IMAGE ===== */
    .menu-img-wrap {
      width: 100%;
      aspect-ratio: 1 / 1;
      background: #f2f2f2;
      overflow: hidden;
    }
    .menu-img-wrap img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    /* ===== BODY ===== */
    .menu-body { padding: 10px; text-align: center; }
    .menu-name {
      font-size: 14px;
      font-weight: 500;
      line-height: 1.3;
      height: 36px;
      overflow: hidden;
    }
    .menu-price {
      font-size: 15px;
      font-weight: 700;
      color: #c62828;
      margin-top: 2px;
    }

    /* ===== Sticky submit bar ===== */
    .sticky-submit {
      position: sticky;
      bottom: 0;
      padding: 10px 0;
      background: rgba(255,255,255,.92);
      backdrop-filter: blur(6px);
      margin-top: 12px;
    }
  </style>

  <script>
    (function () {
      // ---------- Search (optional) ----------
      const searchEl = document.getElementById('search');
      const menuList = document.getElementById('menuList');

      if (searchEl && menuList) {
        searchEl.addEventListener('input', function () {
          const q = (searchEl.value || '').trim().toLowerCase();
          const items = menuList.querySelectorAll('.menu-item');
          items.forEach(el => {
            const name = el.getAttribute('data-name') || '';
            el.style.display = name.includes(q) ? '' : 'none';
          });
        });
      }

      // ---------- Add items logic (NO refresh) ----------
      const totalItemsEl = document.getElementById('totalItems');
      const submitBtn = document.getElementById('submitBtn');
      const form = document.getElementById('qrForm');

      function getTotalQty() {
        const inputs = document.querySelectorAll('input[name^="items["][name$="[qty]"]');
        let total = 0;
        inputs.forEach(i => total += parseInt(i.value || '0', 10));
        return total;
      }

      function refreshTotals() {
        const total = getTotalQty();
        if (totalItemsEl) totalItemsEl.textContent = String(total);
        if (submitBtn) submitBtn.disabled = total <= 0;
      }

      function setQtyBadge(productId, qty) {
        const badge = document.querySelector('.qty-badge[data-product-id="' + productId + '"]');
        if (badge) badge.textContent = String(qty);
      }

      document.addEventListener('click', function (e) {
        const btn = e.target.closest('.add-btn');
        if (!btn) return;

        // Hard stop กัน event เดิมใน layout/parent ที่อาจทำให้ reload/navigate
        e.preventDefault();
        e.stopPropagation();

        const productId = btn.getAttribute('data-product-id');
        const container = document.querySelector('.selected-items[data-product-id="' + productId + '"]');
        if (!container) return;

        // Ensure hidden inputs exist
        let idInput = container.querySelector('input[name="items[' + productId + '][product_id]"]');
        let qtyInput = container.querySelector('input[name="items[' + productId + '][qty]"]');

        if (!idInput) {
          idInput = document.createElement('input');
          idInput.type = 'hidden';
          idInput.name = 'items[' + productId + '][product_id]';
          idInput.value = productId;
          container.appendChild(idInput);
        }

        if (!qtyInput) {
          qtyInput = document.createElement('input');
          qtyInput.type = 'hidden';
          qtyInput.name = 'items[' + productId + '][qty]';
          qtyInput.value = '0';
          container.appendChild(qtyInput);
        }

        // Increment qty
        const nextQty = parseInt(qtyInput.value || '0', 10) + 1;
        qtyInput.value = String(nextQty);

        // Update UI
        setQtyBadge(productId, nextQty);
        btn.textContent = 'เพิ่มแล้ว (' + nextQty + ')';
        refreshTotals();
      }, true); // capture = ตัด handler เดิมจาก parent ให้ชัวร์

      // ---------- Prevent double submit / refresh loop ----------
      if (form) {
        let submitting = false;
        form.addEventListener('submit', function (e) {
          // ถ้ายังไม่มีรายการ อย่าส่ง
          if (getTotalQty() <= 0) {
            e.preventDefault();
            refreshTotals();
            return;
          }

          if (submitting) {
            e.preventDefault();
            return;
          }
          submitting = true;

          // ป้องกันกดซ้ำ
          if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'กำลังส่งรายการ...';
          }
        });
      }

      // Init
      refreshTotals();
    })();
  </script>
@endsection
