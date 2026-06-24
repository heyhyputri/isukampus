    </div> <!-- End of .container-fluid -->
    
    <!-- Footer Credit -->
    <footer class="bg-white border-top py-3 text-center mt-auto">
        <p class="m-0 text-muted small">&copy; <?= date('Y'); ?> Isu Kampus - All Rights Reserved.</p>
    </footer>
</div> <!-- End of #page-content-wrapper -->
</div> <!-- End of #wrapper -->

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Sidebar Toggle Script -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const menuToggle = document.getElementById("menu-toggle");
        const wrapper = document.getElementById("wrapper");
        
        if (menuToggle) {
            menuToggle.addEventListener("click", function(e) {
                e.preventDefault();
                wrapper.classList.toggle("toggled");
            });
        }
    });
</script>
<!-- Custom JavaScript -->
<script src="assets/script.js"></script>
</body>
</html>
