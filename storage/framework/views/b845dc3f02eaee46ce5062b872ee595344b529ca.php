<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php echo $__env->make('components.css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <title>Login</title>
</head>
<body>
<div class="container" id="loginv2">
  <div class="row">
    <div class="col-md-6 offset-md-3">
      <h2 class="text-center text-dark mt-5">Login Form</h2>
      <div class="text-center mb-5 text-dark">Rumah Sakit Keluarga Kita</div>
      <div class="card my-5">

        <form action="<?php echo e(route('login')); ?>" method="POST" class="card-body cardbody-color p-lg-5">
          <?php echo csrf_field(); ?>
          <div class="loginv2 text-center">
          <img src="<?php echo e(asset('assets/img/icons/rskk logo-02.png')); ?>" class="img-fluid profile-image-pic mb-5"
              width="200px" alt="profile" >
          </div>

          <div class="mb-3">
            <input type="text" class="form-control <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="username" value="<?php echo e(old('username')); ?>" id="username" aria-describedby="emailHelp"
              placeholder="Username" required autofocus>
              <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback">
                      <?php echo e($message); ?>

                  </div>
              <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <div class="mb-3">
            <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="password" value="<?php echo e(old('password')); ?>" id="password" placeholder="password" required autofocus>
            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback">
                      <?php echo e($message); ?>

                  </div>
              <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <div class="text-center"><button type="submit" class="btn btn-success px-5 mb-5 w-100">Login</button></div>
          <div id="emailHelp" class="form-text text-center mb-5 text-dark">Jika belum memiliki akun? <a href="#" class="text-dark fw-bold"> Hubungi Unit IT</a>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\project_form\resources\views/auth/login.blade.php ENDPATH**/ ?>