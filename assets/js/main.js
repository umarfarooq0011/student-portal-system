// assets/js/main.js

document.addEventListener("DOMContentLoaded", function () {
  // LOGIN FORM VALIDATION
  const loginForm = document.querySelector('form[action*="login_controller.php"]');
  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      const email = loginForm.email.value.trim();
      const password = loginForm.password.value.trim();
      const role = loginForm.role.value;

      if (!email || !validateEmail(email)) {
        e.preventDefault();
        showError("Invalid Email", "Please enter a valid email address.");
        return;
      }

      // Only check for non-empty password (no length check for login)
      if (!password) {
        e.preventDefault();
        showError("Password Required", "Please enter your password.");
        return;
      }

      if (!role) {
        e.preventDefault();
        showError("Role Missing", "Please select a login role.");
        return;
      }

      showSuccess("Login Submitted", "Redirecting you...");
    });
  }

  // REGISTER FORM VALIDATION
  const registerForm = document.querySelector('form[action*="register_controller.php"]');
  if (registerForm) {
    registerForm.addEventListener("submit", function (e) {
      const fullName = registerForm.full_name.value.trim();
      const email = registerForm.email.value.trim();
      const password = registerForm.password.value.trim();
      const role = registerForm.role.value;

      if (fullName.length < 3) {
        e.preventDefault();
        showError("Invalid Name", "Full Name must be at least 3 characters.");
        return;
      }

      if (!email || !validateEmail(email)) {
        e.preventDefault();
        showError("Invalid Email", "Please enter a valid email address.");
        return;
      }

      if (password.length < 6) {
        e.preventDefault();
        showError("Weak Password", "Password must be at least 6 characters.");
        return;
      }

      if (!role) {
        e.preventDefault();
        showError("Role Missing", "Please select a registration role.");
        return;
      }

      showSuccess("Registration Submitted", "Redirecting you...");
    });
  }

  // Helper functions
  function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }

  function showError(title, message) {
    Swal.fire({
      icon: 'error',
      title: title,
      text: message,
      confirmButtonColor: '#a259ff'
    });
  }

  function showSuccess(title, message) {
    Swal.fire({
      icon: 'success',
      title: title,
      text: message,
      showConfirmButton: false,
      timer: 1500
    });
  }
});
