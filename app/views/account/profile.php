<?php require_once APPROOT . '/views/layouts/header.php'; ?>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="eyebrow mb-2">Akun Saya</div>
        <h2 class="section-title mb-4">Profil</h2>
        <div class="card p-4">
            <div class="text-center mb-3">
                <img src="<?= $user->photo ? UPLOAD_URL.'profiles/'.e($user->photo) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0A1F3A&color=fff&size=160&bold=true' ?>"
                     class="rounded-circle" width="110" height="110" style="object-fit:cover;border:3px solid var(--gi-line)" alt="Foto profil">
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3"><label class="form-label">Nama lengkap</label>
                    <input type="text" name="name" class="form-control" value="<?= e($user->name) ?>" required></div>
                <div class="mb-3"><label class="form-label">Email</label>
                    <input type="email" class="form-control" value="<?= e($user->email) ?>" disabled>
                    <small class="text-muted">Email tidak dapat diubah.</small></div>
                <div class="mb-3"><label class="form-label">No. HP</label>
                    <input type="text" name="phone" class="form-control" value="<?= e($user->phone) ?>" placeholder="08xxxxxxxxxx"></div>
                <div class="mb-3"><label class="form-label">Foto profil</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                    <small class="text-muted">jpg/png/webp, maks 2 MB</small></div>
                <button class="btn btn-ink w-100"><i class="bi bi-check2 me-1"></i> Simpan perubahan</button>
            </form>
        </div>
        <div class="text-center mt-3">
            <a href="<?= BASEURL ?>account" class="text-muted small"><i class="bi bi-arrow-left"></i> Kembali ke reservasi saya</a>
        </div>
    </div>
  </div>
</div>
<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
