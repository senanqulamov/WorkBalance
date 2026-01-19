<?php

namespace App\Livewire\Users;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\User;
use App\Services\SupplierLifecycleService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class SupplierActions extends Component
{
    use Alert, WithLogging;

    public ?User $supplier = null;

    public bool $approveModal = false;
    public bool $rejectModal = false;
    public bool $blockModal = false;

    public string $rejectionReason = '';
    public string $blockReason = '';

    protected SupplierLifecycleService $service;

    public function boot(SupplierLifecycleService $service)
    {
        $this->service = $service;
    }

    public function mount(): void
    {
        // Initialize with empty user to prevent Livewire entangle errors
        $this->supplier = new User;
    }

    public function render(): View
    {
        return view('livewire.users.supplier-actions');
    }

    #[On('load::supplier::actions')]
    public function load(User $supplier): void
    {
        if (!$supplier->is_supplier) {
            $this->error('User is not a supplier');
            return;
        }

        $this->supplier = $supplier;
    }

    #[On('load::supplier::approve')]
    public function loadAndApprove(User $supplier): void
    {

        if (!$supplier->is_supplier) {
            $this->error('Invalid supplier');
            return;
        }

        $this->supplier = $supplier;
        $this->openApproveModal();
    }

    #[On('load::supplier::reject')]
    public function loadAndReject(User $supplier): void
    {
        if (!$supplier->is_supplier) {
            $this->error('Invalid supplier');
            return;
        }

        $this->supplier = $supplier;
        $this->openRejectModal();
    }

    #[On('load::supplier::block')]
    public function loadAndBlock(User $supplier): void
    {
        if (!$supplier->is_supplier) {
            $this->error('Invalid supplier');
            return;
        }

        $this->supplier = $supplier;
        $this->openBlockModal();
    }

    #[On('load::supplier::reactivate')]
    public function loadAndReactivate(User $supplier): void
    {
        if (!$supplier->is_supplier) {
            $this->error('Invalid supplier');
            return;
        }

        $this->supplier = $supplier;
        $this->reactivateSupplier();
    }

    public function openApproveModal(): void
    {
        if (!$this->supplier?->id) {
            $this->error('No supplier selected');
            return;
        }

        $this->approveModal = true;
    }

    public function openRejectModal(): void
    {
        if (!$this->supplier?->id) {
            $this->error('No supplier selected');
            return;
        }

        $this->rejectModal = true;
    }

    public function openBlockModal(): void
    {
        if (!$this->supplier?->id) {
            $this->error('No supplier selected');
            return;
        }

        $this->blockModal = true;
    }

    public function approveSupplier(): void
    {
        if (!Auth::user()->hasPermission('approve_suppliers')) {
            $this->error('You do not have permission to approve suppliers.');
            return;
        }

        if (!$this->supplier?->id) {
            $this->error('No supplier selected');
            return;
        }

        try {
            $supplierId = $this->supplier->id;
            $this->service->approveSupplier($this->supplier, Auth::user());

            $this->logUpdate(
                User::class,
                $supplierId,
                ['supplier_status' => ['old' => 'pending', 'new' => 'active']]
            );

            $this->success('Supplier approved successfully');
            $this->approveModal = false;
            $this->reset('supplier');

            // Refresh the page to show updated status
            $this->redirect(route('users.show', $supplierId), navigate: true);
        } catch (\Exception $e) {
            $this->error('Failed to approve supplier: ' . $e->getMessage());
        }
    }

    public function rejectSupplier(): void
    {
        if (!Auth::user()->hasPermission('approve_suppliers')) {
            $this->error('You do not have permission to reject suppliers.');
            return;
        }

        if (!$this->supplier?->id) {
            $this->error('No supplier selected');
            return;
        }

        if (empty($this->rejectionReason)) {
            $this->error('Please provide a rejection reason');
            return;
        }

        try {
            $supplierId = $this->supplier->id;
            $this->service->rejectSupplier($this->supplier, $this->rejectionReason, Auth::user());

            $this->logUpdate(
                User::class,
                $supplierId,
                [
                    'supplier_status' => ['old' => 'pending', 'new' => 'inactive'],
                    'rejection_reason' => $this->rejectionReason
                ]
            );

            $this->success('Supplier rejected');
            $this->rejectModal = false;
            $this->reset(['supplier', 'rejectionReason']);

            // Refresh the page to show updated status
            $this->redirect(route('users.show', $supplierId), navigate: true);
        } catch (\Exception $e) {
            $this->error('Failed to reject supplier: ' . $e->getMessage());
        }
    }

    public function blockSupplier(): void
    {
        if (!Auth::user()->hasPermission('approve_suppliers')) {
            $this->error('You do not have permission to block suppliers.');
            return;
        }

        if (!$this->supplier?->id) {
            $this->error('No supplier selected');
            return;
        }

        if (empty($this->blockReason)) {
            $this->error('Please provide a blocking reason');
            return;
        }

        try {
            $supplierId = $this->supplier->id;
            $oldStatus = $this->supplier->supplier_status;
            $this->service->blockSupplier($this->supplier, $this->blockReason, Auth::user());

            $this->logUpdate(
                User::class,
                $supplierId,
                [
                    'supplier_status' => ['old' => $oldStatus, 'new' => 'blocked'],
                    'block_reason' => $this->blockReason
                ]
            );

            $this->success('Supplier blocked successfully');
            $this->blockModal = false;
            $this->reset(['supplier', 'blockReason']);

            // Refresh the page to show updated status
            $this->redirect(route('users.show', $supplierId), navigate: true);
        } catch (\Exception $e) {
            $this->error('Failed to block supplier: ' . $e->getMessage());
        }
    }

    public function reactivateSupplier(): void
    {
        if (!Auth::user()->hasPermission('approve_suppliers')) {
            $this->error('You do not have permission to reactivate suppliers.');
            return;
        }

        if (!$this->supplier?->id) {
            $this->error('No supplier selected');
            return;
        }

        try {
            $supplierId = $this->supplier->id;
            $oldStatus = $this->supplier->supplier_status;
            $this->service->reactivateSupplier($this->supplier, Auth::user());

            $this->logUpdate(
                User::class,
                $supplierId,
                ['supplier_status' => ['old' => $oldStatus, 'new' => 'active']]
            );

            $this->success('Supplier reactivated successfully');
            $this->reset(['supplier']);

            // Refresh the page
            $this->redirect(route('users.show', $supplierId), navigate: true);
        } catch (\Exception $e) {
            $this->error('Failed to reactivate supplier: ' . $e->getMessage());
        }
    }
}
