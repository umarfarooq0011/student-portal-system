<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | Student Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f8f9fa;
    }

    .form-control {
      background-color: #1f1f1f;
      border: none;
      color: #fff;
      box-shadow: none;
    }

    .form-control:focus {
      background-color: #232323;
      color: #fff;
      border: 1.5px solid #a259ff;
      box-shadow: 0 0 0 2px rgba(162,89,255,0.15);
    }

    .form-control::placeholder {
      color: #b9b9b9;
      opacity: 1;
    }

    .form-control:focus::placeholder {
      color: #888;
      opacity: 1;
    }

    .btn-purple {
      background: linear-gradient(135deg, #a259ff, #8438ff);
      border: none;
      transition: 0.3s ease;
    }

    .btn-purple:hover {
      background: linear-gradient(135deg, #8438ff, #a259ff);
      box-shadow: 0 8px 20px rgba(130, 60, 255, 0.3);
    }

    .left-panel {
      background-color: #111;
      color: #fff;
      padding: 40px;
    }

    .right-panel {
      background-color: #a259ff;
      color: #fff;
      padding: 40px;
      text-align: center;
    }

    .right-panel img {
      max-width: 100%;
      height: auto;
    }

    @media (max-width: 767.98px) {
      .right-panel {
        order: -1;
      }
    }
  </style>
</head>
<body>

<div class="container-fluid">
  <div class="row g-0 min-vh-100 d-flex flex-column flex-md-row">

    <!-- Right Panel (Image, appears first on mobile) -->
    <div class="col-md-6 right-panel d-flex flex-column justify-content-center align-items-center">
      <div class="px-4 px-md-5">
        <img src="../assets/images/loginsvg.png" alt="Student Portal Illustration" class="mb-4">
        <h1 class="fw-bold">Welcome to Student Portal</h1>
        <p class="lead">Access your grades, assignments and announcements anytime.</p>
      </div>
    </div>

    <!-- Left Panel (Form) -->
    <div class="col-md-6 left-panel d-flex flex-column justify-content-center">
      <div class="px-4 px-md-5">
        <h2 class="mb-3">Welcome Back!</h2>
        <p class="mb-4">Enter your login credentials below to access your portal.</p>

        <form action="../controllers/login_controller.php" method="post">
          <div class="mb-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
          </div>

          <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
          </div>

          <div class="mb-3">
            <select name="role" class="form-control" required>
              <option value="">Login As</option>
              <option value="student">Student</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          <button type="submit" class="btn btn-purple w-100 text-white">Login</button>

          <p class="mt-3 text-white">
            Don't have an account?
            <a href="register.php" class="text-info text-decoration-none">Sign up</a>
          </p>
        </form>
      </div>
    </div>

  </div>
</div>




<script src="../assets/js/main.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    let alertShown = false;
    if (urlParams.has('error')) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: urlParams.get('error'),
        confirmButtonColor: '#a259ff'
      });
      alertShown = true;
    }
    if (urlParams.has('success')) {
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: urlParams.get('success'),
        confirmButtonColor: '#a259ff'
      });
      alertShown = true;
    }
    if (urlParams.has('logout')) {
      Swal.fire({
        icon: 'success',
        title: 'Logged Out',
        text: 'You have been logged out successfully.',
        confirmButtonColor: '#a259ff'
      });
      alertShown = true;
    }
    // Remove query params after showing alert so it doesn't repeat on refresh
    if (alertShown) {
      if (window.history.replaceState) {
        const cleanUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
        window.history.replaceState({}, document.title, cleanUrl);
      }
    }
  });
</script>

</body>
</html>
