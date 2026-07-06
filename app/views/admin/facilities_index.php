<?php require_once APPROOT . '/views/layouts/admin_header.php'; ?>
<div class="card p-3 p-md-4">
    <div class="page-tools">
        <h6 class="title"><i class="bi bi-stars text-brass"></i> Daftar Fasilitas
            <span class="count-chip"><?= count($facilities) ?> data</span></h6>
        <a href="<?= BASEURL ?>facilityadmin/create" class="btn btn-gold btn-sm"><i class="bi bi-plus-lg"></i> Tambah Fasilitas</a>
    </div>
    <?php if (empty($facilities)): ?>
        <div class="empty-state"><i class="bi bi-stars"></i>
            <p class="mt-2 mb-3">Belum ada fasilitas terdaftar.</p>
            <a href="<?= BASEURL ?>facilityadmin/create" class="btn btn-ink btn-sm">Tambah fasilitas pertama</a></div>
    <?php else: ?>
    <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead><tr><th>#</th><th>Ikon</th><th>Nama</th><th>Kelas Ikon</th><th class="text-end">Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($facilities as $f): ?>
            <tr>
                <td class="text-muted"><?= $f->id ?></td>
                <td><i class="bi <?= e($f->icon) ?> fs-4 text-brass"></i></td>
                <td><strong><?= e($f->name) ?></strong></td>
                <td><code><?= e($f->icon) ?></code></td>
                <td class="text-end"><div class="actions justify-content-end">
                    <a href="<?= BASEURL ?>facilityadmin/edit/<?= $f->id ?>" class="btn btn-outline-primary btn-icon" title="Ubah"><i class="bi bi-pencil"></i></a>
                    <a href="<?= BASEURL ?>facilityadmin/delete/<?= $f->id ?>" class="btn btn-outline-danger btn-icon btn-delete" title="Hapus"><i class="bi bi-trash"></i></a>
                </div></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>
<?php require_once APPROOT . '/views/layouts/admin_footer.php'; ?>
