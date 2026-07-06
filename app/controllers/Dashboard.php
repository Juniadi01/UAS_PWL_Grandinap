<?php
class Dashboard extends Controller
{
    public function __construct() { requireAdmin(); }

    public function index()
    {
        $resModel   = $this->model('Reservation');
        $roomModel  = $this->model('Room');
        $hotelModel = $this->model('Hotel');
        $payModel   = $this->model('Payment');

        $this->view('admin/dashboard', [
            'title'          => 'Dashboard',
            'totalRevenue'   => $resModel->totalRevenue(),
            'totalReservasi' => $resModel->count(),
            'totalKamar'     => $roomModel->count(),
            'totalHotel'     => $hotelModel->count(),
            'menungguBayar'  => $payModel->countWaiting(),
            'pending'        => $resModel->countByStatus('pending'),
            'bestRooms'      => $resModel->bestSellingRooms(5),
            'revenuePerMonth'=> array_reverse($resModel->revenuePerMonth()),
            'statusCounts'   => [
                'confirmed'   => $resModel->countByStatus('confirmed'),
                'checked_out' => $resModel->countByStatus('checked_out'),
                'pending'     => $resModel->countByStatus('pending'),
                'cancelled'   => $resModel->countByStatus('cancelled'),
            ],
        ]);
    }
}
