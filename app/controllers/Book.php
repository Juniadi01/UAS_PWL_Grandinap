<?php
class Book extends Controller
{
    public function __construct()
    {
        requireLogin(); // hanya pelanggan login yang bisa booking
    }

    // Proses pembuatan reservasi (dari form di halaman detail kamar)
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('rooms');

        $roomId   = $_POST['room_id']   ?? null;
        $checkIn  = $_POST['check_in']  ?? null;
        $checkOut = $_POST['check_out'] ?? null;
        $guests   = (int)($_POST['guests'] ?? 1);

        $roomModel = $this->model('Room');
        $room = $roomModel->find($roomId);
        if (!$room) { setFlash('danger', 'Kamar tidak ditemukan.'); redirect('rooms'); }

        // ---- Validasi server-side (jangan hanya andalkan JS) ----
        if (!$checkIn || !$checkOut || $checkOut <= $checkIn) {
            setFlash('danger', 'Tanggal menginap tidak valid.');
            redirect('rooms/detail/' . $roomId);
        }
        if ($checkIn < date('Y-m-d')) {
            setFlash('danger', 'Tanggal check-in tidak boleh di masa lalu.');
            redirect('rooms/detail/' . $roomId);
        }
        if ($guests > $room->capacity) {
            setFlash('danger', 'Jumlah tamu melebihi kapasitas kamar.');
            redirect('rooms/detail/' . $roomId);
        }

        // ---- Cek ketersediaan ulang di server (anti double-booking) ----
        $resModel = $this->model('Reservation');
        if (!$resModel->isAvailable($roomId, $checkIn, $checkOut)) {
            setFlash('danger', 'Kamar sudah dipesan pada tanggal tersebut. Pilih tanggal lain.');
            redirect('rooms/detail/' . $roomId);
        }

        $nights = hitungMalam($checkIn, $checkOut);
        $total  = $nights * (float)$room->price;

        $reservationId = $resModel->create([
            'user_id'     => currentUser()['id'],
            'room_id'     => $roomId,
            'check_in'    => $checkIn,
            'check_out'   => $checkOut,
            'guests'      => $guests,
            'nights'      => $nights,
            'total_price' => $total,
        ]);

        // Buat record pembayaran (status unpaid)
        $payModel = $this->model('Payment');
        $payModel->create([
            'reservation_id' => $reservationId,
            'amount'         => $total,
            'method'         => 'Transfer Bank',
        ]);

        setFlash('success', 'Reservasi dibuat! Silakan lakukan pembayaran.');
        redirect('account/payment/' . $reservationId);
    }
}
