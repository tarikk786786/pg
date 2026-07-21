</div> <!-- Close pi-content -->

<footer class="py-4 mt-auto border-top bg-white">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">
                Copyright &copy; <?php echo htmlspecialchars($site_settings['brand_name'] ?? 'Dezo'); ?> 2026. All Rights Reserved<br>
                Developer By <a href="https://tarikislam.in" class="text-decoration-none fw-bold" style="color: #07352D;">tarikislam.in</a> | Made in India
            </div>
            <div>
                <a href="#" class="text-decoration-none text-muted me-3">Privacy Policy</a>
                <a href="#" class="text-decoration-none text-muted">Terms &amp; Conditions</a>
            </div>
        </div>
    </div>
</footer>

</main> <!-- Close pi-main -->

<!-- Common Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Sidebar Responsive Toggle Handler -->
<script>
    (function() {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebarToggle');
        const backdrop = document.getElementById('sidebarBackdrop');

        if (toggle && sidebar && backdrop) {
            function openSidebar() {
                sidebar.classList.add('show');
                backdrop.classList.add('show');
                toggle.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
                toggle.classList.remove('active');
                document.body.style.overflow = '';
            }

            toggle.addEventListener('click', function(e) {
                e.stopPropagation();
                if (sidebar.classList.contains('show')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });

            backdrop.addEventListener('click', closeSidebar);

            // Close on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && sidebar.classList.contains('show')) {
                    closeSidebar();
                }
            });
        }
    })();
</script>
</body>
</html>
