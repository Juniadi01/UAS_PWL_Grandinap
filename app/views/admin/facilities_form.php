<?php require_once APPROOT . '/views/layouts/admin_header.php'; ?>
<div class="card p-4" style="max-width:520px">
    <form method="POST">
        <div class="mb-3"><label class="form-label">Nama Fasilitas</label>
            <input type="text" name="name" class="form-control" value="<?= e($facility->name ?? '') ?>" required placeholder="Contoh: Kolam Renang"></div>
        <div class="mb-3"><label class="form-label">Kelas Ikon (Bootstrap Icons)</label>
            <div class="input-group">
                <span class="input-group-text"><i id="iconPreview" class="bi <?= e($facility->icon ?? 'bi-check-circle') ?>"></i></span>
                <input type="text" name="icon" id="iconInput" class="form-control" value="<?= e($facility->icon ?? 'bi-check-circle') ?>" placeholder="bi-wifi">
            </div>
            <small class="text-muted">Cari nama ikon di
                <a href="https://icons.getbootstrap.com/" target="_blank">icons.getbootstrap.com</a>.
                Contoh: <code>bi-wifi</code>, <code>bi-water</code>, <code>bi-cup-hot</code>.</small>
        </div>
        <button class="btn bg-gi text-white">Simpan</button>
        <a href="<?= BASEURL ?>facilityadmin" class="btn btn-light">Batal</a>
    </form>
</div>
<script>
// Live preview ikon
const inp = document.getElementById('iconInput');
const prev = document.getElementById('iconPreview');
inp.addEventListener('input', () => { prev.className = 'bi ' + (inp.value || 'bi-check-circle'); });
</script>
<?php require_once APPROOT . '/views/layouts/admin_footer.php'; ?>
