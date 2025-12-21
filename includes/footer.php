    </main>

    <!-- Premium Footer -->
    <footer class="border-t border-slate-200/60 bg-white/50 backdrop-blur-sm py-4 px-6">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-sm text-slate-500">
                &copy; <?php echo date('Y'); ?> Student Portal System. All rights reserved.
            </p>
            <p class="text-sm text-slate-500">
                Crafted with <span class="text-red-500">&#10084;</span> by <span class="font-semibold text-violet-600"><a href="https://omer-awan.netlify.app/">OMER AWAN</a></span>
            </p>
        </div>
    </footer>
</div>

<!-- Page Loader -->
<div class="page-loader" id="pageLoader">
    <div class="loader-content">
        <div class="loader-spinner">
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
            <div class="loader-icon">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
        </div>
        <p class="loader-text">Loading...</p>
    </div>
</div>

<script>
// Hide page loader when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const loader = document.getElementById('pageLoader');
    if (loader) {
        loader.classList.remove('active');
    }
});
</script>

</body>
</html>
