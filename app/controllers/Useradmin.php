<?php
class Useradmin extends Controller
{
    private $model;
    public function __construct() { requireAdmin(); $this->model = $this->model('User'); }

    // Daftar semua akun pengguna (admin & pelanggan)
    public function index()
    {
        $this->view('admin/users_index', [
            'title' => 'Kelola Pengguna',
            'users' => $this->model->all(),
        ]);
    }
}
