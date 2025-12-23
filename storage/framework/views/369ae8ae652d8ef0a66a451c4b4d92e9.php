<?php $__env->startSection('title', 'Edit Product'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Edit Product</h3>
  <a class="btn btn-outline-secondary" href="<?php echo e(route('staff.products.index')); ?>">Back</a>
</div>

<?php if($errors->any()): ?>
  <div class="alert alert-danger">
    <div class="fw-semibold mb-1">Please fix the following:</div>
    <ul class="mb-0">
      <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <li><?php echo e($e); ?></li> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
  </div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('staff.products.update', $product)); ?>" enctype="multipart/form-data" class="card p-3">
  <?php echo csrf_field(); ?>
  <?php echo method_field('PUT'); ?>

  <div class="row g-3">
    <div class="col-md-8">
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input class="form-control" name="name" value="<?php echo e(old('name', $product->name)); ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Price</label>
        <input class="form-control" type="number" step="0.01" min="0" name="price" value="<?php echo e(old('price', $product->price)); ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Replace Image</label>
        <input class="form-control" type="file" name="image" accept="image/*">
        <div class="form-text">Upload to replace current image.</div>
      </div>

      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active" <?php echo e(old('is_active', $product->is_active) ? 'checked' : ''); ?>>
        <label class="form-check-label" for="active">Active</label>
      </div>

      <?php if($product->image_path): ?>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image">
          <label class="form-check-label" for="remove_image">Remove current image</label>
        </div>
      <?php endif; ?>

      <div class="d-flex gap-2">
        <button class="btn btn-primary" type="submit">Save</button>
        <a class="btn btn-outline-secondary" href="<?php echo e(route('staff.products.index')); ?>">Cancel</a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="border rounded p-2">
        <div class="small text-muted mb-2">Preview</div>
        <?php if($product->image_path): ?>
          <img src="<?php echo e(asset('storage/'.$product->image_path)); ?>" class="img-fluid rounded" style="object-fit:cover; width:100%; max-height:280px;">
        <?php else: ?>
          <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="height:280px;">
            <span class="text-muted">No image</span>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.staff', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/paruj.le/Documents/restaurant_pos_phase1_laravel11_breeze/resources/views/staff/products/edit.blade.php ENDPATH**/ ?>