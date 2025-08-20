<!-- Informasi Kontak -->
<div class="bg-light border-bottom">
  <div class="container py-2">
    <div class="row align-items-center">
      <!-- Kontak -->
      <div class="col-md d-flex align-items-center text-muted small">
        <i class="fa fa-phone mr-2 text-primary"></i>
        <a href="tel:+6212345678" class="text-muted mr-3">+62 1234 5678</a>
        <i class="fa fa-envelope mr-2 text-primary"></i>
        <a href="mailto:disdikporadiy@gmail.com" class="text-muted">disdikporadiy@gmail.com</a>
      </div>

      <!-- Sosial Media -->
      <div class="col-md-auto d-flex justify-content-end">
        <a href="https://www.facebook.com/dinasdikporadiy" target="_blank" class="text-muted mx-2">
          <i class="fa fa-facebook fa-lg"></i>
        </a>
        <a href="https://x.com/dikpora_diy" target="_blank" class="text-muted mx-2">
          <i class="fa fa-twitter fa-lg"></i>
        </a>
        <a href="https://www.instagram.com/dinasdikporadiy" target="_blank" class="text-muted mx-2">
          <i class="fa fa-instagram fa-lg"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top" id="ftco-navbar">
  <div class="container">
    <!-- Brand -->
    <a class="navbar-brand font-weight-bold text-primary" href="index.html">
      Pinjam Ruang <span class="text-dark">Disdikpora DIY</span>
    </a>

    <!-- Toggle Button -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" 
            aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="fa fa-bars"></span>
    </button>

    <!-- Menu -->
    <div class="collapse navbar-collapse" id="ftco-nav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item @if(\Request::is('/')) active @endif">
          <a href="/" class="nav-link">Beranda</a>
        </li>
        <li class="nav-item @if(\Request::is('rooms')) active @endif">
          <a href="{{ route('rooms') }}" class="nav-link">Daftar Ruangan</a>
        </li>
        <li class="nav-item @if(\Request::is('')) active @endif">
          <a href="{{ route('admin.login')}}" class="nav-link">Login</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
