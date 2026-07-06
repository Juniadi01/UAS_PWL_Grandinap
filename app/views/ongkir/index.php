<?php require_once APPROOT . '/views/layouts/header.php'; ?>
<div class="mb-4">
    <div class="eyebrow">Layanan</div>
    <h2 class="section-title">Cek Ongkos Kirim</h2>
    <p class="text-secondary mb-0">
        Hitung estimasi biaya pengiriman barang antar kota di Indonesia menggunakan data resmi
        <strong>RajaOngkir by Komerce</strong>.
    </p>
</div>

<div class="row g-4">
    <!-- Form -->
    <div class="col-lg-5">
        <div class="card p-4">
            <form id="ongkirForm" novalidate>
                <h6 class="mb-3"><i class="bi bi-truck text-brass"></i> Detail Pengiriman</h6>

                <!-- Asal -->
                <div class="mb-2">
                    <label class="form-label small mb-1">Provinsi Asal</label>
                    <select class="form-select" id="provOrigin" required>
                        <option value="">Memuat provinsi...</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label small mb-1">Kota / Kabupaten Asal</label>
                    <select class="form-select" id="cityOrigin" required disabled>
                        <option value="">Pilih provinsi dulu</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small mb-1">Kecamatan Asal</label>
                    <select class="form-select" id="distOrigin" required disabled>
                        <option value="">Pilih kota dulu</option>
                    </select>
                </div>

                <hr class="my-3">

                <!-- Tujuan -->
                <div class="mb-2">
                    <label class="form-label small mb-1">Provinsi Tujuan</label>
                    <select class="form-select" id="provDest" required>
                        <option value="">Memuat provinsi...</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label small mb-1">Kota / Kabupaten Tujuan</label>
                    <select class="form-select" id="cityDest" required disabled>
                        <option value="">Pilih provinsi dulu</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small mb-1">Kecamatan Tujuan</label>
                    <select class="form-select" id="distDest" required disabled>
                        <option value="">Pilih kota dulu</option>
                    </select>
                </div>

                <!-- Berat & Kurir -->
                <div class="row g-2">
                    <div class="col-7">
                        <label class="form-label small mb-1">Berat (gram)</label>
                        <input type="number" class="form-control" id="weight" min="1" value="1000" required>
                    </div>
                    <div class="col-5">
                        <label class="form-label small mb-1">Kurir</label>
                        <select class="form-select" id="courier" required>
                            <option value="jne">JNE</option>
                            <option value="pos">POS</option>
                            <option value="tiki">TIKI</option>
                            <option value="sicepat">SiCepat</option>
                            <option value="jnt">J&amp;T</option>
                            <option value="anteraja">AnterAja</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-gold w-100 mt-4" id="btnHitung">
                    <i class="bi bi-calculator"></i> Hitung Ongkir
                </button>
            </form>
        </div>
    </div>

    <!-- Hasil -->
    <div class="col-lg-7">
        <div class="card p-4 h-100">
            <h6 class="mb-3"><i class="bi bi-receipt text-brass"></i> Hasil Estimasi</h6>
            <div id="ongkirResult">
                <div class="text-secondary text-center py-5">
                    <i class="bi bi-box-seam fs-1 d-block mb-2 opacity-50"></i>
                    Pilih provinsi, kota &amp; kecamatan asal dan tujuan, lalu klik <strong>Hitung Ongkir</strong>.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const BASE   = '<?= BASEURL ?>';
    const rupiah = n => 'Rp\u00a0' + (Number(n) || 0).toLocaleString('id-ID');
    const result = document.getElementById('ongkirResult');
    const btn    = document.getElementById('btnHitung');

    const provOrigin = document.getElementById('provOrigin');
    const cityOrigin = document.getElementById('cityOrigin');
    const distOrigin = document.getElementById('distOrigin');
    const provDest   = document.getElementById('provDest');
    const cityDest   = document.getElementById('cityDest');
    const distDest   = document.getElementById('distDest');

    function showError(msg) {
        result.innerHTML = '<div class="alert alert-danger mb-0"><i class="bi bi-exclamation-triangle"></i> ' + msg + '</div>';
    }

    function fillSelect(sel, items, placeholder) {
        sel.innerHTML = '<option value="">' + placeholder + '</option>' +
            items.map(i => `<option value="${i.id}">${i.name}</option>`).join('');
    }

    async function getJSON(url) {
        const r = await fetch(url);
        const j = await r.json().catch(() => ({ ok: false, error: 'Respons tidak valid.' }));
        if (!j.ok) throw new Error(j.error || 'Terjadi kesalahan.');
        return j.data;
    }

    // 1. Muat provinsi untuk kedua kolom
    async function loadProvinces() {
        try {
            const data = await getJSON(BASE + 'ongkir/provinces');
            fillSelect(provOrigin, data, 'Pilih provinsi asal');
            fillSelect(provDest,   data, 'Pilih provinsi tujuan');
        } catch (e) {
            provOrigin.innerHTML = provDest.innerHTML = '<option value="">Gagal memuat - ' + e.message + '</option>';
        }
    }

    // 2. Provinsi -> Kota
    async function loadCities(provSel, citySel, distSel) {
        citySel.disabled = true; distSel.disabled = true;
        distSel.innerHTML = '<option value="">Pilih kota dulu</option>';
        citySel.innerHTML = '<option value="">Memuat kota...</option>';
        if (!provSel.value) { citySel.innerHTML = '<option value="">Pilih provinsi dulu</option>'; return; }
        try {
            const data = await getJSON(BASE + 'ongkir/cities?province=' + encodeURIComponent(provSel.value));
            fillSelect(citySel, data, 'Pilih kota / kabupaten');
            citySel.disabled = false;
        } catch (e) {
            citySel.innerHTML = '<option value="">Gagal memuat: ' + e.message + '</option>';
        }
    }

    // 3. Kota -> Kecamatan
    async function loadDistricts(citySel, distSel) {
        distSel.disabled = true;
        distSel.innerHTML = '<option value="">Memuat kecamatan...</option>';
        if (!citySel.value) { distSel.innerHTML = '<option value="">Pilih kota dulu</option>'; return; }
        try {
            const data = await getJSON(BASE + 'ongkir/districts?city=' + encodeURIComponent(citySel.value));
            fillSelect(distSel, data, 'Pilih kecamatan');
            distSel.disabled = false;
        } catch (e) {
            distSel.innerHTML = '<option value="">Gagal memuat: ' + e.message + '</option>';
        }
    }

    provOrigin.addEventListener('change', () => loadCities(provOrigin, cityOrigin, distOrigin));
    cityOrigin.addEventListener('change', () => loadDistricts(cityOrigin, distOrigin));
    provDest.addEventListener('change',   () => loadCities(provDest, cityDest, distDest));
    cityDest.addEventListener('change',   () => loadDistricts(cityDest, distDest));

    // 4. Hitung ongkir memakai ID kecamatan
    document.getElementById('ongkirForm').addEventListener('submit', async (ev) => {
        ev.preventDefault();
        const origin = distOrigin.value;
        const dest   = distDest.value;
        if (!origin) { showError('Pilih kecamatan asal terlebih dahulu.'); return; }
        if (!dest)   { showError('Pilih kecamatan tujuan terlebih dahulu.'); return; }

        btn.disabled = true;
        const oldHTML = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menghitung...';
        result.innerHTML = '<div class="text-center py-5"><span class="spinner-border text-brass"></span></div>';

        try {
            const body = new URLSearchParams({
                origin, destination: dest,
                weight:  document.getElementById('weight').value,
                courier: document.getElementById('courier').value,
            });
            const r = await fetch(BASE + 'ongkir/cost', { method: 'POST', body });
            const j = await r.json();
            if (!j.ok) throw new Error(j.error || 'Terjadi kesalahan.');

            const results = j.data;
            if (!results || !results.length) {
                result.innerHTML = '<div class="alert alert-warning mb-0">Tidak ada layanan tersedia untuk rute ini.</div>';
                return;
            }

            // Komerce mengembalikan array layanan langsung: [{name, code, service, description, cost, etd}, ...]
            let html = '<div class="table-responsive"><table class="table table-sm align-middle mb-0">'
                     + '<thead class="table-light"><tr><th>Kurir</th><th>Layanan</th><th>Estimasi</th><th class="text-end">Biaya</th></tr></thead><tbody>';
            results.forEach(s => {
                let val = 0;
                if (typeof s.cost === 'number')      val = s.cost;
                else if (Array.isArray(s.cost))      val = s.cost[0] && s.cost[0].value ? s.cost[0].value : 0;
                else if (typeof s.value === 'number') val = s.value;
                const etd = s.etd ? String(s.etd).replace(/hari/i, '').replace(/day/i, '').trim() + ' hari' : '-';
                html += `<tr>
                    <td><span class="badge" style="background:var(--clr-brass)">${(s.code || '').toUpperCase()}</span></td>
                    <td><strong>${s.service || '-'}</strong><br><span class="small text-secondary">${s.description || ''}</span></td>
                    <td>${etd}</td>
                    <td class="text-end fw-semibold" style="color:var(--clr-brass)">${rupiah(val)}</td>
                </tr>`;
            });
            html += '</tbody></table></div>';
            result.innerHTML = html;
        } catch (e) {
            showError(e.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = oldHTML;
        }
    });

    loadProvinces();
})();
</script>
<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
