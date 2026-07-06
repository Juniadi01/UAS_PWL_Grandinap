<?php require_once APPROOT . '/views/layouts/admin_header.php'; ?>
<div class="card p-4" style="max-width:760px">
    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-8 mb-3"><label class="form-label">Nama Kamar</label>
                <input type="text" name="name" class="form-control" value="<?= e($room->name ?? '') ?>" required></div>
            <div class="col-md-4 mb-3"><label class="form-label">Hotel</label>
                <select name="hotel_id" class="form-select" required>
                    <?php foreach ($hotels as $h): ?>
                        <option value="<?= $h->id ?>" <?= (($room->hotel_id ?? '')==$h->id)?'selected':'' ?>><?= e($h->name) ?></option>
                    <?php endforeach; ?>
                </select></div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3"><label class="form-label">Tipe</label>
                <select name="type" class="form-select">
                    <?php foreach (['Deluxe','Suite','Presidential Suite','Standard'] as $t): ?>
                        <option <?= (($room->type ?? '')==$t)?'selected':'' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div class="col-md-4 mb-3"><label class="form-label">Harga / malam</label>
                <input type="number" name="price" class="form-control" value="<?= e($room->price ?? '') ?>" required></div>
            <div class="col-md-2 mb-3"><label class="form-label">Kapasitas</label>
                <input type="number" name="capacity" class="form-control" min="1" value="<?= e($room->capacity ?? 2) ?>" required></div>
            <div class="col-md-2 mb-3"><label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="available" <?= (($room->status ?? '')=='available')?'selected':'' ?>>Tersedia</option>
                    <option value="maintenance" <?= (($room->status ?? '')=='maintenance')?'selected':'' ?>>Maintenance</option>
                </select></div>
        </div>
        <div class="mb-3"><label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="3"><?= e($room->description ?? '') ?></textarea></div>
        <div class="mb-3"><label class="form-label">Fasilitas</label>
            <div class="row">
                <?php foreach ($facilities as $f): ?>
                    <div class="col-md-4"><div class="form-check">
                        <input class="form-check-input" type="checkbox" name="facilities[]" value="<?= $f->id ?>"
                            id="f<?= $f->id ?>" <?= in_array($f->id, $roomFacilities)?'checked':'' ?>>
                        <label class="form-check-label" for="f<?= $f->id ?>"><i class="bi <?= e($f->icon) ?>"></i> <?= e($f->name) ?></label>
                    </div></div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="mb-3"><label class="form-label">Foto Kamar</label>
            <input type="file" name="photo" class="form-control" accept="image/*">
            <?php if (!empty($room->photo)): ?><img src="<?= photoSrc($room->photo,'rooms') ?>" class="mt-2 rounded" width="140"><?php endif; ?>
        </div>
        <button class="btn bg-gi text-white">Simpan</button>
        <a href="<?= BASEURL ?>roomadmin" class="btn btn-light">Batal</a>
    </form>
</div>
<?php require_once APPROOT . '/views/layouts/admin_footer.php'; ?>
