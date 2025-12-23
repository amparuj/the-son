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

    <form method="POST" action="{{ route('qr.submit', $table->public_uuid) }}" id="qrForm">
      @csrf

      <div class="row g-3" id="menuList">
        @foreach($products as $product)
          @php
            $img = !empty($product->image_path)
              ? asset('storage/'.$product->image_path)
              : asset('images/no-image.jpg');

            $enabledGroups = $product->optionGroups
              ->filter(fn($g) => (bool)($g->pivot->is_enabled ?? false))
              ->values();

            $allowed = $product->options
              ->filter(fn($o) => (bool)($o->pivot->is_allowed ?? false))
              ->keyBy('id');

            $payload = [
              'id' => $product->id,
              'name' => $product->name,
              'price' => (float)$product->price,
              'groups' => $enabledGroups->map(function($g) use ($allowed) {
                $min = (int)($g->pivot->min_select ?? 0);
                $max = (int)($g->pivot->max_select ?? 0);
                $maxAttr = $max > 0 ? $max : 9999;

                $opts = $g->options
                  ->filter(fn($opt) => $allowed->has($opt->id))
                  ->map(function($opt) use ($allowed) {
                    $p = $allowed->get($opt->id);
                    $override = $p?->pivot?->price_override;
                    $price = $override !== null ? (float)$override : (float)$opt->base_price;

                    return [
                      'id' => $opt->id,
                      'name' => $opt->name,
                      'price' => $price,
                    ];
                  })
                  ->values();

                return [
                  'id' => $g->id,
                  'name' => $g->name,
                  'min' => $min,
                  'max' => $maxAttr,
                  'maxReal' => $max,
                  'options' => $opts,
                ];
              })->values(),
            ];
          @endphp

          <div class="col-6 col-md-4 col-lg-3 menu-item" data-name="{{ mb_strtolower($product->name ?? '') }}">
            <div class="menu-card">
              <div class="menu-img-wrap">
                <img src="{{ $img }}"
                     alt="{{ $product->name }}"
                     loading="lazy"
                     onerror="this.onerror=null;this.src='{{ asset('images/no-image.jpg') }}';">
              </div>

              <div class="menu-body">
                <div class="menu-name">{{ $product->name }}</div>
                <div class="menu-price">{{ number_format($product->price) }} บาท</div>

                <button type="button"
                        class="btn btn-sm btn-dark w-100 mt-2"
                        data-product='@json($payload, JSON_UNESCAPED_UNICODE)'
                        onclick="openOptionModal(this)">
                  เพิ่ม
                </button>

{{--                @if($enabledGroups->count() > 0)--}}
{{--                  <div class="mt-2 small text-muted">มีตัวเลือก</div>--}}
{{--                @else--}}
{{--                  <div class="mt-2 small text-muted">ไม่มีตัวเลือก</div>--}}
{{--                @endif--}}
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <hr class="my-3">

      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-semibold">รายการที่เลือก</div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearCart()">ล้างทั้งหมด</button>
          </div>

          <div id="cartList" class="small text-muted">ยังไม่มีรายการ</div>

          <button type="submit" class="btn btn-success w-100 mt-3" id="submitBtn" disabled>
            ส่งรายการ
          </button>
        </div>
      </div>
    </form>
  </div>

  {{-- Modal --}}
  <div class="modal fade" id="optModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">

        <div class="modal-header">
          <div>
            <div class="fw-semibold" id="modalTitle">เลือกตัวเลือก</div>
            <div class="text-muted small">เลือกตามกลุ่ม แล้วกด “เพิ่มลงรายการ”</div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div id="modalGroups"></div>

          <div class="mb-3">
            <label class="form-label">จำนวน</label>

            <div class="input-group">
              <button type="button"
                      class="btn btn-outline-secondary"
                      onclick="changeQty(-1)">
                −
              </button>

              <input type="number"
                     class="form-control text-center"
                     id="modalQty"
                     min="1"
                     value="1"
                     readonly>

              <button type="button"
                      class="btn btn-outline-secondary"
                      onclick="changeQty(1)">
                +
              </button>
            </div>
          </div>


          <div class="alert alert-danger d-none" id="modalError"></div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="button" class="btn btn-primary" onclick="addLineItem()">เพิ่มลงรายการ</button>
        </div>
      </div>
    </div>
  </div>

  <style>
    .menu-card{background:#fff;border-radius:14px;overflow:hidden;height:100%;box-shadow:0 4px 10px rgba(0,0,0,.08)}
    .menu-img-wrap{width:100%;aspect-ratio:1/1;background:#f2f2f2;overflow:hidden}
    .menu-img-wrap img{width:100%;height:100%;object-fit:cover;display:block}
    .menu-body{padding:10px;text-align:center}
    .menu-name{font-size:14px;font-weight:500;line-height:1.3;min-height:36px;overflow:hidden}
    .menu-price{font-size:15px;font-weight:700;color:#c62828;margin-top:2px}
  </style>

  @push('scripts')
    <script>
      function changeQty(delta) {
        const input = document.getElementById('modalQty');
        let val = parseInt(input.value || '1', 10);
        val += delta;
        if (val < 1) val = 1;
        input.value = val;
      }
    </script>

    <script>
      (function(){
        const searchEl = document.getElementById('search');
        const menuList = document.getElementById('menuList');
        if (searchEl && menuList) {
          searchEl.addEventListener('input', () => {
            const q = (searchEl.value || '').trim().toLowerCase();
            menuList.querySelectorAll('.menu-item').forEach(el => {
              const name = el.dataset.name || '';
              el.style.display = name.includes(q) ? '' : 'none';
            });
          });
        }
      })();
    </script>

    <script>
      const cart = [];
      let currentProduct = null;

      function uuidv4() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
          var r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
          return v.toString(16);
        });
      }

      function openOptionModal(btn){
        currentProduct = JSON.parse(btn.getAttribute('data-product') || '{}');

        document.getElementById('modalTitle').textContent = currentProduct.name || 'เลือกตัวเลือก';
        document.getElementById('modalQty').value = 1;

        const err = document.getElementById('modalError');
        err.classList.add('d-none');
        err.textContent = '';

        const holder = document.getElementById('modalGroups');
        holder.innerHTML = '';

        const groups = (currentProduct.groups || []);
        if (groups.length === 0) {
          // holder.innerHTML = '<div class="alert alert-info">เมนูนี้ไม่มีตัวเลือก</div>';
        } else {
          groups.forEach(g => {
            const min = parseInt(g.min || 0, 10);
            const maxReal = parseInt(g.maxReal || 0, 10);
            const max = parseInt(g.max || 9999, 10);

            const groupEl = document.createElement('div');
            groupEl.className = 'mb-3 option-group';
            groupEl.dataset.groupId = g.id;
            groupEl.dataset.min = String(min);
            groupEl.dataset.max = String(max);

            groupEl.innerHTML = `
          <div class="d-flex justify-content-between align-items-center">
            <div class="fw-semibold">${g.name}</div>
            <div class="text-muted small">
              ${min>0 ? `ต้องเลือกอย่างน้อย ${min}` : ''}
              ${min>0 && maxReal>0 ? `/` : ''}
              ${maxReal>0 ? ` เลือกได้สูงสุด ${maxReal}` : ''}
            </div>
          </div>
          <div class="mt-2" data-group-options="1"></div>
        `;

            const optBox = groupEl.querySelector('[data-group-options="1"]');

            if (!g.options || g.options.length === 0) {
              const empty = document.createElement('div');
              empty.className = 'text-muted small mt-1';
              empty.textContent = 'ไม่มีตัวเลือกในกลุ่มนี้';
              optBox.appendChild(empty);
            } else {
              g.options.forEach(opt => {
                const price = parseFloat(opt.price || 0);
                const line = document.createElement('label');
                line.className = 'd-flex align-items-center justify-content-between border rounded p-2 mb-2 bg-white';
                line.innerHTML = `
              <div class="d-flex align-items-center gap-2">
                <input class="form-check-input opt-checkbox"
                       type="checkbox"
                       value="${opt.id}"
                       data-group-id="${g.id}">
                <span>${opt.name}</span>
              </div>
              <span class="text-muted small">${price>0 ? `+${price.toFixed(2)}` : ''}</span>
            `;
                optBox.appendChild(line);
              });
            }

            groupEl.addEventListener('change', (e) => {
              const chk = e.target.closest('.opt-checkbox');
              if (!chk) return;
              enforceGroupMax(groupEl);
            });

            holder.appendChild(groupEl);
          });
        }

        const modalEl = document.getElementById('optModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
      }

      function enforceGroupMax(groupEl){
        const gid = groupEl.dataset.groupId;
        const max = parseInt(groupEl.dataset.max || '9999', 10);
        const checked = groupEl.querySelectorAll(`.opt-checkbox[data-group-id="${gid}"]:checked`);
        if (checked.length > max) {
          checked[checked.length - 1].checked = false;
        }
      }

      function validateModal(){
        const errs = [];
        document.querySelectorAll('#modalGroups .option-group').forEach(groupEl => {
          const gid = groupEl.dataset.groupId;
          const min = parseInt(groupEl.dataset.min || '0', 10);
          const max = parseInt(groupEl.dataset.max || '9999', 10);
          const checked = groupEl.querySelectorAll(`.opt-checkbox[data-group-id="${gid}"]:checked`).length;

          if (min > 0 && checked < min) errs.push(`เลือกไม่ครบ (min=${min}) ในบางกลุ่ม`);
          if (checked > max) errs.push(`เลือกเกิน max ในบางกลุ่ม`);
        });
        return errs;
      }

      function addLineItem(){
        const errs = validateModal();
        const errBox = document.getElementById('modalError');

        if (errs.length) {
          errBox.textContent = errs.join(' / ');
          errBox.classList.remove('d-none');
          return;
        }

        const qty = parseInt(document.getElementById('modalQty').value || '1', 10);
        const optionIds = Array.from(document.querySelectorAll('#modalGroups .opt-checkbox:checked'))
                .map(x => parseInt(x.value, 10));
        const optionNames = Array.from(
                document.querySelectorAll('#modalGroups .opt-checkbox:checked')
        ).map(x => x.closest('label')?.innerText.trim());

        cart.push({
          uuid: uuidv4(),
          product_id: currentProduct.id,
          product_name: currentProduct.name,
          qty,
          option_ids: optionIds,
          option_names: optionNames
        });

        bootstrap.Modal.getInstance(document.getElementById('optModal')).hide();
        renderCart();
      }

      function clearCart(){
        cart.length = 0;
        renderCart();
      }

      function renderCart(){
        const list = document.getElementById('cartList');
        const form = document.getElementById('qrForm');
        const submitBtn = document.getElementById('submitBtn');

        form.querySelectorAll('input[data-cart="1"]').forEach(i => i.remove());

        if (cart.length === 0) {
          list.textContent = 'ยังไม่มีรายการ';
          submitBtn.disabled = true;
          return;
        }

        submitBtn.disabled = false;
        list.innerHTML = '';

        cart.forEach((it, idx) => {
          const row = document.createElement('div');
          row.className = 'd-flex justify-content-between align-items-start border rounded p-2 mb-2 bg-white';
          row.innerHTML = `
        <div>
          <div class="fw-semibold">#${idx+1} รายการ: ${it.product_name} x ${it.qty}</div>
          <div class="text-muted">options: ${it.option_names.join(', ') || '-'}</div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger">ลบ</button>
      `;
          row.querySelector('button').onclick = () => { cart.splice(idx, 1); renderCart(); };
          list.appendChild(row);

          const mk = (name, value) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            input.dataset.cart = "1";
            form.appendChild(input);
          };

          mk(`items[${it.uuid}][product_id]`, it.product_id);
          mk(`items[${it.uuid}][qty]`, it.qty);
          it.option_ids.forEach(oid => mk(`items[${it.uuid}][option_ids][]`, oid));
        });
      }
    </script>
  @endpush
@endsection
