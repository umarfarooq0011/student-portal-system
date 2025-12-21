<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | Student Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Notyf (Modern Premium Alerts) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
  <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900">
  <!-- soft background -->
  <div class="fixed inset-0 -z-10">
    <div class="absolute -top-24 -right-24 h-72 w-72 rounded-full bg-sky-200/60 blur-3xl"></div>
    <div class="absolute top-40 -left-24 h-72 w-72 rounded-full bg-indigo-200/60 blur-3xl"></div>
    <div class="absolute bottom-0 right-1/3 h-72 w-72 rounded-full bg-emerald-100/70 blur-3xl"></div>
  </div>

  <main class="min-h-screen flex items-center justify-center px-4 py-10">
    <section class="w-full max-w-5xl grid lg:grid-cols-2 overflow-hidden rounded-3xl bg-white shadow-xl border border-slate-200">

      <!-- Left (image / message) -->
      <div class="relative p-8 lg:p-10 bg-gradient-to-br from-sky-50 to-indigo-50">
        <div class="inline-flex items-center gap-2 rounded-full bg-white/80 px-3 py-1 text-sm font-semibold text-sky-700 border border-sky-100">
          🎓 Student Portal System
        </div>

        <h1 class="mt-5 text-3xl lg:text-4xl font-extrabold leading-tight text-slate-900">
          Welcome back!
        </h1>
        <p class="mt-2 text-slate-600">
          Login to check grades, manage assignments, and view announcements.
        </p>

        <!-- Login Illustration -->
        <div class="mt-8">
          <img
            src="../assets/images/loginimage.jpeg"
            alt="Student portal illustration"
            class="w-full h-72 object-contain"
          />
        </div>

        <div class="mt-6 grid grid-cols-3 gap-3 text-center">
          <div class="rounded-2xl bg-white/80 border border-slate-200 p-3">
            <p class="text-sm font-bold text-slate-900">Grades</p>
            <p class="text-xs text-slate-600 mt-1">View results</p>
          </div>
          <div class="rounded-2xl bg-white/80 border border-slate-200 p-3">
            <p class="text-sm font-bold text-slate-900">Assignments</p>
            <p class="text-xs text-slate-600 mt-1">Track tasks</p>
          </div>
          <div class="rounded-2xl bg-white/80 border border-slate-200 p-3">
            <p class="text-sm font-bold text-slate-900">Notices</p>
            <p class="text-xs text-slate-600 mt-1">Stay updated</p>
          </div>
        </div>
      </div>

      <!-- Right (form) -->
      <div class="p-8 lg:p-10">
        <div>
          <h2 class="text-2xl font-extrabold text-slate-900">Sign in</h2>
          <p class="mt-1 text-slate-600">Enter your credentials to continue.</p>
        </div>

        <form action="../controllers/login_controller.php" method="post" class="mt-8 space-y-4">

          <div>
            <label class="text-sm font-semibold text-slate-700">Email</label>
            <input
              type="email"
              name="email"
              placeholder="Enter your email"
              required
              class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder:text-slate-400
                     focus:outline-none focus:ring-4 focus:ring-sky-200 focus:border-sky-400"
            />
          </div>

          <div>
            <label class="text-sm font-semibold text-slate-700">Password</label>
            <input
              type="password"
              name="password"
              placeholder="Enter your password"
              required
              class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder:text-slate-400
                     focus:outline-none focus:ring-4 focus:ring-indigo-200 focus:border-indigo-400"
            />
          </div>

          <div>
            <label class="text-sm font-semibold text-slate-700">Login As</label>
            <select
              name="role"
              required
              class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                     focus:outline-none focus:ring-4 focus:ring-emerald-200 focus:border-emerald-400"
            >
              <option value="">Select role</option>
              <option value="student">Student</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          <button
            type="submit"
            class="w-full rounded-xl bg-sky-600 px-4 py-3 font-bold text-white
                   hover:bg-sky-700 transition shadow-md"
          >
            Login
          </button>

          <p class="pt-2 text-sm text-slate-600">
            Don't have an account?
            <a href="register.php" class="font-semibold text-sky-700 hover:text-sky-800 underline underline-offset-4">
              Sign up
            </a>
          </p>
        </form>
      </div>

    </section>
  </main>

  <script src="../assets/js/main.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const urlParams = new URLSearchParams(window.location.search);
      let alertShown = false;

      // Premium toast alerts
      const notyf = new Notyf({
        duration: 3500,
        position: { x: 'right', y: 'top' },
        dismissible: true
      });

      if (urlParams.has('error')) {
        notyf.error(urlParams.get('error'));
        alertShown = true;
      }

      if (urlParams.has('success')) {
        notyf.success(urlParams.get('success'));
        alertShown = true;
      }

      if (urlParams.has('logout')) {
        notyf.success('You have been logged out successfully.');
        alertShown = true;
      }

      if (urlParams.has('account_deleted')) {
        notyf.success('Your account has been permanently deleted.');
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
