<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Bill;
use App\Models\Medicine;
use App\Models\MedicineStock;
use App\Models\Patient;
use App\Models\PrescriptionItem;
use App\Models\StockTransaction;
use App\Models\Visit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Display analytics dashboard.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $doctorId = $user->isDoctor() ? $user->id : $user->created_by_doctor_id;
        
        $periodInput = $request->get('period', 'weekly'); // daily, weekly, monthly
        $periodMap = ['daily' => 'day', 'weekly' => 'week', 'monthly' => 'month'];
        $internalPeriod = $periodMap[$periodInput] ?? 'week';
        
        $startDate = $this->getStartDate($internalPeriod);
        $endDate = now();
        
        // Revenue stats
        $revenueStats = $this->getRevenueStats($doctorId, $startDate, $endDate);
        
        // Patient stats  
        $patientStats = $this->getPatientStats($doctorId, $startDate, $endDate);
        
        // Medicine stats
        $medicineStats = $this->getMedicineStats($startDate, $endDate);
        
        // Today's summary
        $todaySummary = $this->getTodaySummary($doctorId);
        
        // Chart data
        $revenueChart = $this->getRevenueChartData($doctorId, $internalPeriod);
        $visitsChart = $this->getVisitsChartData($doctorId, $internalPeriod);
        $topMedicinesData = $this->getTopMedicines($startDate, $endDate);
        $topServices = $this->getTopServices($doctorId, $startDate, $endDate);
        
        // Get inventory alerts
        $inventoryAlerts = $this->getInventoryAlerts();
        
        // Get recent visits
        $recentVisits = Visit::with(['patient', 'bill'])
            ->where('doctor_id', $doctorId)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Prepare data for view
        $period = $periodInput;
        
        $stats = [
            'totalRevenue' => $revenueStats['total'],
            'revenueGrowth' => $revenueStats['growth'],
            'totalPatients' => $patientStats['total_visits'],
            'patientGrowth' => 0, // Calculate if needed
            'totalPrescriptions' => $patientStats['completed'],
            'avgRevenue' => $revenueStats['average'],
        ];
        
        // Format top medicines for view
        $topMedicines = collect($topMedicinesData)->take(10)->toArray();
        
        // Chart data formatted for view
        $chartData = [
            'revenue' => collect($revenueChart['labels'])->map(function($label, $i) use ($revenueChart) {
                return ['label' => $label, 'value' => $revenueChart['data'][$i] ?? 0];
            })->toArray(),
            'demographics' => $this->getPatientDemographics($doctorId),
        ];
        
        return view('doctor.reports', compact(
            'period',
            'stats',
            'topMedicines',
            'inventoryAlerts',
            'recentVisits',
            'chartData'
        ));
    }
    
    /**
     * Get inventory alerts for low stock and expiring items.
     */
    private function getInventoryAlerts(): array
    {
        $alerts = [];
        
        // Low stock items
        $lowStock = Medicine::whereHas('stock', function($q) {
            $q->where('qty', '<', 10);
        })->with('stock')->limit(10)->get();
        
        foreach ($lowStock as $med) {
            $alerts[] = [
                'name' => $med->name,
                'stock' => $med->stock?->qty ?? 0,
                'status' => ($med->stock?->qty ?? 0) < 5 ? 'critical' : 'low',
            ];
        }
        
        // Expiring soon
        $expiring = MedicineStock::with('medicine')
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [today(), today()->addDays(30)])
            ->limit(5)
            ->get();
        
        foreach ($expiring as $stock) {
            $alerts[] = [
                'name' => $stock->medicine->name ?? 'Unknown',
                'stock' => $stock->qty,
                'status' => 'expiring',
            ];
        }
        
        return $alerts;
    }
    
    /**
     * Get patient demographics (gender distribution).
     */
    private function getPatientDemographics($doctorId): array
    {
        $visits = Visit::where('doctor_id', $doctorId)
            ->with('patient')
            ->get();
        
        $genderCounts = ['male' => 0, 'female' => 0, 'other' => 0];
        
        foreach ($visits as $visit) {
            $gender = strtolower($visit->patient->gender ?? 'other');
            if (isset($genderCounts[$gender])) {
                $genderCounts[$gender]++;
            } else {
                $genderCounts['other']++;
            }
        }
        
        return $genderCounts;
    }

    /**
     * Export report to Excel/CSV.
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'revenue');
        $period = $request->get('period', 'month');
        $format = $request->get('format', 'csv');
        
        $user = auth()->user();
        $doctorId = $user->isDoctor() ? $user->id : $user->created_by_doctor_id;
        $startDate = $this->getStartDate($period);
        $endDate = now();
        
        $data = match($type) {
            'revenue' => $this->getRevenueExportData($doctorId, $startDate, $endDate),
            'patients' => $this->getPatientExportData($doctorId, $startDate, $endDate),
            'medicines' => $this->getMedicineExportData($startDate, $endDate),
            'inventory' => $this->getInventoryExportData(),
            default => [],
        };
        
        if ($format === 'csv') {
            return $this->exportToCsv($data, $type);
        }
        
        return response()->json($data);
    }

    /**
     * Get start date based on period.
     */
    private function getStartDate($period): Carbon
    {
        return match($period) {
            'day' => today(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->subDays(7),
        };
    }

    /**
     * Get revenue statistics.
     */
    private function getRevenueStats($doctorId, $startDate, $endDate): array
    {
        $bills = Bill::whereHas('visit', function ($q) use ($doctorId) {
            $q->where('doctor_id', $doctorId);
        })->whereBetween('created_at', [$startDate, $endDate]);
        
        $totalRevenue = (clone $bills)->sum('total_amount');
        $paidRevenue = (clone $bills)->where('status', 'paid')->sum('total_amount');
        $pendingRevenue = (clone $bills)->where('status', 'pending')->sum('total_amount');
        $billCount = (clone $bills)->count();
        
        // Previous period comparison
        $periodDays = $startDate->diffInDays($endDate);
        $prevStartDate = (clone $startDate)->subDays($periodDays);
        $prevEndDate = $startDate;
        
        $prevRevenue = Bill::whereHas('visit', function ($q) use ($doctorId) {
            $q->where('doctor_id', $doctorId);
        })->whereBetween('created_at', [$prevStartDate, $prevEndDate])->sum('total_amount');
        
        $growth = $prevRevenue > 0 ? (($totalRevenue - $prevRevenue) / $prevRevenue) * 100 : 0;
        
        return [
            'total' => $totalRevenue,
            'paid' => $paidRevenue,
            'pending' => $pendingRevenue,
            'count' => $billCount,
            'average' => $billCount > 0 ? $totalRevenue / $billCount : 0,
            'growth' => round($growth, 1),
        ];
    }

    /**
     * Get patient statistics.
     */
    private function getPatientStats($doctorId, $startDate, $endDate): array
    {
        $visits = Visit::where('doctor_id', $doctorId)
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        $totalVisits = (clone $visits)->count();
        $completedVisits = (clone $visits)->where('status', 'completed')->count();
        $newPatients = (clone $visits)->whereHas('patient', function ($q) use ($startDate) {
            $q->whereDate('created_at', '>=', $startDate);
        })->count();
        
        // Unique patients
        $uniquePatients = (clone $visits)->distinct('patient_id')->count('patient_id');
        
        // Average patients per day
        $days = max(1, $startDate->diffInDays($endDate));
        $avgPerDay = round($totalVisits / $days, 1);
        
        return [
            'total_visits' => $totalVisits,
            'completed' => $completedVisits,
            'new_patients' => $newPatients,
            'unique_patients' => $uniquePatients,
            'avg_per_day' => $avgPerDay,
            'completion_rate' => $totalVisits > 0 ? round(($completedVisits / $totalVisits) * 100, 1) : 0,
        ];
    }

    /**
     * Get medicine statistics.
     */
    private function getMedicineStats($startDate, $endDate): array
    {
        $prescriptionItems = PrescriptionItem::whereBetween('created_at', [$startDate, $endDate]);
        
        $totalDispensed = (clone $prescriptionItems)->sum('qty');
        $totalValue = (clone $prescriptionItems)->sum(DB::raw('qty * unit_price'));
        $uniqueMedicines = (clone $prescriptionItems)->distinct('medicine_id')->count('medicine_id');
        
        // Low stock count
        $lowStockCount = Medicine::whereHas('stock', function ($q) {
            $q->where('qty', '<', 10);
        })->count();
        
        // Expiring soon (within 30 days)
        $expiringSoon = 0;
        try {
            $expiringSoon = MedicineStock::whereNotNull('expiry_date')
                ->whereBetween('expiry_date', [today(), today()->addDays(30)])
                ->count();
        } catch (\Exception $e) {
            // Column may not exist
        }
        
        return [
            'total_dispensed' => $totalDispensed ?? 0,
            'total_value' => $totalValue ?? 0,
            'unique_medicines' => $uniqueMedicines ?? 0,
            'low_stock' => $lowStockCount,
            'expiring_soon' => $expiringSoon,
        ];
    }

    /**
     * Get today's summary.
     */
    private function getTodaySummary($doctorId): array
    {
        $todayVisits = Visit::where('doctor_id', $doctorId)->whereDate('created_at', today());
        $todayBills = Bill::whereHas('visit', fn($q) => $q->where('doctor_id', $doctorId))
            ->whereDate('created_at', today());
        
        return [
            'visits' => (clone $todayVisits)->count(),
            'completed' => (clone $todayVisits)->where('status', 'completed')->count(),
            'waiting' => (clone $todayVisits)->where('status', 'waiting')->count(),
            'in_consult' => (clone $todayVisits)->where('status', 'in_consult')->count(),
            'revenue' => (clone $todayBills)->sum('total_amount'),
            'bills' => (clone $todayBills)->count(),
        ];
    }

    /**
     * Get revenue chart data.
     */
    private function getRevenueChartData($doctorId, $period): array
    {
        $labels = [];
        $data = [];
        
        if ($period === 'week') {
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $labels[] = $date->format('D');
                $data[] = Bill::whereHas('visit', fn($q) => $q->where('doctor_id', $doctorId))
                    ->whereDate('created_at', $date)
                    ->sum('total_amount');
            }
        } elseif ($period === 'month') {
            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $labels[] = $date->format('M d');
                $data[] = Bill::whereHas('visit', fn($q) => $q->where('doctor_id', $doctorId))
                    ->whereDate('created_at', $date)
                    ->sum('total_amount');
            }
        } elseif ($period === 'year') {
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $labels[] = $date->format('M');
                $data[] = Bill::whereHas('visit', fn($q) => $q->where('doctor_id', $doctorId))
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('total_amount');
            }
        }
        
        return compact('labels', 'data');
    }

    /**
     * Get visits chart data.
     */
    private function getVisitsChartData($doctorId, $period): array
    {
        $labels = [];
        $data = [];
        
        if ($period === 'week') {
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $labels[] = $date->format('D');
                $data[] = Visit::where('doctor_id', $doctorId)
                    ->whereDate('created_at', $date)
                    ->count();
            }
        } elseif ($period === 'month') {
            for ($i = 3; $i >= 0; $i--) {
                $startOfWeek = now()->subWeeks($i)->startOfWeek();
                $endOfWeek = now()->subWeeks($i)->endOfWeek();
                $labels[] = 'Week ' . (4 - $i);
                $data[] = Visit::where('doctor_id', $doctorId)
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->count();
            }
        } elseif ($period === 'year') {
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $labels[] = $date->format('M');
                $data[] = Visit::where('doctor_id', $doctorId)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
            }
        }
        
        return compact('labels', 'data');
    }

    /**
     * Get top medicines.
     */
    private function getTopMedicines($startDate, $endDate): array
    {
        try {
            return PrescriptionItem::select('medicine_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(qty * unit_price) as total_value'), DB::raw('COUNT(*) as times'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('medicine_id')
                ->orderByDesc('total_qty')
                ->limit(10)
                ->with('medicine:id,name')
                ->get()
                ->map(fn($item) => [
                    'name' => $item->medicine?->name ?? 'Unknown',
                    'times' => $item->times,
                    'quantity' => $item->total_qty,
                    'value' => $item->total_value,
                    'stock' => $item->medicine?->stock?->qty ?? 0,
                ])
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get top services (doctor charges).
     */
    private function getTopServices($doctorId, $startDate, $endDate): array
    {
        return \App\Models\BillItem::select('title', DB::raw('COUNT(*) as count'), DB::raw('SUM(subtotal) as total_value'))
            ->whereHas('bill.visit', fn($q) => $q->where('doctor_id', $doctorId))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('title')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(fn($item) => [
                'name' => $item->title,
                'count' => $item->count,
                'value' => $item->total_value,
            ])
            ->toArray();
    }

    /**
     * Get revenue export data.
     */
    private function getRevenueExportData($doctorId, $startDate, $endDate): array
    {
        return Bill::with('visit.patient', 'visit.doctor')
            ->whereHas('visit', fn($q) => $q->where('doctor_id', $doctorId))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($bill) => [
                'Date' => $bill->created_at->format('Y-m-d'),
                'Bill No' => $bill->id,
                'Patient' => $bill->visit?->patient?->name ?? 'N/A',
                'Amount' => $bill->total_amount,
                'Status' => ucfirst($bill->status),
            ])
            ->toArray();
    }

    /**
     * Get patient export data.
     */
    private function getPatientExportData($doctorId, $startDate, $endDate): array
    {
        return Visit::with('patient')
            ->where('doctor_id', $doctorId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($visit) => [
                'Date' => $visit->created_at->format('Y-m-d'),
                'Queue No' => $visit->queue_number,
                'Patient' => $visit->patient?->name ?? 'N/A',
                'Phone' => $visit->patient?->phone ?? 'N/A',
                'Age' => $visit->patient?->age ?? 'N/A',
                'Gender' => ucfirst($visit->patient?->gender ?? 'N/A'),
                'Status' => ucfirst($visit->status),
            ])
            ->toArray();
    }

    /**
     * Get medicine export data.
     */
    private function getMedicineExportData($startDate, $endDate): array
    {
        return PrescriptionItem::with('medicine', 'prescription.visit.patient')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($item) => [
                'Date' => $item->created_at->format('Y-m-d'),
                'Medicine' => $item->medicine?->name ?? 'N/A',
                'Patient' => $item->prescription?->visit?->patient?->name ?? 'N/A',
                'Quantity' => $item->qty,
                'Unit Price' => $item->unit_price,
                'Total' => $item->qty * $item->unit_price,
            ])
            ->toArray();
    }

    /**
     * Get inventory export data.
     */
    private function getInventoryExportData(): array
    {
        return Medicine::with('stock')
            ->orderBy('name')
            ->get()
            ->map(fn($medicine) => [
                'Medicine' => $medicine->name,
                'Current Stock' => $medicine->stock?->qty ?? 0,
                'Reorder Level' => $medicine->reorder_level,
                'Unit Price' => $medicine->unit_price,
                'Stock Value' => ($medicine->stock?->qty ?? 0) * $medicine->unit_price,
                'Status' => ($medicine->stock?->qty ?? 0) <= $medicine->reorder_level ? 'Low Stock' : 'OK',
            ])
            ->toArray();
    }

    /**
     * Export to CSV.
     */
    private function exportToCsv($data, $type)
    {
        $filename = $type . '_report_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            
            // Write header row
            if (!empty($data)) {
                fputcsv($file, array_keys($data[0]));
            }
            
            // Write data rows
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get low stock alerts.
     */
    public function lowStockAlerts()
    {
        $lowStockMedicines = Medicine::with('stock')
            ->whereHas('stock', function ($q) {
                $q->whereColumn('qty', '<=', 'medicines.reorder_level');
            })
            ->orderBy('name')
            ->get();
        
        $expiringMedicines = MedicineStock::with('medicine')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', today()->addDays(30))
            ->orderBy('expiry_date')
            ->get();
        
        return view('doctor.inventory-alerts', compact('lowStockMedicines', 'expiringMedicines'));
    }
}
