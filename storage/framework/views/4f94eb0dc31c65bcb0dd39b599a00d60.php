<?php $__env->startSection('title','Monitor - Submissions'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Monitor: Submissions</h3>
  <a class="btn btn-outline-secondary" href="<?php echo e(route('staff.orders.dashboard')); ?>">Back</a>
</div>

<ul class="nav nav-tabs mb-3">
  <li class="nav-item">
    <a class="nav-link <?php echo e($statusGroup === 'OPEN' ? 'active' : ''); ?>"
       href="<?php echo e(route('staff.monitor.submissions', ['status' => 'OPEN'])); ?>">
      OPEN
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo e($statusGroup === 'DONE' ? 'active' : ''); ?>"
       href="<?php echo e(route('staff.monitor.submissions', ['status' => 'DONE'])); ?>">
      DONE
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo e($statusGroup === 'CLOSED' ? 'active' : ''); ?>"
       href="<?php echo e(route('staff.monitor.submissions', ['status' => 'CLOSED'])); ?>">
      CLOSE
    </a>
  </li>
</ul>

<div class="card">
  <div class="card-body">
    <?php if($statusGroup === 'OPEN'): ?>
      <div class="small text-muted mb-2">เรียง: เก่า → ใหม่ (submitted_at ASC)</div>
    <?php else: ?>
      <div class="small text-muted mb-2">เรียง: ใหม่ → เก่า (submitted_at DESC)</div>
    <?php endif; ?>

    <?php if($submissions->isEmpty()): ?>
      <div class="text-muted">No submissions.</div>
    <?php else: ?>
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
          <?php $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $order = $s->order;
              $submitTotal = $s->items->sum('line_total');
              $tableNo = optional(optional($order)->table)->number;
            ?>
            <tr>
              <td class="fw-semibold">
                <?php echo e(optional($s->submitted_at)->format('H:i:s')); ?>

                <div class="small text-muted"><?php echo e(optional($s->submitted_at)->format('d/m/Y')); ?></div>
              </td>
              <td>
                <span class="badge <?php echo e($s->source === 'QR' ? 'text-bg-info' : 'text-bg-secondary'); ?>"><?php echo e($s->source); ?></span>
                <div class="small text-muted">#<?php echo e($s->id); ?></div>
              </td>
              <td>
                <?php if($order?->channel === 'DINE_IN'): ?>
                  <div class="fw-semibold">โต๊ะ <?php echo e($tableNo); ?></div>
                <?php else: ?>
                  <div class="fw-semibold">DELIVERY</div>
                <?php endif; ?>
                <div class="small text-muted"><?php echo e($order?->status); ?></div>
              </td>
              <td>
                <div class="fw-semibold"><?php echo e($order?->order_no); ?></div>
                <div class="small text-muted">Bill: <?php echo e(number_format((float)($order?->total ?? 0),2)); ?></div>
              </td>
              <td>
                <?php $__currentLoopData = $s->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div>
                    <span class="fw-semibold"><?php echo e($it->product_name); ?></span>
                    <span class="text-muted">x<?php echo e($it->qty); ?></span>
                    <span class="text-muted">(<?php echo e(number_format((float)$it->line_total,2)); ?>)</span>
                    <?php if($it->note): ?>
                      <span class="text-muted">- <?php echo e($it->note); ?></span>
                    <?php endif; ?>
                  </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </td>
              <td class="text-end fw-semibold"><?php echo e(number_format((float)$submitTotal,2)); ?></td>
              <td class="text-end">
                <div class="d-flex justify-content-end gap-2">
                  <?php if($order): ?>
                    <a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('staff.orders.show', $order)); ?>">Open</a>
                  <?php endif; ?>

                  <?php if($statusGroup === 'OPEN' && $s->status === 'OPEN'): ?>
                    <form method="POST" action="<?php echo e(route('staff.monitor.submissions.done', $s)); ?>"
                          onsubmit="return confirm('Mark submission #<?php echo e($s->id); ?> as DONE?')">
                      <?php echo csrf_field(); ?>
                      <?php echo method_field('PATCH'); ?>
                      <button class="btn btn-sm btn-success" type="submit">Mark DONE</button>
                    </form>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>

      <?php echo e($submissions->links()); ?>

    <?php endif; ?>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
  setTimeout(() => window.location.reload(), 5000);
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.staff', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/paruj.le/Documents/restaurant_pos_phase1_laravel11_breeze/resources/views/staff/monitor/submissions.blade.php ENDPATH**/ ?>