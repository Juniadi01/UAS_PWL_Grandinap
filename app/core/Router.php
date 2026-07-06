<?php
/**
 * Router.php
 * Mengubah URL menjadi pemanggilan Controller->method(params).
 * Aturan: nama kelas controller = ucfirst(segmen pertama URL).
 * Contoh: /roomadmin/edit/5 -> Roomadmin->edit(5)
 */
class Router
{
    private $controller = 'Home';
    private $method     = 'index';
    private $params     = [];

    public function dispatch()
    {
        $url = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $parts = $url !== '' ? explode('/', $url) : [];

        // Tentukan controller
        if (!empty($parts[0])) {
            $candidate = ucfirst(strtolower($parts[0]));
            if (file_exists(APPROOT . '/controllers/' . $candidate . '.php')) {
                $this->controller = $candidate;
            } else {
                return $this->notFound("Controller '$candidate' tidak ditemukan");
            }
        }
        unset($parts[0]);

        require_once APPROOT . '/controllers/' . $this->controller . '.php';
        $controller = new $this->controller();

        // Tentukan method
        if (!empty($parts[1])) {
            if (method_exists($controller, $parts[1])) {
                $this->method = $parts[1];
            } else {
                return $this->notFound("Halaman tidak ditemukan");
            }
        }
        unset($parts[1]);

        // Sisa segmen jadi parameter
        $this->params = $parts ? array_values($parts) : [];

        call_user_func_array([$controller, $this->method], $this->params);
    }

    private function notFound($msg = 'Halaman tidak ditemukan')
    {
        http_response_code(404);
        echo "<div style='font-family:sans-serif;text-align:center;margin-top:80px'>";
        echo "<h1>404</h1><p>$msg</p>";
        echo "<a href='" . BASEURL . "'>Kembali ke beranda</a></div>";
    }
}
