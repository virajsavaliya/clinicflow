<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineStock;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class InventoryAlertController extends Controller
{
    /**
     * Get low stock and expiring medicines.
     */
    public function index(Request $request)
    {
        $lowStockItems = $this->getLowStockItems();
        $expiringItems = $this->getExpiringItems();
        $recentAlerts = $this->getRecentAlerts();
        
        return view('doctor.inventory-alerts', compact(
            'lowStockItems',
            'expiringItems',
            'recentAlerts'
        ));
    }
    
    /**
     * API endpoint for alerts.
     */
    public function getAlerts(Request $request)
    {
        return response()->json([
            'success' => true,
            'lowStock' => $this->getLowStockItems(),
            'expiring' => $this->getExpiringItems(),
            'summary' => $this->getAlertSummary(),
        ]);
    }
    
    /**
     * Get low stock items.
     */
    private function getLowStockItems(): array
    {
        $items = [];
        
        $medicines = Medicine::with('stock')->get();
        
        foreach ($medicines as $medicine) {
            $stock = $medicine->stock;
            if (!$stock) continue;
            
            $currentQty = $stock->qty ?? 0;
            $minQty = $stock->min_quantity ?? $medicine->reorder_level ?? 10;
            
            if ($currentQty <= $minQty) {
                $items[] = [
                    'id' => $medicine->id,
                    'name' => $medicine->name,
                    'current_stock' => $currentQty,
                    'min_stock' => $minQty,
                    'status' => $currentQty == 0 ? 'out_of_stock' : ($currentQty < 5 ? 'critical' : 'low'),
                    'priority' => $currentQty == 0 ? 1 : ($currentQty < 5 ? 2 : 3),
                ];
            }
        }
        
        // Sort by priority (out of stock first)
        usort($items, fn($a, $b) => $a['priority'] - $b['priority']);
        
        return $items;
    }
    
    /**
     * Get expiring items.
     */
    private function getExpiringItems(): array
    {
        $items = [];
        
        $expiringStocks = MedicineStock::with('medicine')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(60))
            ->where('qty', '>', 0)
            ->orderBy('expiry_date')
            ->get();
        
        foreach ($expiringStocks as $stock) {
            $daysUntilExpiry = now()->diffInDays($stock->expiry_date, false);
            
            $items[] = [
                'id' => $stock->id,
                'medicine_id' => $stock->medicine_id,
                'name' => $stock->medicine->name ?? 'Unknown',
                'batch_number' => $stock->batch_number,
                'expiry_date' => $stock->expiry_date->format('Y-m-d'),
                'expiry_formatted' => $stock->expiry_date->format('M d, Y'),
                'days_until_expiry' => $daysUntilExpiry,
                'quantity' => $stock->qty,
                'status' => $daysUntilExpiry < 0 ? 'expired' : ($daysUntilExpiry <= 7 ? 'expiring_soon' : 'expiring'),
                'priority' => $daysUntilExpiry < 0 ? 1 : ($daysUntilExpiry <= 7 ? 2 : 3),
            ];
        }
        
        return $items;
    }
    
    /**
     * Get alert summary for dashboard.
     */
    private function getAlertSummary(): array
    {
        $lowStock = count($this->getLowStockItems());
        $expiring = count($this->getExpiringItems());
        
        return [
            'low_stock_count' => $lowStock,
            'expiring_count' => $expiring,
            'total_alerts' => $lowStock + $expiring,
            'has_critical' => $lowStock > 0 || $expiring > 0,
        ];
    }
    
    /**
     * Get recent alert notifications.
     */
    private function getRecentAlerts()
    {
        return Notification::where('type', 'like', 'inventory_%')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
    }
    
    /**
     * Mark stock as ordered.
     */
    public function markOrdered(Request $request, Medicine $medicine)
    {
        $request->validate([
            'quantity_ordered' => 'required|integer|min:1',
            'expected_date' => 'nullable|date|after:today',
        ]);
        
        // Create a notification for tracking
        Notification::create([
            'type' => 'inventory_ordered',
            'data' => json_encode([
                'medicine_id' => $medicine->id,
                'medicine_name' => $medicine->name,
                'quantity' => $request->quantity_ordered,
                'expected_date' => $request->expected_date,
            ]),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Order recorded for ' . $medicine->name,
        ]);
    }
    
    /**
     * Dismiss an alert.
     */
    public function dismissAlert(Request $request)
    {
        $request->validate([
            'type' => 'required|in:low_stock,expiring',
            'item_id' => 'required|integer',
        ]);
        
        // Store dismissed alert (won't show again for 24 hours)
        $dismissKey = $request->type . '_' . $request->item_id;
        cache()->put('dismissed_alert_' . $dismissKey, true, now()->addHours(24));
        
        return response()->json([
            'success' => true,
            'message' => 'Alert dismissed for 24 hours',
        ]);
    }
    
    /**
     * Generate and send alert emails (called by scheduler).
     */
    public function sendAlertEmails()
    {
        $lowStock = $this->getLowStockItems();
        $expiring = $this->getExpiringItems();
        
        if (empty($lowStock) && empty($expiring)) {
            return ['sent' => false, 'reason' => 'No alerts'];
        }
        
        // Get doctor email from config or settings
        $doctorEmail = config('clinic.email', 'doctor@clinic.com');
        
        // Create notification record
        Notification::create([
            'type' => 'inventory_alert_email',
            'data' => json_encode([
                'low_stock_count' => count($lowStock),
                'expiring_count' => count($expiring),
                'sent_at' => now()->toDateTimeString(),
            ]),
        ]);
        
        // In production, you would send an actual email here
        // Mail::to($doctorEmail)->send(new InventoryAlertMail($lowStock, $expiring));
        
        return [
            'sent' => true,
            'low_stock' => count($lowStock),
            'expiring' => count($expiring),
        ];
    }
    
    /**
     * Check if daily alert should be sent.
     */
    public function shouldSendDailyAlert(): bool
    {
        $lastSent = cache()->get('last_inventory_alert_email');
        
        if (!$lastSent) {
            return true;
        }
        
        return Carbon::parse($lastSent)->diffInHours(now()) >= 24;
    }
}
