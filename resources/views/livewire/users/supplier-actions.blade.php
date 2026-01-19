<div>
    {{-- Approve Modal --}}
    <x-modal wire="approveModal" title="Approve Supplier" blur>
        @if($supplier?->id)
            <div class="space-y-4">
                <p class="text-gray-700 dark:text-gray-300">
                    Are you sure you want to approve <strong>{{ $supplier->name }}</strong> as a supplier?
                </p>

                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                    <h4 class="font-medium text-blue-900 dark:text-blue-100 mb-2">This will:</h4>
                    <ul class="list-disc list-inside text-sm text-blue-800 dark:text-blue-200 space-y-1">
                        <li>Activate the supplier account</li>
                        <li>Allow them to receive RFQ invitations</li>
                        <li>Enable quote submissions</li>
                        <li>Grant access to supplier portal</li>
                    </ul>
                </div>
            </div>

            <x-slot:footer>
                <x-button text="Cancel" wire:click="$set('approveModal', false)"/>
                <x-button text="Approve Supplier" wire:click="approveSupplier" color="green"/>
            </x-slot:footer>
        @endif
    </x-modal>

    {{-- Reject Modal --}}
    <x-modal wire="rejectModal" title="Reject Supplier" blur size="lg">
        @if($supplier?->id)
            <div class="space-y-4">
                <p class="text-gray-700 dark:text-gray-300">
                    You are about to reject <strong>{{ $supplier->name }}</strong>'s supplier application.
                </p>

                <x-textarea
                    label="Rejection Reason *"
                    wire:model="rejectionReason"
                    hint="Provide a reason for rejection (will be added to notes)"
                    rows="4"
                    required
                />
            </div>

            <x-slot:footer>
                <x-button text="Cancel" wire:click="$set('rejectModal', false)"/>
                <x-button text="Reject Application" wire:click="rejectSupplier" color="red"/>
            </x-slot:footer>
        @endif
    </x-modal>

    {{-- Block Modal --}}
    <x-modal wire="blockModal" title="Block Supplier" blur size="lg">
        @if($supplier?->id)
            <div class="space-y-4">
                <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                    <p class="text-red-800 dark:text-red-200 font-medium">⚠️ {{ __('Warning')}}</p>
                    <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                        {!! __('Blocking :name will immediately suspend their account and prevent all activities.', ['name' => '<strong>' . e($supplier->name) . '</strong>']) !!}
                    </p>
                </div>

                <x-textarea
                    label="{{ __('Blocking Reason')}} *"
                    wire:model="blockReason"
                    hint="{{ __('Provide a reason for blocking this supplier')}}"
                    rows="4"
                    required
                />
            </div>

            <x-slot:footer>
                <x-button text="Cancel" wire:click="$set('blockModal', false)"/>
                <x-button text="Block Supplier" wire:click="blockSupplier" color="red"/>
            </x-slot:footer>
        @endif
    </x-modal>
</div>
