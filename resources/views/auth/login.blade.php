<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  @include('components.css')
  <title>Login</title>
</head>
<body>
<div class="container" id="loginv2">
  <div class="row">
    <div class="col-md-6 offset-md-3">
      <h2 class="text-center text-dark mt-5">Login Form</h2>
      <div class="text-center mb-5 text-dark">Rumah Sakit Keluarga Kita</div>
      <div class="card my-5">

        <form action="{{ route('login') }}" method="POST" class="card-body cardbody-color p-lg-5">
          @csrf
          <div class="loginv2 text-center">
          <img src="{{ asset('assets/img/icons/rskk logo-02.png') }}" class="img-fluid profile-image-pic mb-5"
              width="200px" alt="profile" >
          </div>

          <div class="mb-3">
            <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" id="username" aria-describedby="emailHelp"
              placeholder="Username" required autofocus>
              @error('username')
                  <div class="invalid-feedback">
                      {{ $message }}
                  </div>
              @enderror
          </div>
          <div class="mb-3">
            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" value="{{ old('password') }}" id="password" placeholder="password" required autofocus>
            @error('password')
                  <div class="invalid-feedback">
                      {{ $message }}
                  </div>
              @enderror
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

