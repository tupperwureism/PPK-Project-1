<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the user dashboard with mock Todo List data.
     */
    public function index(): View
    {
        $stats = [
            'total' => 12,
            'completed' => 7,
            'in_progress' => 4,
            'pending' => 1,
        ];

        $todos = [
            [
                'id' => 1,
                'title' => 'Analisis Kebutuhan Modul Auth & RBAC SRS JARA',
                'category' => 'PPK Project',
                'priority' => 'Tinggi',
                'status' => 'Selesai',
                'due_date' => '2026-09-10',
            ],
            [
                'id' => 2,
                'title' => 'Implementasi CRUD Manajemen Pengguna Admin',
                'category' => 'Backend Core',
                'priority' => 'Tinggi',
                'status' => 'Selesai',
                'due_date' => '2026-09-11',
            ],
            [
                'id' => 3,
                'title' => 'Perancangan Skema Database Tugas & Relasi Cascading',
                'category' => 'Database',
                'priority' => 'Sedang',
                'status' => 'Dalam Proses',
                'due_date' => '2026-09-14',
            ],
            [
                'id' => 4,
                'title' => 'Desain Wireframe Modul Kolaborasi Tim',
                'category' => 'UI/UX Design',
                'priority' => 'Sedang',
                'status' => 'Dalam Proses',
                'due_date' => '2026-09-16',
            ],
            [
                'id' => 5,
                'title' => 'Integrasi Notifikasi Realtime & Reminder Deadline',
                'category' => 'Frontend',
                'priority' => 'Rendah',
                'status' => 'Tertunda',
                'due_date' => '2026-09-20',
            ],
        ];

        return view('dashboard', compact('stats', 'todos'));
    }
}
