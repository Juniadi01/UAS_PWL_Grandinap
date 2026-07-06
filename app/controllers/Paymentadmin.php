<?php
class Paymentadmin extends Controller
{
    private $model;
    public function __construct() { requireAdmin(); $this->model = $this->model('Payment'); }

    public function index()
    {
        $this->view('admin/payments_index', [
            'title'    => 'Verifikasi Pembayaran',
            'payments' => $this->model->all(),
        ]);
    }

    // Verifikasi -> sekaligus konfirmasi reservasi
    public function verify($id)
    {
        $payment = $this->model->find($id);
        if ($payment) {
            $this->model->setStatus($id, 'verified', 'Pembayaran diverifikasi admin');
            $this->model('Reservation')->updateStatus($payment->reservation_id, 'confirmed');
            setFlash('success', 'Pembayaran diverifikasi & reservasi dikonfirmasi.');
        }
        redirect('paymentadmin');
    }

    public function reject($id)
    {
        $payment = $this->model->find($id);
        if ($payment) {
            $this->model->setStatus($id, 'rejected', 'Bukti pembayaran ditolak');
            setFlash('warning', 'Pembayaran ditolak.');
        }
        redirect('paymentadmin');
    }
}
