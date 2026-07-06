<?php require_once APPROOT . '/views/layouts/header.php'; ?>
<div class="container-narrow">
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <a href="<?= BASEURL ?>account" class="btn btn-link px-0">&larr; Kembali</a>
        <button onclick="window.print()" class="btn btn-ink btn-sm"><i class="bi bi-printer"></i> Cetak / Simpan PDF</button>
    </div>

    <div class="card p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-4">
            <div>
                <div class="brand font-display fs-3"><i class="bi bi-building-fill-gear text-brass"></i> Grand<span class="text-brass">Inap</span></div>
                <p class="small text-muted mb-0">E-Voucher Reservasi</p>
            </div>
            <div class="text-end">
                <div class="small text-muted">No. Reservasi</div>
                <div class="fs-5 fw-bold">#GI<?= str_pad($reservation->id, 5, '0', STR_PAD_LEFT) ?></div>
                <?= statusBadge($reservation->status) ?>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-sm-6">
                <div class="eyebrow mb-2">Tamu</div>
                <p class="mb-0 fw-semibold"><?= e($reservation->customer_name) ?></p>
                <p class="small text-muted"><?= e($reservation->email) ?></p>
            </div>
            <div class="col-sm-6">
                <div class="eyebrow mb-2">Properti</div>
                <p class="mb-0 fw-semibold"><?= e($reservation->hotel_name) ?></p>
                <p class="small text-muted"><i class="bi bi-geo-alt"></i> <?= e($reservation->city) ?></p>
            </div>
        </div>

        <div class="rounded p-3 my-4" style="background:var(--gi-surface-2)">
            <div class="row text-center g-3">
                <div class="col-4 border-end"><div class="small text-muted">Check-in</div><div class="fw-bold"><?= date('d M Y', strtotime($reservation->check_in)) ?></div></div>
                <div class="col-4 border-end"><div class="small text-muted">Check-out</div><div class="fw-bold"><?= date('d M Y', strtotime($reservation->check_out)) ?></div></div>
                <div class="col-4"><div class="small text-muted">Malam</div><div class="fw-bold"><?= $reservation->nights ?></div></div>
            </div>
        </div>

        <table class="table">
            <tbody>
                <tr><td>Kamar</td><td class="text-end fw-semibold"><?= e($reservation->room_name) ?> (<?= e($reservation->type) ?>)</td></tr>
                <tr><td>Jumlah Tamu</td><td class="text-end"><?= $reservation->guests ?> orang</td></tr>
                <tr><td>Status Pembayaran</td><td class="text-end"><?= $payment ? statusBadge($payment->status) : '-' ?></td></tr>
                <tr class="border-top"><td class="fw-bold fs-5 pt-3">Total</td><td class="text-end fw-bold fs-5 text-brass pt-3"><?= rupiah($reservation->total_price) ?></td></tr>
            </tbody>
        </table>

        <p class="small text-muted text-center mt-4 mb-0">
            Tunjukkan e-voucher ini saat check-in. Terima kasih telah memilih GrandInap.<br>
            Dokumen ini sah tanpa tanda tangan basah.
        </p>
    </div>
</div>
<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
