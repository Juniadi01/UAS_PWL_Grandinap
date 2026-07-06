<?php
class Hoteladmin extends Controller
{
    private $model;
    public function __construct() { requireAdmin(); $this->model = $this->model('Hotel'); }

    public function index()
    {
        $this->view('admin/hotels_index', [
            'title'  => 'Manajemen Hotel',
            'hotels' => $this->model->all(),
        ]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') return $this->store();
        $this->view('admin/hotels_form', ['title' => 'Tambah Hotel', 'hotel' => null]);
    }

    public function edit($id)
    {
        $hotel = $this->model->find($id);
        if (!$hotel) redirect('hoteladmin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') return $this->store($id);
        $this->view('admin/hotels_form', ['title' => 'Edit Hotel', 'hotel' => $hotel]);
    }

    private function store($id = null)
    {
        $name = trim($_POST['name'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $star = (int)($_POST['star'] ?? 5);
        $back = $id ? 'hoteladmin/edit/' . $id : 'hoteladmin/create';

        $errors = [];
        if ($name === '') $errors[] = 'Nama hotel wajib diisi.';
        if ($city === '') $errors[] = 'Kota wajib diisi.';
        if ($star < 1 || $star > 5) $errors[] = 'Bintang harus antara 1-5.';
        if ($errors) { setFlash('danger', implode(' ', $errors)); redirect($back); }

        $existing = $id ? $this->model->find($id) : null;
        $photo = $existing->photo ?? null;

        $err = null;
        $uploaded = uploadImage('photo', 'hotels', $err);
        if ($uploaded === false) { setFlash('danger', $err); redirect($back); }
        if ($uploaded) { deleteUpload('hotels', $photo); $photo = $uploaded; }

        $data = [
            'name'        => $name,
            'city'        => $city,
            'address'     => trim($_POST['address'] ?? ''),
            'star'        => $star,
            'description' => trim($_POST['description'] ?? ''),
            'photo'       => $photo,
        ];

        if ($id) { $this->model->update($id, $data); setFlash('success', 'Hotel diperbarui.'); }
        else     { $this->model->create($data);     setFlash('success', 'Hotel ditambahkan.'); }
        redirect('hoteladmin');
    }

    public function delete($id)
    {
        $hotel = $this->model->find($id);
        if ($hotel) { deleteUpload('hotels', $hotel->photo); $this->model->delete($id); }
        setFlash('warning', 'Hotel dihapus.');
        redirect('hoteladmin');
    }
}
