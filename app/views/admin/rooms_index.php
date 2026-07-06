<?php require_once APPROOT . '/views/layouts/admin_header.php'; ?>
<div class="card p-3 p-md-4">
    <div class="page-tools">
        <h6 class="title"><i class="bi bi-door-open text-brass"></i> Daftar Kamar
            <span class="count-chip"><?= count($rooms) ?> data</span></h6>
        <a href="<?= BASEURL ?>roomadmin/create" class="btn btn-gold btn-sm"><i class="bi bi-plus-lg"></i> Tambah Kamar</a>
    </div>
    <?php if (empty($rooms)): ?>
        <div class="empty-state"><i class="bi bi-door-open"></i>
            <p class="mt-2 mb-3">Belum ada kamar terdaftar.</p>
            <a href="<?= BASEURL ?>roomadmin/create" class="btn btn-ink btn-sm">Tambah kamar pertama</a></div>
    <?php else: ?>
    <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead><tr><th>#</th><th>Foto</th><th>Nama</th><th>Hotel</th><th>Tipe</th><th>Harga</th><th>Kap.</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($rooms as $r): ?>
            <tr>
                <td class="text-muted"><?= $r->id ?></td>
                <td><img class="thumb" src="<?= photoSrc($r->photo, 'rooms', 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=160') ?>" alt=""></td>
                <td><strong><?= e($r->name) ?></strong></td>
                <td><?= e($r->hotel_name) ?><br><small class="text-muted"><?= e($r->city) ?></small></td>
                <td><span class="badge bg-gi"><?= e($r->type) ?></span></td>
                <td><?= rupiah($r->price) ?></td>
                <td><?= $r->capacity ?> <small class="text-muted">org</small></td>
                <td><?= statusBadge($r->status) ?></td>
                <td class="text-end"><div class="actions justify-content-end">
                    <a href="<?= BASEURL ?>roomadmin/edit/<?= $r->id ?>" class="btn btn-outline-primary btn-icon" title="Ubah"><i class="bi bi-pencil"></i></a>
                    <a href="<?= BASEURL ?>roomadmin/delete/<?= $r->id ?>" class="btn btn-outline-danger btn-icon btn-delete" title="Hapus"><i class="bi bi-trash"></i></a>
                </div></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>
<?php require_once APPROOT . '/views/layouts/admin_footer.php'; ?>
