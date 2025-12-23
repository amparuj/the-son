<?php $__env->startSection('title', 'Order '.$order->order_no); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-start mb-3">

  <?php
    $pendingStaff = $order->items()->whereNull('order_submission_id')->where('created_via','STAFF')->where('status','OPEN')->count();
  ?>
  <div class="text-end">
    <?php if($order->status === 'OPEN'): ?>
      <div class="small text-muted mb-1">Pending (not sent): <span class="fw-semibold"><?php echo e($pendingStaff); ?></span></div>
      <div class="d-flex gap-2 justify-content-end">
        <form method="POST" action="<?php echo e(route('staff.orders.submitStay', $order)); ?>">
          <?php echo csrf_field(); ?>
          <button class="btn btn-outline-primary" type="submit" <?php echo e($pendingStaff === 0 ? 'disabled' : ''); ?>>
            Send (stay)
          </button>
        </form>
        <form method="POST" action="<?php echo e(route('staff.orders.submit', $order)); ?>">
          <?php echo csrf_field(); ?>
          <button class="btn btn-primary" type="submit" <?php echo e($pendingStaff === 0 ? 'disabled' : ''); ?>>
            Send + Monitor
          </button>
        </form>
      </div>
    <?php endif; ?>
  </div>

  <div>
    <h3 class="mb-1">Order: <?php echo e($order->order_no); ?></h3>
    <div class="text-muted">
      Channel: <span class="fw-semibold"><?php echo e($order->channel); ?></span>
      <?php if($order->channel === 'DINE_IN' && $order->table): ?>
        | Table: <span class="fw-semibold"><?php echo e($order->table->number); ?></span>
      <?php endif; ?>
      | Status: <span class="badge <?php echo e($order->status === 'OPEN' ? 'text-bg-warning' : 'text-bg-success'); ?>"><?php echo e($order->status); ?></span>
    </div>
    <?php if(session('change')): ?>
      <div class="alert alert-info mt-2 mb-0">Change: <?php echo e(session('change')); ?></div>
    <?php endif; ?>
  </div>

  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary" href="<?php echo e(route('staff.orders.dashboard')); ?>">Back</a>
    <?php if($order->status === 'OPEN'): ?>
      <a class="btn btn-primary" href="<?php echo e(route('staff.checkout.show', $order)); ?>">Checkout</a>
    <?php endif; ?>
  </div>
</div>

<?php if($order->channel === 'DELIVERY'): ?>
  <div class="card mb-3">
    <div class="card-header fw-semibold">Delivery Details</div>
    <div class="card-body">
      <form method="POST" action="<?php echo e(route('staff.orders.delivery.update', $order)); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PATCH'); ?>
        <div class="row g-2">
          <div class="col-md-6">
            <label class="form-label">Customer name</label>
            <input class="form-control" name="customer_name" value="<?php echo e(optional($order->delivery)->customer_name); ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input class="form-control" name="phone" value="<?php echo e(optional($order->delivery)->phone); ?>">
          </div>
          <div class="col-12">
            <label class="form-label">Address</label>
            <textarea class="form-control" name="address" rows="2"><?php echo e(optional($order->delivery)->address); ?></textarea>
          </div>
          <div class="col-12">
            <label class="form-label">Note</label>
            <input class="form-control" name="note" value="<?php echo e(optional($order->delivery)->note); ?>">
          </div>
        </div>

        <div class="mt-3 text-end">
          <button class="btn btn-outline-primary" type="submit" <?php echo e($order->status !== 'OPEN' ? 'disabled' : ''); ?>>Save</button>
        </div>
      </form>
    </div>
  </div>
<?php endif; ?>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header fw-semibold">Items</div>
      <div class="card-body">
        <?php if($order->items->isEmpty()): ?>
          <div class="text-muted">No items yet.</div>
        <?php else: ?>
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
                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td>
                      <div class="fw-semibold"><?php echo e($it->product_name); ?> <?php if(($it->status ?? 'OPEN') === 'DONE'): ?><span class="badge text-bg-success ms-1">DONE</span><?php endif; ?></div>
                      <div class="small text-muted">
                        <?php if($it->created_via === 'QR'): ?>
                          <span class="badge text-bg-info">QR</span>
                        <?php else: ?>
                          <span class="badge text-bg-secondary">STAFF</span>
                        <?php endif; ?>
                        <?php if($it->note): ?>
                          | Note: <?php echo e($it->note); ?>

                        <?php endif; ?>
                      </div>
                    </td>
                    <td class="text-end"><?php echo e(number_format($it->unit_price,2)); ?></td>
                    <td class="text-center" style="width:160px;">
                      <form class="d-flex gap-2 justify-content-center" method="POST" action="<?php echo e(route('staff.orders.items.qty', $it)); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <input type="number" min="1" max="99" class="form-control form-control-sm" name="qty" value="<?php echo e($it->qty); ?>" <?php echo e($order->status !== 'OPEN' ? 'disabled' : ''); ?>>
                        <button class="btn btn-sm btn-outline-primary" type="submit" <?php echo e($order->status !== 'OPEN' ? 'disabled' : ''); ?>>OK</button>
                      </form>
                    </td>
                    <td class="text-end"><?php echo e(number_format($it->line_total,2)); ?></td>
                    <td class="text-end">
                      <form method="POST" action="<?php echo e(route('staff.orders.items.remove', $it)); ?>" onsubmit="return confirm('ลบรายการนี้?')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button class="btn btn-sm btn-outline-danger" type="submit" <?php echo e(($order->status !== 'OPEN' || ($it->status ?? 'OPEN') === 'DONE') ? 'disabled' : ''); ?>>Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card mb-3">
      <div class="card-header fw-semibold">Add Item (Staff)</div>
      <div class="card-body">
        <form method="POST" action="<?php echo e(route('staff.orders.items.add', $order)); ?>">
          <?php echo csrf_field(); ?>
          <div class="mb-2">
            <label class="form-label">Product</label>
            <select class="form-select" name="product_id" <?php echo e($order->status !== 'OPEN' ? 'disabled' : ''); ?>>
              <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?> (<?php echo e(number_format($p->price,2)); ?>)</option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="row g-2">
            <div class="col-4">
              <label class="form-label">Qty</label>
              <input type="number" class="form-control" name="qty" min="1" max="99" value="1" <?php echo e($order->status !== 'OPEN' ? 'disabled' : ''); ?>>
            </div>
            <div class="col-8">
              <label class="form-label">Note</label>
              <input class="form-control" name="note" placeholder="optional" <?php echo e($order->status !== 'OPEN' ? 'disabled' : ''); ?>>
            </div>
          </div>
          <button class="btn btn-primary w-100 mt-3" type="submit" <?php echo e($order->status !== 'OPEN' ? 'disabled' : ''); ?>>Add</button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header fw-semibold">Totals / Discount</div>
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>Subtotal</div>
          <div class="fw-semibold"><?php echo e(number_format($order->subtotal,2)); ?></div>
        </div>
        <div class="d-flex justify-content-between">
          <div>Discount</div>
          <div class="fw-semibold">
            <?php if($order->discount_type === 'NONE'): ?>
              0.00
            <?php elseif($order->discount_type === 'AMOUNT'): ?>
              -<?php echo e(number_format($order->discount_value,2)); ?>

            <?php else: ?>
              <?php echo e(number_format($order->discount_value,2)); ?>%
            <?php endif; ?>
          </div>
        </div>
        <hr>
        <div class="d-flex justify-content-between fs-5">
          <div>Total</div>
          <div class="fw-bold"><?php echo e(number_format($order->total,2)); ?></div>
        </div>

        <form class="mt-3" method="POST" action="<?php echo e(route('staff.orders.discount', $order)); ?>">
          <?php echo csrf_field(); ?>
          <?php echo method_field('PATCH'); ?>
          <div class="row g-2 align-items-end">
            <div class="col-5">
              <label class="form-label">Type</label>
              <select class="form-select" name="discount_type" <?php echo e($order->status !== 'OPEN' ? 'disabled' : ''); ?>>
                <option value="NONE" <?php echo e($order->discount_type==='NONE'?'selected':''); ?>>NONE</option>
                <option value="AMOUNT" <?php echo e($order->discount_type==='AMOUNT'?'selected':''); ?>>AMOUNT</option>
                <option value="PERCENT" <?php echo e($order->discount_type==='PERCENT'?'selected':''); ?>>PERCENT</option>
              </select>
            </div>
            <div class="col-4">
              <label class="form-label">Value</label>
              <input class="form-control" name="discount_value" value="<?php echo e($order->discount_value); ?>" <?php echo e($order->status !== 'OPEN' ? 'disabled' : ''); ?>>
            </div>
            <div class="col-3">
              <button class="btn btn-outline-primary w-100" type="submit" <?php echo e($order->status !== 'OPEN' ? 'disabled' : ''); ?>>Apply</button>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.staff', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/paruj.le/Documents/restaurant_pos_phase1_laravel11_breeze/resources/views/staff/orders/show.blade.php ENDPATH**/ ?>