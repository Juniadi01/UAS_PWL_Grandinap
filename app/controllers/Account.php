<?php
class Account extends Controller
{
    public function __construct() { requireLogin(); }

    // Dashboard pelanggan daftar reservasi
    public function index()
    {
        $resModel    = $this->model('Reservation');
        $reviewModel = $this->model('Review');
        $reservations = $resModel->byUser(currentUser()['id']);

        // Tandai reservasi yang sudah pernah diulas
        foreach ($reservations as $r) {
            $r->reviewed = $reviewModel->existsForReservation($r->id);
        }

        $this->view('account/index', [
            'title'        => 'Reservasi Saya',
            'reservations' => $reservations,
        ]);
    }

    // Pelanggan memberi ulasan untuk reservasi yang sudah checked_out
    public function review($reservationId = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('account');

        $resModel = $this->model('Reservation');
        $reservation = $resModel->find($reservationId);

        // Validasi kepemilikan & syarat
        if (!$reservation || $reservation->user_id != currentUser()['id']) {
            setFlash('danger', 'Reservasi tidak ditemukan.'); redirect('account');
        }
        if ($reservation->status !== 'checked_out') {
            setFlash('warning', 'Ulasan hanya bisa diberikan setelah menginap selesai.'); redirect('account');
        }

        $reviewModel = $this->model('Review');
        if ($reviewModel->existsForReservation($reservationId)) {
            setFlash('warning', 'Reservasi ini sudah pernah diulas.'); redirect('account');
        }

        $rating  = (int)($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');
        if ($rating < 1 || $rating > 5) {
            setFlash('danger', 'Beri rating 1 sampai 5 bintang.'); redirect('account');
        }

        $reviewModel->create([
            'reservation_id' => $reservationId,
            'user_id'        => currentUser()['id'],
            'room_id'        => $reservation->room_id,
            'rating'         => $rating,
            'comment'        => $comment,
        ]);
        setFlash('success', 'Terima kasih! Ulasan kamu sudah tayang.');
        redirect('account');
    }

    // E-Voucher / bukti reservasi yang bisa dicetak
    public function voucher($reservationId = null)
    {
        $resModel = $this->model('Reservation');
        $reservation = $resModel->find($reservationId);
        if (!$reservation || $reservation->user_id != currentUser()['id']) {
            setFlash('danger', 'Reservasi tidak ditemukan.'); redirect('account');
        }
        $payment = $this->model('Payment')->findByReservation($reservationId);
        $this->view('account/voucher', [
            'title'       => 'E-Voucher #' . $reservation->id,
            'reservation' => $reservation,
            'payment'     => $payment,
        ]);
    }

    // Halaman profil + update foto
    public function profile()
    {
        $userModel = $this->model('User');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name  = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $user  = $userModel->findById(currentUser()['id']);
            $photo = $user->photo;

            $err = null;
            $uploaded = uploadImage('photo', 'profiles', $err);
            if ($uploaded === false) {
                setFlash('danger', $err);
                redirect('account/profile');
            }
            if ($uploaded) {
                deleteUpload('profiles', $user->photo);
                $photo = $uploaded;
            }

            $userModel->updateProfile($user->id, [
                'name' => $name, 'phone' => $phone, 'photo' => $photo,
            ]);

            // Perbarui sesi
            $_SESSION['user']['name']  = $name;
            $_SESSION['user']['photo'] = $photo;

            setFlash('success', 'Profil diperbarui.');
            redirect('account/profile');
        }

        $this->view('account/profile', [
            'title' => 'Profil Saya',
            'user'  => $userModel->findById(currentUser()['id']),
        ]);
    }

    // Halaman pembayaran (upload bukti transfer)
    public function payment($reservationId = null)
    {
        $resModel = $this->model('Reservation');
        $payModel = $this->model('Payment');

        $reservation = $resModel->find($reservationId);
        if (!$reservation || $reservation->user_id != currentUser()['id']) {
            setFlash('danger', 'Reservasi tidak ditemukan.');
            redirect('account');
        }
        $payment = $payModel->findByReservation($reservationId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $err = null;
            $proof = uploadImage('proof', 'payments', $err);
            if (!$proof) {
                setFlash('danger', $err ?: 'Bukti transfer wajib diunggah (jpg/png).');
                redirect('account/payment/' . $reservationId);
            }
            $payModel->uploadProof($payment->id, $proof);
            setFlash('success', 'Bukti pembayaran terkirim. Menunggu verifikasi admin.');
            redirect('account');
        }

        $this->view('account/payment', [
            'title'       => 'Pembayaran',
            'reservation' => $reservation,
            'payment'     => $payment,
        ]);
    }

    // Pelanggan membatalkan reservasi
    public function cancel($reservationId = null)
    {
        $resModel = $this->model('Reservation');
        $reservation = $resModel->find($reservationId);
        if ($reservation && $reservation->user_id == currentUser()['id']
            && in_array($reservation->status, ['pending', 'confirmed'])) {
            $resModel->updateStatus($reservationId, 'cancelled');
            setFlash('warning', 'Reservasi dibatalkan.');
        }
        redirect('account');
    }
}
