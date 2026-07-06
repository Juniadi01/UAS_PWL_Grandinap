<?php require_once APPROOT . '/views/layouts/admin_header.php'; ?>
<div class="d-flex justify-content-end gap-2 mb-3">
    <a href="<?= BASEURL ?>reportadmin/export" class="btn btn-sm btn-gold"><i class="bi bi-filetype-csv"></i> Unduh CSV</a>
    <button onclick="window.print()" class="btn btn-sm btn-outline-dark"><i class="bi bi-printer"></i> Cetak Laporan</button>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6"><div class="card stat-card text-white bg-gi p-3">
        <small>Total Pendapatan (Terverifikasi)</small><h3 class="mb-0 mt-1"><?= rupiah($totalRevenue) ?></h3></div></div>
    <div class="col-md-6"><div class="card stat-card p-3">
        <small class="text-muted">Total Reservasi</small><h3 class="mb-0 mt-1"><?= $totalReservasi ?></h3></div></div>
</div>

<div class="card p-3 mb-4">
    <h6 class="mb-3"><i class="bi bi-calendar3 text-gold"></i> Pendapatan per Bulan</h6>
    <table class="table table-sm">
        <thead class="table-light"><tr><th>Bulan</th><th>Jumlah Reservasi</th><th>Pendapatan</th></tr></thead>
        <tbody>
        <?php foreach ($revenuePerMonth as $row): ?>
            <tr><td><?= e($row->bulan) ?></td><td><?= $row->total_reservasi ?></td><td><?= rupiah($row->pendapatan) ?></td></tr>
        <?php endforeach; ?>
        <?php if (empty($revenuePerMonth)): ?><tr><td colspan="3" class="text-center text-muted">Belum ada pendapatan terverifikasi.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>

<div class="card p-3 mb-4">
    <h6 class="mb-3"><i class="bi bi-trophy text-gold"></i> Kamar Terlaris</h6>
    <table class="table table-sm">
        <thead class="table-light"><tr><th>#</th><th>Kamar</th><th>Hotel</th><th>Total Booking</th><th>Pendapatan</th></tr></thead>
        <tbody>
        <?php foreach ($bestRooms as $i => $b): ?>
            <tr><td><?= $i+1 ?></td><td><?= e($b->room_name) ?></td><td><?= e($b->hotel_name) ?></td>
                <td><?= $b->total_booking ?>x</td><td><?= rupiah($b->total_revenue) ?></td></tr>
        <?php endforeach; ?>
        <?php if (empty($bestRooms)): ?><tr><td colspan="5" class="text-center text-muted">Belum ada data.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>

<div class="card p-3">
    <h6 class="mb-3"><i class="bi bi-list-check text-gold"></i> Rekap Seluruh Reservasi</h6>
    <div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-light"><tr><th>#</th><th>Pelanggan</th><th>Kamar</th><th>Tanggal</th><th>Total</th><th>Status</th><th>Bayar</th></tr></thead>
        <tbody>
        <?php foreach ($allReservations as $r): ?>
            <tr><td><?= $r->id ?></td><td><?= e($r->customer_name) ?></td><td><?= e($r->room_name) ?></td>
                <td><small><?= date('d/m/Y', strtotime($r->check_in)) ?> - <?= date('d/m/Y', strtotime($r->check_out)) ?></small></td>
                <td><?= rupiah($r->total_price) ?></td><td><?= statusBadge($r->status) ?></td>
                <td><?= $r->payment_status ? statusBadge($r->payment_status) : '-' ?></td></tr>
        <?php endforeach; ?>
        <?php if (empty($allReservations)): ?><tr><td colspan="7" class="text-center text-muted">Belum ada reservasi.</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<?php require_once APPROOT . '/views/layouts/admin_footer.php'; ?>
