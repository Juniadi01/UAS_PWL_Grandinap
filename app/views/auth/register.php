<?php require_once APPROOT . '/views/layouts/header.php'; ?>
<div class="auth-card" style="max-width:520px">
    <div class="card p-4">
        <div class="eyebrow mb-1">Bergabung</div>
        <h4 class="mb-1">Buat Akun GrandInap</h4>
        <p class="text-muted small mb-4">Sudah punya akun? <a href="<?= BASEURL ?>auth/login">Masuk</a></p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><ul class="mb-0 ps-3"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <form method="POST" action="<?= BASEURL ?>auth/register">
            <div class="mb-3"><label class="form-label" for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= e($old['name'] ?? '') ?>" required autofocus></div>
            <div class="mb-3"><label class="form-label" for="remail">Email</label>
                <input type="email" id="remail" name="email" class="form-control" value="<?= e($old['email'] ?? '') ?>" required></div>
            <div class="mb-3"><label class="form-label" for="phone">No. HP</label>
                <input type="text" id="phone" name="phone" class="form-control" value="<?= e($old['phone'] ?? '') ?>" placeholder="08xxxxxxxxxx"></div>
            <div class="row">
                <div class="col mb-3"><label class="form-label" for="pass">Password</label>
                    <div class="input-group">
                        <input type="password" id="pass" name="password" class="form-control" required>
                        <button class="btn btn-outline-ink" type="button" data-toggle-password="#pass" aria-label="Tampilkan"><i class="bi bi-eye"></i></button>
                    </div>
                    <small class="text-muted">Minimal 6 karakter.</small></div>
                <div class="col mb-3"><label class="form-label" for="pass2">Ulangi Password</label>
                    <input type="password" id="pass2" name="password2" class="form-control" required></div>
            </div>
            <button class="btn btn-gold w-100">Daftar Sekarang</button>
        </form>
    </div>
</div>
<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
