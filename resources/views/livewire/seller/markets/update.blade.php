<div>
    <x-slide wire="modal" bottom size="lg" blur="md">
        <x-slot name="title">{{ __('Update Market: #:id', ['id' => $market?->id]) }}</x-slot>
        <form id="seller-market-update-{{ $market?->id }}" wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <x-input
                    label="{{ __('Name') }}"
                    wire:model.blur="market.name"
                    required
                    hint="{{ __('Market name') }}"
                />

                <x-input
                    label="{{ __('Location') }}"
                    wire:model.blur="market.location"
                    hint="{{ __('Market location') }}"
                />

                <x-input
                    label="{{ __('Image Path') }}"
                    wire:model.blur="market.image_path"
                    hint="{{ __('URL or path to image') }}"
                />
            </div>

            <x-button
                type="submit"
                form="seller-market-update-{{ $market?->id }}"
                color="primary"
                loading="save"
                icon="check"
            >
                {{ __('Save Changes') }}
            </x-button>
        </form>
    </x-slide>
</div>
