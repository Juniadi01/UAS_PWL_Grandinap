<?php
class Home extends Controller
{
    public function index()
    {
        $roomModel  = $this->model('Room');
        $hotelModel = $this->model('Hotel');

        $data = [
            'title'    => 'Beranda',
            'rooms'    => array_slice($roomModel->search([]), 0, 6), // kamar unggulan
            'hotels'   => $hotelModel->all(),
            'cities'   => $roomModel->cities(),
        ];
        $this->view('home/index', $data);
    }
}
