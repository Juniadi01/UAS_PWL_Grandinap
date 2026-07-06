<?php
class Reportadmin extends Controller
{
    public function __construct() { requireAdmin(); }

    public function index()
    {
        $resModel = $this->model('Reservation');
        $this->view('admin/reports', [
            'title'           => 'Laporan',
            'totalRevenue'    => $resModel->totalRevenue(),
            'totalReservasi'  => $resModel->count(),
            'revenuePerMonth' => $resModel->revenuePerMonth(),
            'bestRooms'       => $resModel->bestSellingRooms(10),
            'allReservations' => $resModel->all(),
        ]);
    }

    // Ekspor seluruh reservasi ke CSV (unduhan). Hanya admin (requireAdmin di constructor).
    public function export()
    {
        $rows = $this->model('Reservation')->all();
        $filename = 'laporan-reservasi-' . date('Ymd-His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');

        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8 dengan benar
        fputcsv($out, ['ID', 'Pelanggan', 'Hotel', 'Kamar', 'Check-in', 'Check-out', 'Total (Rp)', 'Status', 'Pembayaran']);
        foreach ($rows as $r) {
            fputcsv($out, [
                $r->id,
                $r->customer_name,
                $r->hotel_name,
                $r->room_name,
                $r->check_in,
                $r->check_out,
                $r->total_price,
                $r->status,
                $r->payment_status ?? '-',
            ]);
        }
        fclose($out);
        exit;
    }
}
