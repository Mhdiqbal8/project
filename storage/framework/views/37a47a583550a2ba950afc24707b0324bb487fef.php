<?php if(session('success')): ?>
    <div class="alert alert-success mt-1">
        <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>

<?php if(session('failed')): ?>
    <div class="alert alert-danger mt-1">
        <?php echo e(session('failed')); ?>

    </div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\project_form\resources\views/components/alert.blade.php ENDPATH**/ ?>