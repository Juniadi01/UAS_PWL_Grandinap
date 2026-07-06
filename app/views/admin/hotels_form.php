<?php require_once APPROOT . '/views/layouts/admin_header.php'; ?>
<div class="card p-4" style="max-width:700px">
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3"><label class="form-label">Nama Hotel</label>
            <input type="text" name="name" class="form-control" value="<?= e($hotel->name ?? '') ?>" required></div>
        <div class="row">
            <div class="col-md-8 mb-3"><label class="form-label">Kota</label>
                <input type="text" name="city" class="form-control" value="<?= e($hotel->city ?? '') ?>" required></div>
            <div class="col-md-4 mb-3"><label class="form-label">Bintang</label>
                <select name="star" class="form-select">
                    <?php for ($i=3;$i<=5;$i++): ?><option <?= (($hotel->star ?? 5)==$i)?'selected':'' ?>><?= $i ?></option><?php endfor; ?>
                </select></div>
        </div>
        <div class="mb-3"><label class="form-label">Alamat</label>
            <textarea name="address" class="form-control" rows="2"><?= e($hotel->address ?? '') ?></textarea></div>
        <div class="mb-3"><label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="3"><?= e($hotel->description ?? '') ?></textarea></div>
        <div class="mb-3"><label class="form-label">Foto Hotel</label>
            <input type="file" name="photo" class="form-control" accept="image/*">
            <?php if (!empty($hotel->photo)): ?><img src="<?= photoSrc($hotel->photo,'hotels') ?>" class="mt-2 rounded" width="120"><?php endif; ?>
        </div>
        <button class="btn bg-gi text-white">Simpan</button>
        <a href="<?= BASEURL ?>hoteladmin" class="btn btn-light">Batal</a>
    </form>
</div>
<?php require_once APPROOT . '/views/layouts/admin_footer.php'; ?>
