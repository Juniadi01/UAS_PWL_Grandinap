<?php
// Controller publik untuk telusur hotel (berbeda dari Hoteladmin yang mengelola data)
class Hotels extends Controller
{
    private $hotel;
    private $room;

    public function __construct()
    {
        $this->hotel = $this->model('Hotel');
        $this->room  = $this->model('Room');
    }

    // Daftar semua hotel (dengan filter & urutan)
    public function index()
    {
        $filters = [
            'city' => $_GET['city'] ?? '',
            'star' => $_GET['star'] ?? '',
            'sort' => $_GET['sort'] ?? '',
        ];
        $this->view('hotels/index', [
            'title'   => 'Daftar Hotel',
            'hotels'  => $this->hotel->allWithStats($filters),
            'cities'  => $this->room->cities(),
            'filters' => $filters,
        ]);
    }

    // Detail satu hotel beserta kamarnya
    public function detail($id = 0)
    {
        $hotel = $this->hotel->find((int)$id);
        if (!$hotel) {
            setFlash('danger', 'Hotel tidak ditemukan.');
            redirect('hotels');
        }
        $this->view('hotels/detail', [
            'title' => $hotel->name,
            'hotel' => $hotel,
            'rooms' => $this->room->byHotel($hotel->id),
        ]);
    }
}
