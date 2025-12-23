<?php $__env->startSection('title', 'Products'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Products</h3>
  <a class="btn btn-primary" href="<?php echo e(route('staff.products.create')); ?>">+ Add Product</a>
</div>

<?php if(session('success')): ?>
  <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th style="width:90px">Image</th>
          <th>Name</th>
          <th class="text-end" style="width:140px">Price</th>
          <th style="width:120px">Active</th>
          <th style="width:180px"></th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td>
              <?php if($p->image_path): ?>
                <img src="<?php echo e(asset('storage/'.$p->image_path)); ?>" class="rounded" style="width:72px;height:72px;object-fit:cover;">
              <?php else: ?>
                <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="width:72px;height:72px;">
                  <span class="text-muted small">No image</span>
                </div>
              <?php endif; ?>
            </td>
            <td>
              <div class="fw-semibold"><?php echo e($p->name); ?></div>
              <div class="small text-muted">ID: <?php echo e($p->id); ?></div>
            </td>
            <td class="text-end"><?php echo e(number_format($p->price, 2)); ?></td>
            <td>
              <?php if($p->is_active): ?>
                <span class="badge text-bg-success">ACTIVE</span>
              <?php else: ?>
                <span class="badge text-bg-secondary">INACTIVE</span>
              <?php endif; ?>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('staff.products.edit', $p)); ?>">Edit</a>
              <form class="d-inline" method="POST" action="<?php echo e(route('staff.products.destroy', $p)); ?>"
                    onsubmit="return confirm('ลบสินค้านี้?');">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="5" class="text-center text-muted py-4">No products</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">
  <?php echo e($products->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.staff', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/paruj.le/Documents/restaurant_pos_phase1_laravel11_breeze/resources/views/staff/products/index.blade.php ENDPATH**/ ?>