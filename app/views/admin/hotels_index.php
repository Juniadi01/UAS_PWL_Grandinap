<?php require_once APPROOT . '/views/layouts/admin_header.php'; ?>
<div class="card p-3 p-md-4">
    <div class="page-tools">
        <h6 class="title"><i class="bi bi-buildings text-brass"></i> Daftar Hotel
            <span class="count-chip"><?= count($hotels) ?> data</span></h6>
        <a href="<?= BASEURL ?>hoteladmin/create" class="btn btn-gold btn-sm"><i class="bi bi-plus-lg"></i> Tambah Hotel</a>
    </div>
    <?php if (empty($hotels)): ?>
        <div class="empty-state"><i class="bi bi-buildings"></i>
            <p class="mt-2 mb-3">Belum ada hotel terdaftar.</p>
            <a href="<?= BASEURL ?>hoteladmin/create" class="btn btn-ink btn-sm">Tambah hotel pertama</a></div>
    <?php else: ?>
    <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead><tr><th>#</th><th>Foto</th><th>Nama</th><th>Kota</th><th>Bintang</th><th class="text-end">Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($hotels as $h): ?>
            <tr>
                <td class="text-muted"><?= $h->id ?></td>
                <td><img class="thumb" src="<?= photoSrc($h->photo, 'hotels', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=160') ?>" alt=""></td>
                <td><strong><?= e($h->name) ?></strong></td>
                <td><?= e($h->city) ?></td>
                <td><span class="star-gold"><?= str_repeat('<i class="bi bi-star-fill"></i>', (int)$h->star) ?></span></td>
                <td class="text-end"><div class="actions justify-content-end">
                    <a href="<?= BASEURL ?>hoteladmin/edit/<?= $h->id ?>" class="btn btn-outline-primary btn-icon" title="Ubah"><i class="bi bi-pencil"></i></a>
                    <a href="<?= BASEURL ?>hoteladmin/delete/<?= $h->id ?>" class="btn btn-outline-danger btn-icon btn-delete" title="Hapus"><i class="bi bi-trash"></i></a>
                </div></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>
<?php require_once APPROOT . '/views/layouts/admin_footer.php'; ?>
