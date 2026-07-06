<?php require_once APPROOT . '/views/layouts/admin_header.php'; ?>

<!-- Kartu statistik -->
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3"><div class="card stat-card feature">
        <div class="stat-label">Total Pendapatan</div>
        <div class="stat-value"><?= rupiah($totalRevenue) ?></div>
        <i class="bi bi-cash-stack stat-icon"></i></div></div>
    <div class="col-6 col-xl-3"><div class="card stat-card">
        <div class="stat-label">Total Reservasi</div>
        <div class="stat-value"><?= $totalReservasi ?></div>
        <i class="bi bi-calendar-check stat-icon text-brass"></i></div></div>
    <div class="col-6 col-xl-3"><div class="card stat-card">
        <div class="stat-label">Total Kamar</div>
        <div class="stat-value"><?= $totalKamar ?></div>
        <i class="bi bi-door-open stat-icon text-brass"></i></div></div>
    <div class="col-6 col-xl-3"><div class="card stat-card">
        <div class="stat-label">Total Hotel</div>
        <div class="stat-value"><?= $totalHotel ?></div>
        <i class="bi bi-buildings stat-icon text-brass"></i></div></div>
</div>

<!-- Aksi cepat -->
<div class="row g-3 mb-4">
    <div class="col-md-6"><div class="card p-3 d-flex flex-row align-items-center justify-content-between">
        <div><div class="stat-label">Menunggu Verifikasi Pembayaran</div><div class="stat-value"><?= $menungguBayar ?></div></div>
        <a href="<?= BASEURL ?>paymentadmin" class="btn btn-gold btn-sm">Proses <i class="bi bi-arrow-right"></i></a>
    </div></div>
    <div class="col-md-6"><div class="card p-3 d-flex flex-row align-items-center justify-content-between">
        <div><div class="stat-label">Reservasi Pending</div><div class="stat-value"><?= $pending ?></div></div>
        <a href="<?= BASEURL ?>reservationadmin" class="btn btn-outline-ink btn-sm">Kelola <i class="bi bi-arrow-right"></i></a>
    </div></div>
</div>

<!-- Grafik -->
<div class="row g-3 mb-4">
    <div class="col-lg-8"><div class="card p-3">
        <h6 class="mb-3"><i class="bi bi-graph-up text-brass"></i> Pendapatan per Bulan</h6>
        <div class="chart-box"><canvas id="chartRevenue"></canvas></div>
    </div></div>
    <div class="col-lg-4"><div class="card p-3">
        <h6 class="mb-3"><i class="bi bi-pie-chart text-brass"></i> Status Reservasi</h6>
        <div class="chart-box chart-box-sm"><canvas id="chartStatus"></canvas></div>
    </div></div>
</div>

<!-- Kamar terlaris -->
<div class="card p-3">
    <h6 class="mb-3"><i class="bi bi-trophy text-brass"></i> Kamar Terlaris</h6>
    <div class="table-responsive">
    <table class="table table-sm align-middle mb-0">
        <thead class="table-light"><tr><th>#</th><th>Kamar</th><th>Hotel</th><th>Booking</th><th>Pendapatan</th></tr></thead>
        <tbody>
        <?php foreach ($bestRooms as $i => $b): ?>
            <tr><td><?= $i+1 ?></td><td class="fw-semibold"><?= e($b->room_name) ?></td><td><?= e($b->hotel_name) ?></td>
                <td><span class="badge bg-ink"><?= $b->total_booking ?>x</span></td><td><?= rupiah($b->total_revenue) ?></td></tr>
        <?php endforeach; ?>
        <?php if (empty($bestRooms)): ?><tr><td colspan="5" class="text-center text-muted py-3">Belum ada data.</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<?php
// Siapkan data grafik untuk JS
$labels  = array_map(fn($r) => $r->bulan, $revenuePerMonth);
$values  = array_map(fn($r) => (float)$r->pendapatan, $revenuePerMonth);
$pageScript = '<script>
(function(){
  const brass = "#C9A24B", ink = "#0A1F3A";
  const gridColor = getComputedStyle(document.documentElement).getPropertyValue("--gi-line") || "rgba(0,0,0,.08)";
  const tick = getComputedStyle(document.documentElement).getPropertyValue("--gi-muted") || "#888";

  new Chart(document.getElementById("chartRevenue"), {
    type: "bar",
    data: {
      labels: ' . json_encode($labels ?: ['-']) . ',
      datasets: [{
        label: "Pendapatan (Rp)",
        data: ' . json_encode($values ?: [0]) . ',
        backgroundColor: brass, borderRadius: 8, maxBarThickness: 46
      }]
    },
    options: {
      responsive:true, maintainAspectRatio:false,
      plugins: { legend: { display:false } },
      scales: {
        y: { ticks:{ color:tick, callback:v=>"Rp "+(v/1000000)+"jt" }, grid:{ color:gridColor } },
        x: { ticks:{ color:tick }, grid:{ display:false } }
      }
    }
  });

  new Chart(document.getElementById("chartStatus"), {
    type: "doughnut",
    data: {
      labels: ["Confirmed","Checked-out","Pending","Cancelled"],
      datasets: [{
        data: [' . $statusCounts['confirmed'] . ',' . $statusCounts['checked_out'] . ',' . $statusCounts['pending'] . ',' . $statusCounts['cancelled'] . '],
        backgroundColor: [ink, brass, "#9aa6b2", "#e23b5a"], borderWidth: 0
      }]
    },
    options: { responsive:true, maintainAspectRatio:false, cutout: "62%", plugins: { legend: { position:"bottom", labels:{ color:tick, boxWidth:12, padding:14 } } } }
  });
})();
</script>';
?>
<?php require_once APPROOT . '/views/layouts/admin_footer.php'; ?>
