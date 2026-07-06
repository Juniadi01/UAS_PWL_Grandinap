<?php require_once APPROOT . '/views/layouts/admin_header.php'; ?>
<div class="card p-3 p-md-4">
    <div class="page-tools">
        <h6 class="title"><i class="bi bi-cash-coin text-brass"></i> Verifikasi Pembayaran
            <span class="count-chip"><?= count($payments) ?> data</span></h6>
    </div>
    <?php if (empty($payments)): ?>
        <div class="empty-state"><i class="bi bi-wallet2"></i><p class="mt-2 mb-0">Belum ada pembayaran masuk.</p></div>
    <?php else: ?>
    <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead><tr><th>#</th><th>Pelanggan</th><th>Kamar</th><th>Jumlah</th><th>Metode</th><th>Bukti</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($payments as $p): ?>
            <tr>
                <td class="text-muted"><?= $p->id ?></td>
                <td><strong><?= e($p->customer_name) ?></strong></td>
                <td><?= e($p->room_name) ?></td>
                <td><strong><?= rupiah($p->amount) ?></strong></td>
                <td><span class="badge bg-secondary-subtle text-secondary-emphasis"><?= e($p->method) ?></span></td>
                <td>
                    <?php if ($p->proof): ?>
                        <a href="<?= UPLOAD_URL.'payments/'.e($p->proof) ?>" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="bi bi-receipt"></i> Lihat</a>
                    <?php else: ?><span class="text-muted small">belum ada</span><?php endif; ?>
                </td>
                <td><?= statusBadge($p->status) ?></td>
                <td class="text-end">
                    <?php if ($p->status === 'waiting'): ?>
                        <div class="actions justify-content-end">
                        <a href="<?= BASEURL ?>paymentadmin/verify/<?= $p->id ?>" class="btn btn-success btn-sm btn-confirm" data-msg="Verifikasi pembayaran #<?= $p->id ?>? Reservasi akan otomatis dikonfirmasi."><i class="bi bi-check-lg"></i> Verifikasi</a>
                        <a href="<?= BASEURL ?>paymentadmin/reject/<?= $p->id ?>" class="btn btn-outline-danger btn-icon btn-confirm" data-msg="Tolak pembayaran #<?= $p->id ?>?" title="Tolak"><i class="bi bi-x-lg"></i></a>
                        </div>
                    <?php elseif ($p->status === 'verified'): ?>
                        <span class="text-success small"><i class="bi bi-check-circle-fill"></i> Terverifikasi</span>
                    <?php elseif ($p->status === 'rejected'): ?>
                        <span class="text-danger small"><i class="bi bi-x-circle"></i> Ditolak</span>
                    <?php else: ?>
                        <span class="text-muted small">Menunggu bayar</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>
<?php require_once APPROOT . '/views/layouts/admin_footer.php'; ?>
