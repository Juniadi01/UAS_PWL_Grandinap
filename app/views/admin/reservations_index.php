<?php require_once APPROOT . '/views/layouts/admin_header.php'; ?>
<div class="card p-3 p-md-4">
    <div class="page-tools">
        <h6 class="title"><i class="bi bi-calendar-check text-brass"></i> Semua Reservasi
            <span class="count-chip"><?= count($reservations) ?> data</span></h6>
    </div>
    <?php if (empty($reservations)): ?>
        <div class="empty-state"><i class="bi bi-calendar-x"></i><p class="mt-2 mb-0">Belum ada reservasi masuk.</p></div>
    <?php else: ?>
    <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead><tr><th>#</th><th>Pelanggan</th><th>Kamar</th><th>Tanggal</th><th>Malam</th><th>Total</th><th>Bayar</th><th>Status</th><th class="text-end">Ubah</th></tr></thead>
        <tbody>
        <?php foreach ($reservations as $r): ?>
            <tr>
                <td class="text-muted"><?= $r->id ?></td>
                <td><strong><?= e($r->customer_name) ?></strong></td>
                <td><?= e($r->room_name) ?><br><small class="text-muted"><?= e($r->hotel_name) ?></small></td>
                <td><small><?= date('d M Y', strtotime($r->check_in)) ?><br><i class="bi bi-arrow-down text-muted"></i> <?= date('d M Y', strtotime($r->check_out)) ?></small></td>
                <td><?= $r->nights ?></td>
                <td><strong><?= rupiah($r->total_price) ?></strong></td>
                <td><?= $r->payment_status ? statusBadge($r->payment_status) : '<span class="text-muted small">-</span>' ?></td>
                <td><?= statusBadge($r->status) ?></td>
                <td class="text-end">
                    <div class="dropdown">
                        <button class="btn btn-outline-dark btn-sm dropdown-toggle" data-bs-toggle="dropdown">Ubah</button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php
                            $opsi = ['confirmed'=>['Konfirmasi','bi-check2-circle'],'checked_in'=>['Check-in','bi-box-arrow-in-right'],'checked_out'=>['Check-out','bi-box-arrow-right'],'cancelled'=>['Batalkan','bi-x-circle']];
                            foreach ($opsi as $st => $m):
                                if ($st === $r->status) continue; ?>
                                <li><a class="dropdown-item btn-confirm" data-msg="Ubah status reservasi #<?= $r->id ?> menjadi <?= $m[0] ?>?"
                                       href="<?= BASEURL ?>reservationadmin/status/<?= $r->id ?>/<?= $st ?>"><i class="bi <?= $m[1] ?> me-1 text-muted"></i><?= $m[0] ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>
<?php require_once APPROOT . '/views/layouts/admin_footer.php'; ?>
