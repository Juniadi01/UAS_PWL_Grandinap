<?php require_once APPROOT . '/views/layouts/header.php'; ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="<?= BASEURL ?>">Beranda</a></li>
        <li class="breadcrumb-item"><a href="<?= BASEURL ?>rooms">Kamar</a></li>
        <li class="breadcrumb-item active"><?= e($room->name) ?></li>
    </ol>
</nav>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="position-relative">
            <img src="<?= photoSrc($room->photo, 'rooms', 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=1200&auto=format&fit=crop') ?>"
                 class="img-fluid rounded w-100" style="max-height:420px;object-fit:cover;border-radius:var(--gi-radius)" alt="<?= e($room->name) ?>">
            <button class="wish-btn" data-wishlist="<?= $room->id ?>" type="button" aria-pressed="false" aria-label="Simpan ke favorit" style="width:46px;height:46px;font-size:1.2rem"><i class="bi bi-heart"></i></button>
        </div>

        <div class="card p-4 mt-3">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <span class="badge bg-ink"><?= e($room->type) ?></span>
                    <h1 class="h3 mt-2 mb-1"><?= e($room->name) ?></h1>
                    <p class="text-muted mb-2"><i class="bi bi-building"></i> <?= e($room->hotel_name) ?> · <i class="bi bi-geo-alt"></i> <?= e($room->city) ?></p>
                </div>
                <?php if ($summary && $summary->total > 0): ?>
                    <div class="text-end">
                        <div class="fs-3 fw-bold text-brass font-display"><?= $summary->avg_rating ?></div>
                        <?= starsHtml($summary->avg_rating) ?>
                        <div class="small text-muted"><?= $summary->total ?> ulasan</div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="d-flex gap-4 my-2 small">
                <span><i class="bi bi-people text-brass"></i> Kapasitas <?= $room->capacity ?> tamu</span>
                <span><?= str_repeat('<i class="bi bi-star-fill text-warning"></i>', (int)$room->star) ?> Hotel</span>
            </div>
            <p class="mt-2 mb-0"><?= nl2br(e($room->description)) ?></p>

            <h6 class="mt-4 mb-2">Fasilitas Kamar</h6>
            <div class="row g-2">
                <?php foreach ($facilities as $f): ?>
                    <div class="col-6 col-md-4"><span class="d-inline-flex align-items-center gap-2 small"><i class="bi <?= e($f->icon) ?> text-brass"></i> <?= e($f->name) ?></span></div>
                <?php endforeach; ?>
                <?php if (empty($facilities)): ?><span class="text-muted small">Belum ada fasilitas terdaftar.</span><?php endif; ?>
            </div>
        </div>

        <!-- ULASAN TAMU -->
        <div class="card p-4 mt-3">
            <h5 class="mb-3"><i class="bi bi-chat-quote text-brass"></i> Ulasan Tamu <?= $summary && $summary->total>0 ? '('.$summary->total.')' : '' ?></h5>
            <?php if (empty($reviews)): ?>
                <div class="empty-state py-4"><i class="bi bi-chat-square-heart d-block mb-2"></i>Belum ada ulasan. Jadilah yang pertama setelah menginap!</div>
            <?php else: ?>
                <div class="d-grid gap-3">
                <?php foreach ($reviews as $rv): ?>
                    <div class="d-flex gap-3">
                        <img src="<?= $rv->author_photo ? UPLOAD_URL.'profiles/'.e($rv->author_photo) : 'https://ui-avatars.com/api/?name='.urlencode($rv->author).'&background=0A1F3A&color=fff' ?>"
                             width="44" height="44" class="rounded-circle flex-shrink-0" style="object-fit:cover" alt="">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <strong><?= e($rv->author) ?></strong>
                                <?= starsHtml($rv->rating) ?>
                            </div>
                            <div class="small text-muted mb-1"><?= date('d M Y', strtotime($rv->created_at)) ?></div>
                            <p class="mb-0"><?= nl2br(e($rv->comment)) ?></p>
                        </div>
                    </div>
                    <hr class="my-0">
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- PANEL BOOKING (AJAX ketersediaan + harga) -->
    <div class="col-lg-5">
        <div class="card p-4 position-sticky" style="top:90px">
            <div class="d-flex justify-content-between align-items-baseline">
                <div><span class="h3 text-brass fw-bold font-display"><?= rupiah($room->price) ?></span><span class="text-muted"> /malam</span></div>
                <?php if ($summary && $summary->total>0): ?><span class="rating-pill"><i class="bi bi-star-fill"></i> <?= $summary->avg_rating ?></span><?php endif; ?>
            </div>
            <hr>
            <form id="bookingForm" method="POST" action="<?= BASEURL ?>book/store">
                <input type="hidden" name="room_id" value="<?= $room->id ?>">
                <div class="row g-2">
                    <div class="col-6"><label class="form-label small" for="check_in">Check-in</label>
                        <input type="date" name="check_in" id="check_in" class="form-control" min="<?= date('Y-m-d') ?>" required></div>
                    <div class="col-6"><label class="form-label small" for="check_out">Check-out</label>
                        <input type="date" name="check_out" id="check_out" class="form-control" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required></div>
                </div>
                <div class="mb-2 mt-2"><label class="form-label small" for="guests">Jumlah Tamu</label>
                    <input type="number" name="guests" id="guests" class="form-control" min="1" max="<?= $room->capacity ?>" value="1" required></div>

                <div id="availabilityBox" class="alert d-none py-2 my-3" role="status" aria-live="polite"></div>
                <div id="priceBox" class="d-none mb-3 p-3 rounded" style="background:var(--gi-surface-2)">
                    <div class="d-flex justify-content-between small"><span>Harga per malam</span><span><?= rupiah($room->price) ?></span></div>
                    <div class="d-flex justify-content-between small"><span>Jumlah malam</span><span id="nightsText">-</span></div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between fw-bold"><span>Total</span><span id="totalText" class="text-brass">-</span></div>
                </div>

                <?php if (isLoggedIn() && !isAdmin()): ?>
                    <button type="submit" id="btnBook" class="btn btn-gold w-100" disabled><i class="bi bi-calendar-check me-1"></i> Pesan Sekarang</button>
                    <p class="text-center small text-muted mt-2 mb-0">Belum ada biaya yang ditarik sekarang.</p>
                <?php elseif (isAdmin()): ?>
                    <div class="alert alert-secondary small mb-0">Kamu masuk sebagai admin. Gunakan akun pelanggan untuk memesan.</div>
                <?php else: ?>
                    <a href="<?= BASEURL ?>auth/login" class="btn btn-gold w-100">Masuk untuk Memesan</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<script>
// Simpan ke "Terakhir dilihat"
window.giRememberRoom && giRememberRoom({
    id: <?= (int)$room->id ?>,
    name: <?= json_encode($room->name) ?>,
    price: <?= json_encode(rupiah($room->price)) ?>,
    photo: <?= json_encode(photoSrc($room->photo, 'rooms', 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=400')) ?>
});

// ===== AJAX cek ketersediaan + kalkulasi harga otomatis =====
const checkIn=document.getElementById('check_in'),checkOut=document.getElementById('check_out'),
      box=document.getElementById('availabilityBox'),priceBox=document.getElementById('priceBox'),
      btnBook=document.getElementById('btnBook'),roomId=<?= (int)$room->id ?>;
function checkAvailability(){
    if(!checkIn.value||!checkOut.value)return;
    box.className='alert alert-info py-2 my-3';box.classList.remove('d-none');
    box.innerHTML='<span class="spinner-border spinner-border-sm"></span> Mengecek ketersediaan...';
    fetch('<?= BASEURL ?>rooms/availability?room_id='+roomId+'&check_in='+checkIn.value+'&check_out='+checkOut.value)
      .then(r=>r.json()).then(d=>{
        if(!d.ok){box.className='alert alert-warning py-2 my-3';box.innerHTML='<i class="bi bi-exclamation-triangle"></i> '+d.message;priceBox.classList.add('d-none');if(btnBook)btnBook.disabled=true;return;}
        if(d.available){
            box.className='alert alert-success py-2 my-3';box.innerHTML='<i class="bi bi-check-circle"></i> '+d.message;
            document.getElementById('nightsText').textContent=d.nights+' malam';
            document.getElementById('totalText').textContent=d.total_format;
            priceBox.classList.remove('d-none');if(btnBook)btnBook.disabled=false;
        }else{
            box.className='alert alert-danger py-2 my-3';box.innerHTML='<i class="bi bi-x-circle"></i> '+d.message;
            priceBox.classList.add('d-none');if(btnBook)btnBook.disabled=true;
        }
      }).catch(()=>{box.className='alert alert-danger py-2 my-3';box.innerHTML='Terjadi kesalahan koneksi.';});
}
checkIn.addEventListener('change',function(){
    const n=new Date(this.value);n.setDate(n.getDate()+1);
    checkOut.min=n.toISOString().split('T')[0];
    if(checkOut.value<=this.value)checkOut.value=checkOut.min;
    checkAvailability();
});
checkOut.addEventListener('change',checkAvailability);
</script>
<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
