<?php
class Wishlist extends Controller
{
    // Toggle favorit via AJAX (JSON). Login wajib.
    public function toggle()
    {
        if (!isLoggedIn()) $this->json(['ok' => false, 'auth' => false, 'message' => 'Silakan login dulu.'], 401);

        $roomId = $_POST['room_id'] ?? ($_GET['room_id'] ?? null);
        if (!$roomId) $this->json(['ok' => false, 'message' => 'Kamar tidak valid.'], 400);

        $model = $this->model('WishlistModel');
        $added = $model->toggle(currentUser()['id'], $roomId);
        $this->json([
            'ok'      => true,
            'added'   => $added,
            'count'   => $model->countByUser(currentUser()['id']),
            'message' => $added ? 'Ditambahkan ke favorit.' : 'Dihapus dari favorit.',
        ]);
    }

    // Daftar id favorit (untuk menandai ikon hati saat halaman dimuat)
    public function ids()
    {
        if (!isLoggedIn()) $this->json(['ids' => []]);
        $this->json(['ids' => $this->model('WishlistModel')->idsByUser(currentUser()['id'])]);
    }

    // Halaman daftar favorit
    public function index()
    {
        requireLogin();
        $this->view('account/wishlist', [
            'title' => 'Favorit Saya',
            'rooms' => $this->model('WishlistModel')->byUser(currentUser()['id']),
        ]);
    }
}
