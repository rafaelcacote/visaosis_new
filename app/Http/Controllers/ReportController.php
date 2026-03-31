<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function attendance()
    {
        // Get today's statistics
        $stats = [
            'total' => 45,
            'cancelled' => 3,
            'returns' => 8,
            'referrals' => 5,
            'avg_wait_time' => '15 min',
            'avg_service_time' => '25 min',
            'priority_patients' => 6,
            'first_time' => 12
        ];

        // Get professional statistics
        $professionalStats = [
            [
                'name' => 'Dr. João Silva',
                'specialty' => 'Optometrista',
                'attended' => 15,
                'returns' => 3,
                'referrals' => 2,
                'cancelled' => 1,
                'total' => 20
            ],
            [
                'name' => 'Dra. Maria Santos',
                'specialty' => 'Oftalmologista',
                'attended' => 18,
                'returns' => 4,
                'referrals' => 2,
                'cancelled' => 0,
                'total' => 24
            ]
        ];

        return view('reports.attendance', compact('stats', 'professionalStats'));
    }

    public function exportAttendance()
    {
        // TODO: Implement Excel export
        return response()->download(
            storage_path('app/reports/attendance_' . date('Y-m-d') . '.xlsx')
        );
    }
}
