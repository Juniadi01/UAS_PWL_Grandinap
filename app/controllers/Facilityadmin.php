<?php
class Facilityadmin extends Controller
{
    private $model;
    public function __construct() { requireAdmin(); $this->model = $this->model('Facility'); }

    public function index()
    {
        $this->view('admin/facilities_index', [
            'title'      => 'Manajemen Fasilitas',
            'facilities' => $this->model->all(),
        ]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') return $this->store();
        $this->view('admin/facilities_form', ['title' => 'Tambah Fasilitas', 'facility' => null]);
    }

    public function edit($id)
    {
        $facility = $this->model->find($id);
        if (!$facility) redirect('facilityadmin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') return $this->store($id);
        $this->view('admin/facilities_form', ['title' => 'Edit Fasilitas', 'facility' => $facility]);
    }

    private function store($id = null)
    {
        $name = trim($_POST['name'] ?? '');
        $back = $id ? 'facilityadmin/edit/' . $id : 'facilityadmin/create';
        if ($name === '') { setFlash('danger', 'Nama fasilitas wajib diisi.'); redirect($back); }

        $data = [
            'name' => $name,
            'icon' => trim($_POST['icon'] ?? '') ?: 'bi-check-circle',
        ];
        if ($id) { $this->model->update($id, $data); setFlash('success', 'Fasilitas diperbarui.'); }
        else     { $this->model->create($data);     setFlash('success', 'Fasilitas ditambahkan.'); }
        redirect('facilityadmin');
    }

    public function delete($id)
    {
        $this->model->delete($id);
        setFlash('warning', 'Fasilitas dihapus.');
        redirect('facilityadmin');
    }
}
