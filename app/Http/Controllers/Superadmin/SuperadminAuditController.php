<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

/**
 * Audit Aktivitas Sistem.
 *
 * Superadmin hanya dapat melihat metadata aktivitas administratif
 * (login, perubahan status laporan, unggah dokumen, perubahan data).
 * Isi laporan dan identitas pelapor/korban tidak pernah ditampilkan di sini.
 */
class SuperadminAuditController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::query();

        // Pencarian: nama administrator, deskripsi aktivitas, atau referensi
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($sub) use ($keyword) {
                $sub->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhere('reference', 'like', "%{$keyword}%");
            });
        }

        // Filter instansi
        if ($request->filled('instansi')) {
            $query->where('instansi', $request->instansi);
        }

        // Filter jenis aktivitas
        if ($request->filled('jenis')) {
            $query->where('activity_type', $request->jenis);
        }

        // Filter tanggal aktivitas
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        $activities = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $instansiList = [
            'UPTD PPA'    => 'UPTD PPA',
            'Dinas Sosial' => 'Dinas Sosial',
            'Superadmin'  => 'Superadmin',
        ];

        $jenisList = ActivityLog::TYPES;

        return view('backend.superadmin.audit.index', compact('activities', 'instansiList', 'jenisList'));
    }

    public function show($id)
    {
        // Hanya metadata aktivitas — tanpa isi laporan / identitas pelapor.
        $activity = ActivityLog::findOrFail($id);

        return view('backend.superadmin.audit.show', compact('activity'));
    }
}
