<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $__env->yieldContent('title', 'Staff'); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="<?php echo e(route('staff.orders.dashboard')); ?>">Restaurant POS</a>
    <div class="navbar-nav ms-3">
      <a class="nav-link text-white-50" href="<?php echo e(route('staff.monitor.submissions', ['status' => 'OPEN'])); ?>">Monitor</a>
    </div>

    <div class="ms-auto d-flex align-items-center gap-2">
      <span class="text-white-50 small">Logged in as <?php echo e(auth()->user()->name); ?></span>
      <form method="POST" action="<?php echo e(route('logout')); ?>">
        <?php echo csrf_field(); ?>
        <button class="btn btn-sm btn-outline-light" type="submit">Logout</button>
      </form>
    </div>
  </div>
</nav>

<main class="container py-4">
  <?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>
  <?php if($errors->any()): ?>
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">มีข้อผิดพลาด</div>
      <ul class="mb-0">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><?php echo e($e); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
  <?php endif; ?>
  <?php echo $__env->yieldContent('content'); ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /Users/paruj.le/Documents/restaurant_pos_phase1_laravel11_breeze/resources/views/layouts/staff.blade.php ENDPATH**/ ?>