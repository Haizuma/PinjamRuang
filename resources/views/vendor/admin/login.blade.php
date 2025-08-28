<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>{{ config('admin.title') }} | {{ trans('admin.login') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="{{ admin_asset('vendor/laravel-admin/AdminLTE/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ admin_asset('vendor/laravel-admin/font-awesome/css/font-awesome.min.css') }}">
<style>
  body {
    margin: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #00c853 0%, #2196f3 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  .login-container {
    display: flex;
    background: #fff;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    max-width: 850px;
    width: 100%;
    min-height: 450px;
  }
.left-box {
  flex: 1;
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: center; /* teks di tengah */
  align-items: center;
  padding: 20px;
  color: white;
  text-align: center;

  background: 
    linear-gradient(to bottom, rgba(0,200,83,0.6), rgba(33,150,243,0.6)),
    url('{{ asset('images/login-bg.jpg') }}') center center no-repeat;
  background-size: cover;
  border-top-left-radius: 15px;
  border-bottom-left-radius: 15px;
}


  .left-box h1 {
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 10px;
  }

  .left-box p {
    font-size: 14px;
    line-height: 1.6;
  }

  .right-box {
    flex: 1;
    padding: 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .right-box h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #2196f3;
    font-weight: bold;
  }

.left-box a.btn-beranda {
  margin-top: 20px;
  padding: 10px 20px;
  background: rgba(255, 255, 255, 0.2);
  border: 2px solid white;
  border-radius: 8px;
  color: white;
  text-decoration: none;
  font-weight: bold;
  transition: 0.3s;
  display: inline-block;
}

.left-box a.btn-beranda:hover {
  background: white;
  color: #2196f3; /* biru */
}

.input-group {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 30px;
    padding: 0 15px;
    background: #f9f9f9;
}

.input-group .fa {
    color: #888;
    font-size: 16px;
    margin-right: 10px;
}

.form-control {
    border: none;
    outline: none;
    box-shadow: none;
    flex: 1;
    padding: 12px;
    font-size: 14px;
    background: transparent;
}

.btn-login {
  width: 100%;
  border: none;
  border-radius: 30px;
  padding: 12px;
  background: linear-gradient(90deg, #00c853, #2196f3);
  color: #fff;
  font-weight: bold;
  transition: 0.3s;
  height: 45px; /* seragam dengan input */
}


  .btn-login:hover {
    opacity: 0.9;
  }

  .options {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
    margin-top: 10px;
  }

  /* Responsif */
  @media(max-width: 768px) {
    .login-container {
      flex-direction: column;
      max-width: 95%;
    }

 .left-box,.right-box {
      flex: unset;
      width: 100%;
    }
  }
</style>

</head>

<body>

  <div class="login-container">
    <div class="left-box">
      <h2>Selamat Datang</h2>
      <p>Akses sistem peminjaman ruang dengan mudah dan cepat</p>
      <a href="{{ url('/') }}" class="btn-beranda">Kembali ke Beranda</a>
    </div>
    <div class="right-box">
      <h2>Peminjaman Ruang</h2>
      <form action="{{ admin_url('auth/login') }}" method="post">
        @csrf
        <div class="input-group">
          <i class="fa fa-user"></i>
          <input type="text" class="form-control" placeholder="{{ trans('admin.username') }}" name="username"
            value="{{ old('username') }}">
        </div>
        <div class="input-group">
          <i class="fa fa-lock"></i>
          <input type="password" class="form-control" placeholder="{{ trans('admin.password') }}" name="password">
        </div>
        <button type="submit" class="btn-login">{{ trans('admin.login') }}</button>
        <div class="options">
          <label>
            <input type="checkbox" name="remember" value="1" {{ (!old('username') || old('remember')) ? 'checked' : '' }}>
            Ingat Pengguna
          </label>
          
        </div>
      </form>
    </div>
  </div>

</body>

</html>