<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use App\Models\Santri;
use App\Models\Ustad;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $ustad = Ustad::count();
        $santri = Santri::count();
        $mapel = MataPelajaran::count();
        return view('admin.dashboard.dashboard', [
            'ustad' => $ustad,
            'santri' => $santri,
            'mapel' => $mapel
        ]);
    }
}
