<?php
class Roomadmin extends Controller
{
    private $model;
    public function __construct() { requireAdmin(); $this->model = $this->model('Room'); }

    public function index()
    {
        $this->view('admin/rooms_index', [
            'title' => 'Manajemen Kamar',
            'rooms' => $this->model->all(),
        ]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') return $this->store();
        $this->view('admin/rooms_form', [
            'title'         => 'Tambah Kamar',
            'room'          => null,
            'hotels'        => $this->model('Hotel')->all(),
            'facilities'    => $this->model('Facility')->all(),
            'roomFacilities'=> [],
        ]);
    }

    public function edit($id)
    {
        $room = $this->model->find($id);
        if (!$room) redirect('roomadmin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') return $this->store($id);
        $this->view('admin/rooms_form', [
            'title'         => 'Edit Kamar',
            'room'          => $room,
            'hotels'        => $this->model('Hotel')->all(),
            'facilities'    => $this->model('Facility')->all(),
            'roomFacilities'=> $this->model->getFacilityIds($id),
        ]);
    }

    private function store($id = null)
    {
        $name     = trim($_POST['name'] ?? '');
        $hotel_id = (int)($_POST['hotel_id'] ?? 0);
        $type     = trim($_POST['type'] ?? '');
        $price    = (float)($_POST['price'] ?? 0);
        $capacity = (int)($_POST['capacity'] ?? 0);
        $status   = in_array(($_POST['status'] ?? ''), ['available','maintenance']) ? $_POST['status'] : 'available';
        $back     = $id ? 'roomadmin/edit/' . $id : 'roomadmin/create';

        $errors = [];
        if ($name === '')      $errors[] = 'Nama kamar wajib diisi.';
        if ($hotel_id <= 0)    $errors[] = 'Hotel wajib dipilih.';
        if ($type === '')      $errors[] = 'Tipe kamar wajib dipilih.';
        if ($price <= 0)       $errors[] = 'Harga harus lebih dari 0.';
        if ($capacity <= 0)    $errors[] = 'Kapasitas minimal 1 orang.';
        if ($errors) { setFlash('danger', implode(' ', $errors)); redirect($back); }

        $existing = $id ? $this->model->find($id) : null;
        $photo = $existing->photo ?? null;

        $err = null;
        $uploaded = uploadImage('photo', 'rooms', $err);
        if ($uploaded === false) { setFlash('danger', $err); redirect($back); }
        if ($uploaded) { deleteUpload('rooms', $photo); $photo = $uploaded; }

        $data = [
            'hotel_id'    => $hotel_id,
            'name'        => $name,
            'type'        => $type,
            'price'       => $price,
            'capacity'    => $capacity,
            'description' => trim($_POST['description'] ?? ''),
            'photo'       => $photo,
            'status'      => $status,
        ];

        if ($id) {
            $this->model->update($id, $data);
            $this->model->syncFacilities($id, $_POST['facilities'] ?? []);
            setFlash('success', 'Kamar diperbarui.');
        } else {
            $newId = $this->model->create($data);
            $this->model->syncFacilities($newId, $_POST['facilities'] ?? []);
            setFlash('success', 'Kamar ditambahkan.');
        }
        redirect('roomadmin');
    }

    public function delete($id)
    {
        $room = $this->model->find($id);
        if ($room) { deleteUpload('rooms', $room->photo); $this->model->delete($id); }
        setFlash('warning', 'Kamar dihapus.');
        redirect('roomadmin');
    }
}
