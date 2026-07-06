<?php
/**
 * Ongkir.php — Integrasi API RajaOngkir by Komerce (v1)
 *
 * Endpoint yang dipakai:
 *   GET  /destination/province          -> daftar provinsi
 *   GET  /destination/city/{provinceId} -> daftar kota per provinsi
 *   POST /calculate/domestic-cost       -> hitung ongkir
 *
 * Format response Komerce: { "meta": {"code":200,...}, "data": [...] }
 */
class Ongkir extends Controller
{
    public function index()
    {
        $this->view('ongkir/index', ['title' => 'Cek Ongkir']);
    }

    // AJAX: daftar provinsi -> JSON [{id, name}]
    public function provinces()
    {
        ob_start();
        ini_set('display_errors', '0');

        $res = $this->call('/destination/province');
        if (!$res['ok']) {
            $this->json(['ok' => false, 'error' => $res['error']], 502);
        }
        $rows = $res['data']['data'] ?? [];
        $out  = array_map(fn($r) => [
            'id'   => $r['id']       ?? $r['province_id'] ?? '',
            'name' => $r['province'] ?? $r['name']        ?? '',
        ], $rows);
        $this->json(['ok' => true, 'data' => $out]);
    }

    // AJAX: daftar kota berdasarkan ID provinsi -> JSON [{id, name}]
    public function cities()
    {
        ob_start();
        ini_set('display_errors', '0');

        $provId = (int)($_GET['province'] ?? 0);
        if ($provId <= 0) {
            $this->json(['ok' => false, 'error' => 'ID provinsi tidak valid.'], 400);
        }
        $res = $this->call('/destination/city/' . $provId);
        if (!$res['ok']) {
            $this->json(['ok' => false, 'error' => $res['error']], 502);
        }
        $rows = $res['data']['data'] ?? [];
        $out  = array_map(function ($r) {
            $type = $r['type']      ?? '';
            $name = $r['city_name'] ?? $r['name'] ?? '';
            return [
                'id'   => $r['id'] ?? $r['city_id'] ?? '',
                'name' => trim($type . ' ' . $name),
            ];
        }, $rows);
        $this->json(['ok' => true, 'data' => $out]);
    }

    // AJAX: daftar kecamatan berdasarkan ID kota -> JSON [{id, name}]
    public function districts()
    {
        ob_start();
        ini_set('display_errors', '0');

        $cityId = (int)($_GET['city'] ?? 0);
        if ($cityId <= 0) {
            $this->json(['ok' => false, 'error' => 'ID kota tidak valid.'], 400);
        }
        $res = $this->call('/destination/district/' . $cityId);
        if (!$res['ok']) {
            $this->json(['ok' => false, 'error' => $res['error']], 502);
        }
        $rows = $res['data']['data'] ?? [];
        $out  = array_map(function ($r) {
            return [
                'id'   => $r['id'] ?? $r['district_id'] ?? '',
                'name' => $r['name'] ?? $r['district_name'] ?? '',
            ];
        }, $rows);
        $this->json(['ok' => true, 'data' => $out]);
    }

    // AJAX POST: hitung ongkir -> JSON {ok, data:[...]}
    public function cost()
    {
        ob_start();
        ini_set('display_errors', '0');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['ok' => false, 'error' => 'Metode tidak diizinkan.'], 405);
        }

        $origin  = (int)($_POST['origin']      ?? 0);
        $dest    = (int)($_POST['destination']  ?? 0);
        $weight  = (int)($_POST['weight']       ?? 0);
        $courier = preg_replace('/[^a-z]/', '', strtolower($_POST['courier'] ?? ''));
        $allowed = ['jne', 'pos', 'tiki', 'sicepat', 'jnt', 'anteraja', 'wahana', 'lion'];

        $errs = [];
        if ($origin <= 0)                    $errs[] = 'Kota asal wajib dipilih.';
        if ($dest   <= 0)                    $errs[] = 'Kota tujuan wajib dipilih.';
        if ($weight <= 0)                    $errs[] = 'Berat harus lebih dari 0 gram.';
        if (!in_array($courier, $allowed))   $errs[] = 'Kurir tidak valid.';
        if ($errs) { $this->json(['ok' => false, 'error' => implode(' ', $errs)], 400); }

        $body = http_build_query([
            'origin'      => $origin,
            'destination' => $dest,
            'weight'      => $weight,
            'courier'     => $courier,
            'price'       => 'lowest',
        ]);
        $res = $this->call('/calculate/domestic-cost', 'POST', $body);
        if (!$res['ok']) {
            $this->json(['ok' => false, 'error' => $res['error']], 502);
        }
        $this->json(['ok' => true, 'data' => $res['data']['data'] ?? []]);
    }

    // Helper cURL ke API Komerce
    private function call($path, $method = 'GET', $body = null)
    {
        if (!function_exists('curl_init')) {
            return ['ok' => false, 'error' => 'Ekstensi cURL belum aktif di php.ini.'];
        }
        if (!defined('RAJAONGKIR_KEY') || RAJAONGKIR_KEY === '' || RAJAONGKIR_KEY === 'MASUKKAN_API_KEY_KAMU') {
            return ['ok' => false, 'error' => 'API key RajaOngkir belum diisi di config/config.php.'];
        }

        $ch = curl_init(RAJAONGKIR_BASEURL . $path);
        $headers = ['key: ' . RAJAONGKIR_KEY, 'Accept: application/json'];
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        if ($method === 'POST') {
            $headers[] = 'content-type: application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $raw = curl_exec($ch);
        if ($raw === false) {
            $err = curl_error($ch); curl_close($ch);
            return ['ok' => false, 'error' => 'Gagal terhubung ke server RajaOngkir: ' . $err];
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $json = json_decode($raw, true);
        if (!is_array($json)) {
            return ['ok' => false, 'error' => 'Respons tidak valid dari RajaOngkir (HTTP ' . $httpCode . ').'];
        }

        // Format Komerce: { "meta": { "code": 200, "message": "..." }, "data": [...] }
        $metaCode = (int)($json['meta']['code'] ?? $httpCode);
        if ($metaCode !== 200) {
            return ['ok' => false, 'error' => $json['meta']['message'] ?? ('Error HTTP ' . $httpCode)];
        }
        return ['ok' => true, 'data' => $json];
    }
}
