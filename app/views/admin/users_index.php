<?php require_once APPROOT . '/views/layouts/admin_header.php'; ?>
<div class="card p-3 p-md-4">
    <div class="page-tools">
        <h6 class="title"><i class="bi bi-people text-brass"></i> Kelola Pengguna
            <span class="count-chip"><?= count($users) ?> akun</span></h6>
    </div>

    <div class="alert alert-secondary d-flex gap-2 align-items-start small" role="alert" style="border-radius:var(--gi-radius-sm)">
        <i class="bi bi-shield-lock-fill text-brass mt-1"></i>
        <div>Demi keamanan, kata sandi disimpan sebagai <strong>hash bcrypt satu arah</strong> dan
        <strong>tidak dapat dikembalikan</strong> menjadi teks asli. Kolom "Kata Sandi" menampilkan nilai hash yang tersimpan di basis data.</div>
    </div>

    <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead><tr>
            <th>#</th><th>Nama</th><th>Email</th><th>Peran</th><th>No. HP</th>
            <th>Kata Sandi (hash bcrypt)</th><th>Bergabung</th>
        </tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td class="text-muted"><?= $u->id ?></td>
                <td class="d-flex align-items-center gap-2">
                    <img class="thumb-sq rounded-circle" style="width:34px;height:34px;object-fit:cover"
                         src="<?= $u->photo ? UPLOAD_URL.'profiles/'.e($u->photo) : 'https://ui-avatars.com/api/?name='.urlencode($u->name).'&background=0A1F3A&color=fff&bold=true' ?>" alt="">
                    <strong><?= e($u->name) ?></strong>
                </td>
                <td><?= e($u->email) ?></td>
                <td>
                    <?php if ($u->role === 'admin'): ?>
                        <span class="badge bg-gi">admin</span>
                    <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary-emphasis">customer</span>
                    <?php endif; ?>
                </td>
                <td><?= e($u->phone ?: '-') ?></td>
                <td style="max-width:260px">
                    <code class="d-inline-block text-truncate align-bottom" style="max-width:200px;font-size:.72rem"
                          title="<?= e($u->password) ?>"><?= e($u->password) ?></code>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1 ms-1"
                            data-copy="<?= e($u->password) ?>" title="Salin hash"><i class="bi bi-clipboard"></i></button>
                </td>
                <td><small class="text-muted"><?= $u->created_at ? date('d M Y', strtotime($u->created_at)) : '-' ?></small></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($users)): ?><tr><td colspan="7" class="text-center text-muted py-3">Belum ada pengguna.</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<?php require_once APPROOT . '/views/layouts/admin_footer.php'; ?>
