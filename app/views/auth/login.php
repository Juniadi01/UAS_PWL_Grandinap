<?php require_once APPROOT . '/views/layouts/header.php'; ?>
<div class="row justify-content-center align-items-stretch g-0 auth-card" style="max-width:840px">
    <div class="col-md-6 d-none d-md-block">
        <div class="auth-aside h-100 p-4 d-flex flex-column justify-content-between" style="border-top-right-radius:0;border-bottom-right-radius:0">
            <div class="eyebrow" style="color:var(--gi-champagne)">GrandInap</div>
            <div>
                <h3 class="text-white">Selamat datang kembali.</h3>
                <p class="small mb-0" style="color:rgba(255,255,255,.8)">Masuk untuk melanjutkan reservasi, mengelola favorit, dan menikmati pengalaman menginap mewah.</p>
            </div>
            <div class="small" style="color:rgba(255,255,255,.6)"><i class="bi bi-shield-check"></i> Data &amp; sandi terlindungi.</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 p-4" style="border-top-left-radius:0;border-bottom-left-radius:0">
            <h4 class="mb-1">Masuk</h4>
            <p class="text-muted small mb-4">Belum punya akun? <a href="<?= BASEURL ?>auth/register">Daftar gratis</a></p>
            <form method="POST" action="<?= BASEURL ?>auth/login">
                <div class="mb-3"><label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= e($email ?? '') ?>" required autofocus></div>
                <div class="mb-3"><label class="form-label" for="password">Password</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control" required>
                        <button class="btn btn-outline-ink" type="button" data-toggle-password="#password" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
                    </div></div>
                <button class="btn btn-gold w-100">Masuk</button>
            </form>
            <div class="mt-4 p-3 rounded small" style="background:var(--gi-surface-2)">
                <strong class="d-block mb-1"><i class="bi bi-info-circle text-brass"></i> Akun demo</strong>
                Admin: <code>admin@grandinap.com</code> / <code>admin123</code><br>
                Pelanggan: <code>juni@mail.com</code> / <code>customer123</code>
            </div>
        </div>
    </div>
</div>
<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
