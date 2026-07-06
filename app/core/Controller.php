<?php
/**
 * Controller.php (Base Controller)
 * Menyediakan helper model() dan view() untuk semua controller turunan.
 */
class Controller
{
    // Muat model
    protected function model($model)
    {
        require_once APPROOT . '/models/' . $model . '.php';
        return new $model();
    }

    // Render view + data
    protected function view($view, $data = [])
    {
        extract($data);
        $file = APPROOT . '/views/' . $view . '.php';
        if (file_exists($file)) {
            require_once $file;
        } else {
            die('View tidak ditemukan: ' . $view);
        }
    }

    // Render output JSON (untuk endpoint API/AJAX)
    protected function json($data, $code = 200)
    {
        // Buang output tak terduga (mis. warning/notice PHP) agar JSON tetap valid
        while (ob_get_level() > 0) { @ob_end_clean(); }
        if (!headers_sent()) {
            http_response_code($code);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
