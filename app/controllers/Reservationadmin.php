<?php
class Reservationadmin extends Controller
{
    private $model;
    public function __construct() { requireAdmin(); $this->model = $this->model('Reservation'); }

    public function index()
    {
        $this->view('admin/reservations_index', [
            'title'        => 'Manajemen Reservasi',
            'reservations' => $this->model->all(),
        ]);
    }

    // Ubah status reservasi (confirmed, checked_in, checked_out, cancelled)
    public function status($id, $newStatus = null)
    {
        $allowed = ['pending','confirmed','checked_in','checked_out','cancelled'];
        if (in_array($newStatus, $allowed)) {
            $this->model->updateStatus($id, $newStatus);
            setFlash('success', 'Status reservasi diperbarui menjadi: ' . $newStatus);
        }
        redirect('reservationadmin');
    }
}
