<?php
class Rooms extends Controller
{
    // Katalog publik + filter
    public function index()
    {
        $roomModel = $this->model('Room');
        $filters = [
            'city'      => $_GET['city']      ?? '',
            'type'      => $_GET['type']      ?? '',
            'guests'    => $_GET['guests']    ?? '',
            'max_price' => $_GET['max_price'] ?? '',
            'keyword'   => $_GET['keyword']   ?? '',
            'sort'      => $_GET['sort']       ?? '',
        ];

        $this->view('rooms/index', [
            'title'   => 'Cari Kamar',
            'rooms'   => $roomModel->search($filters),
            'cities'  => $roomModel->cities(),
            'filters' => $filters,
        ]);
    }

    // Detail kamar + form booking
    public function detail($id = null)
    {
        $roomModel = $this->model('Room');
        $room = $roomModel->find($id);
        if (!$room) redirect('rooms');

        $reviewModel = $this->model('Review');
        $this->view('rooms/detail', [
            'title'      => $room->name,
            'room'       => $room,
            'facilities' => $roomModel->getFacilities($id),
            'reviews'    => $reviewModel->byRoom($id),
            'summary'    => $reviewModel->summary($id),
        ]);
    }

    /**
     * ENDPOINT API (AJAX) -> mengembalikan JSON.
     * Dipakai untuk cek ketersediaan + kalkulasi harga otomatis tanpa reload.
     * URL: /rooms/availability
     * Memenuhi: CPMK 2 (API) & CPMK 5 (Interaktivitas).
     */
    public function availability()
    {
        $roomId   = $_GET['room_id']   ?? null;
        $checkIn  = $_GET['check_in']  ?? null;
        $checkOut = $_GET['check_out'] ?? null;

        if (!$roomId || !$checkIn || !$checkOut) {
            $this->json(['ok' => false, 'message' => 'Parameter tidak lengkap.'], 400);
        }

        // Validasi tanggal
        if ($checkOut <= $checkIn) {
            $this->json(['ok' => false, 'message' => 'Tanggal check-out harus setelah check-in.']);
        }
        if ($checkIn < date('Y-m-d')) {
            $this->json(['ok' => false, 'message' => 'Tanggal check-in tidak boleh di masa lalu.']);
        }

        $roomModel = $this->model('Room');
        $room = $roomModel->find($roomId);
        if (!$room) $this->json(['ok' => false, 'message' => 'Kamar tidak ditemukan.'], 404);

        $resModel  = $this->model('Reservation');
        $available = $resModel->isAvailable($roomId, $checkIn, $checkOut);

        $nights = hitungMalam($checkIn, $checkOut);
        $total  = $nights * (float)$room->price;

        $this->json([
            'ok'             => true,
            'available'      => $available,
            'nights'         => $nights,
            'price_per_night'=> (float)$room->price,
            'total'          => $total,
            'total_format'   => rupiah($total),
            'message'        => $available
                ? "Kamar tersedia untuk $nights malam."
                : 'Maaf, kamar sudah dipesan pada rentang tanggal tersebut.',
        ]);
    }
}
