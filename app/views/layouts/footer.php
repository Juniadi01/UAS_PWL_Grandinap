</main>
<footer class="gi-footer pt-5 pb-4 mt-5">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-5">
                <div class="brand mb-2"><i class="bi bi-building-fill-gear" style="color:var(--gi-brass-2)"></i> Grand<span style="color:var(--gi-brass-2)">Inap</span></div>
                <p class="small mb-0" style="max-width:330px">Jaringan hotel mewah bintang 5 dengan layanan kelas dunia di kota-kota terbaik Indonesia.</p>
            </div>
            <div class="col-6 col-md-3">
                <h6 class="text-white mb-3">Jelajahi</h6>
                <ul class="list-unstyled small d-grid gap-2">
                    <li><a href="<?= BASEURL ?>">Beranda</a></li>
                    <li><a href="<?= BASEURL ?>rooms">Cari Kamar</a></li>
                    <li><a href="<?= BASEURL ?>auth/register">Daftar Akun</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-4">
                <h6 class="text-white mb-3">Kontak</h6>
                <ul class="list-unstyled small d-grid gap-2">
                    <li><i class="bi bi-geo-alt me-2"></i>Jl. Sudirman No. 1, Jakarta</li>
                    <li><i class="bi bi-telephone me-2"></i>(021) 1500-888</li>
                    <li><i class="bi bi-envelope me-2"></i>halo@grandinap.com</li>
                </ul>
            </div>
        </div>
        <hr class="my-4" style="border-color:rgba(255,255,255,.12)">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 small">
            <span>&copy; <?= date('Y') ?> GrandInap. Proyek Akhir Pengembangan Aplikasi Web.</span>
            <span class="text-secondary">Dibangun dengan PHP Native (MVC) &amp; MySQL</span>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.GI = { base: "<?= BASEURL ?>", auth: <?= isLoggedIn() ? 'true' : 'false' ?> };
    <?php if ($flash): ?>window.GI_FLASH = { type: "<?= e($flash['type']) ?>", message: "<?= e($flash['message']) ?>" };<?php endif; ?>
</script>
<script src="<?= BASEURL ?>public/assets/js/app.js?v=3"></script>
</body>
</html>
