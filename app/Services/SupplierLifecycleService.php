<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupplierLifecycleService
{
    /**
     * Approve a supplier and activate their account
     */
    public function approveSupplier(User $supplier, ?User $approvedBy = null): bool
    {
        if (!$supplier->is_supplier) {
            throw new \Exception('User is not a supplier');
        }

        DB::beginTransaction();
        try {
            $supplier->update([
                'supplier_status' => 'active',
                'supplier_approved_at' => now(),
                'is_active' => true,
            ]);

            // Log the approval
            if ($approvedBy) {
                Log::info('Supplier approved', [
                    'supplier_id' => $supplier->id,
                    'supplier_name' => $supplier->name,
                    'approved_by' => $approvedBy->id,
                    'approved_by_name' => $approvedBy->name,
                ]);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve supplier', [
                'supplier_id' => $supplier->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Reject a supplier application
     */
    public function rejectSupplier(User $supplier, string $reason, ?User $rejectedBy = null): bool
    {
        if (!$supplier->is_supplier) {
            throw new \Exception('User is not a supplier');
        }

        DB::beginTransaction();
        try {
            $supplier->update([
                'supplier_status' => 'inactive',
                'notes' => ($supplier->notes ?? '') . "\n\nRejection Reason: " . $reason . " (Date: " . now()->format('Y-m-d H:i:s') . ")",
            ]);

            if ($rejectedBy) {
                Log::info('Supplier rejected', [
                    'supplier_id' => $supplier->id,
                    'supplier_name' => $supplier->name,
                    'rejected_by' => $rejectedBy->id,
                    'reason' => $reason,
                ]);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject supplier', [
                'supplier_id' => $supplier->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Block/suspend a supplier
     */
    public function blockSupplier(User $supplier, string $reason, ?User $blockedBy = null): bool
    {
        if (!$supplier->is_supplier) {
            throw new \Exception('User is not a supplier');
        }

        DB::beginTransaction();
        try {
            $supplier->update([
                'supplier_status' => 'blocked',
                'is_active' => false,
                'notes' => ($supplier->notes ?? '') . "\n\nBlocked: " . $reason . " (Date: " . now()->format('Y-m-d H:i:s') . ")",
            ]);

            if ($blockedBy) {
                Log::warning('Supplier blocked', [
                    'supplier_id' => $supplier->id,
                    'supplier_name' => $supplier->name,
                    'blocked_by' => $blockedBy->id,
                    'reason' => $reason,
                ]);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to block supplier', [
                'supplier_id' => $supplier->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Reactivate a blocked or inactive supplier
     */
    public function reactivateSupplier(User $supplier, ?User $reactivatedBy = null): bool
    {
        if (!$supplier->is_supplier) {
            throw new \Exception('User is not a supplier');
        }

        DB::beginTransaction();
        try {
            $supplier->update([
                'supplier_status' => 'active',
                'is_active' => true,
                'notes' => ($supplier->notes ?? '') . "\n\nReactivated (Date: " . now()->format('Y-m-d H:i:s') . ")",
            ]);

            if ($reactivatedBy) {
                Log::info('Supplier reactivated', [
                    'supplier_id' => $supplier->id,
                    'supplier_name' => $supplier->name,
                    'reactivated_by' => $reactivatedBy->id,
                ]);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reactivate supplier', [
                'supplier_id' => $supplier->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Calculate supplier performance metrics
     */
    public function calculatePerformanceMetrics(User $supplier): array
    {
        if (!$supplier->is_supplier) {
            return [];
        }

        // Get supplier's quotes
        $quotes = $supplier->quotes()->with('request')->get();
        $totalQuotes = $quotes->count();

        // Get accepted quotes
        $acceptedQuotes = $quotes->where('status', 'accepted')->count();

        // Calculate response time (average time from invitation to quote submission)
        $avgResponseTime = $supplier->supplierInvitations()
            ->whereNotNull('responded_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, sent_at, responded_at)) as avg_hours')
            ->value('avg_hours');

        // Get RFQ participation rate
        $invitations = $supplier->supplierInvitations()->count();
        $responded = $supplier->supplierInvitations()->whereNotNull('responded_at')->count();
        $participationRate = $invitations > 0 ? ($responded / $invitations) * 100 : 0;

        // Calculate win rate
        $winRate = $totalQuotes > 0 ? ($acceptedQuotes / $totalQuotes) * 100 : 0;

        // Get total value of won quotes
        $totalWonValue = $quotes->where('status', 'accepted')->sum('total_price');

        return [
            'total_quotes_submitted' => $totalQuotes,
            'quotes_accepted' => $acceptedQuotes,
            'win_rate' => round($winRate, 2),
            'participation_rate' => round($participationRate, 2),
            'avg_response_time_hours' => round($avgResponseTime ?? 0, 2),
            'total_won_value' => $totalWonValue,
            'success_rate' => $supplier->getSuccessRate(),
            'rating' => $supplier->rating ?? 0,
            'total_orders' => $supplier->total_orders ?? 0,
            'completed_orders' => $supplier->completed_orders ?? 0,
        ];
    }

    /**
     * Get supplier qualification status
     */
    public function getQualificationStatus(User $supplier): array
    {
        if (!$supplier->is_supplier) {
            return ['qualified' => false, 'missing_fields' => []];
        }

        $requiredFields = [
            'company_name' => 'Company Name',
            'tax_id' => 'Tax ID',
            'phone' => 'Phone Number',
            'address_line1' => 'Address',
            'city' => 'City',
            'country' => 'Country',
        ];

        $missingFields = [];
        foreach ($requiredFields as $field => $label) {
            if (empty($supplier->$field)) {
                $missingFields[] = $label;
            }
        }

        $qualified = empty($missingFields) && $supplier->supplier_status === 'active';

        return [
            'qualified' => $qualified,
            'missing_fields' => $missingFields,
            'status' => $supplier->supplier_status,
            'completion_percentage' => count($missingFields) > 0
                ? round((1 - count($missingFields) / count($requiredFields)) * 100, 0)
                : 100,
        ];
    }

    /**
     * Get supplier dashboard summary
     */
    public function getSupplierSummary(User $supplier): array
    {
        $metrics = $this->calculatePerformanceMetrics($supplier);
        $qualification = $this->getQualificationStatus($supplier);

        // Get recent quotes
        $recentQuotes = $supplier->quotes()
            ->with('request')
            ->latest()
            ->take(5)
            ->get();

        // Get pending invitations
        $pendingInvitations = $supplier->supplierInvitations()
            ->where('status', 'pending')
            ->with('request')
            ->latest()
            ->get();

        return [
            'metrics' => $metrics,
            'qualification' => $qualification,
            'recent_quotes' => $recentQuotes,
            'pending_invitations' => $pendingInvitations,
            'status' => $supplier->supplier_status,
            'approved_at' => $supplier->supplier_approved_at,
        ];
    }
}
