<?php require_once APPROOT . '/views/layouts/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card p-4">
            <h4 class="mb-3"><i class="bi bi-cash-coin text-gold"></i> Pembayaran Reservasi #<?= $reservation->id ?></h4>

            <div class="bg-light rounded p-3 mb-3">
                <div class="d-flex justify-content-between"><span>Kamar</span><strong><?= e($reservation->room_name) ?></strong></div>
                <div class="d-flex justify-content-between"><span>Hotel</span><span><?= e($reservation->hotel_name) ?>, <?= e($reservation->city) ?></span></div>
                <div class="d-flex justify-content-between"><span>Tanggal</span><span><?= date('d/m/Y', strtotime($reservation->check_in)) ?> &rarr; <?= date('d/m/Y', strtotime($reservation->check_out)) ?></span></div>
                <div class="d-flex justify-content-between"><span>Malam / Tamu</span><span><?= $reservation->nights ?> malam, <?= $reservation->guests ?> tamu</span></div>
                <hr class="my-2">
                <div class="d-flex justify-content-between fs-5 fw-bold"><span>Total Bayar</span><span class="text-gold"><?= rupiah($reservation->total_price) ?></span></div>
            </div>

            <div class="alert alert-info small">
                <i class="bi bi-bank"></i> Silakan transfer ke: <strong>BCA 1234567890 a.n. GrandInap</strong>,
                lalu unggah bukti transfer di bawah ini.
            </div>

            <p>Status pembayaran: <?= statusBadge($payment->status) ?></p>

            <?php if ($payment->proof): ?>
                <p class="small mb-1">Bukti yang diunggah:</p>
                <img src="<?= UPLOAD_URL.'payments/'.e($payment->proof) ?>" class="img-fluid rounded border mb-3" style="max-height:260px">
            <?php endif; ?>

            <?php if (in_array($payment->status, ['unpaid','rejected'])): ?>
                <?php if ($payment->status === 'rejected'): ?>
                    <div class="alert alert-danger small">Bukti sebelumnya ditolak. Silakan unggah ulang bukti yang benar.</div>
                <?php endif; ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3"><label class="form-label">Upload Bukti Transfer</label>
                        <input type="file" name="proof" class="form-control" accept="image/*" required></div>
                    <button class="btn bg-gi text-white w-100"><i class="bi bi-upload"></i> Kirim Bukti Pembayaran</button>
                </form>
            <?php elseif ($payment->status === 'waiting'): ?>
                <div class="alert alert-warning"><i class="bi bi-hourglass-split"></i> Menunggu verifikasi admin.</div>
            <?php elseif ($payment->status === 'verified'): ?>
                <div class="alert alert-success"><i class="bi bi-check-circle"></i> Pembayaran terverifikasi. Terima kasih!</div>
            <?php endif; ?>

            <a href="<?= BASEURL ?>account" class="btn btn-link mt-2">&larr; Kembali ke reservasi saya</a>
        </div>
    </div>
</div>
<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
