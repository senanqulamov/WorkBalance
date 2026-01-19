<div>
    <x-slide wire="modal" right size="6xl" blur="md">
        <x-slot name="title">{{ __('Update User: :name (#:id)', ['name' => $user?->name, 'id' => $user?->id]) }}</x-slot>

        <form id="user-update-{{ $user?->id }}" wire:submit="save">
            @php
                $selectedRoleNames = collect($roles ?? [])->whereIn('id', $roleIds ?? [])->pluck('name')->all();
                $isBuyerRole = in_array('buyer', $selectedRoleNames, true);
                $isSupplierRole = in_array('supplier', $selectedRoleNames, true);
                $isSellerRole = in_array('seller', $selectedRoleNames, true);
                $isMarketWorkerRole = in_array('market_worker', $selectedRoleNames, true);

                $marketOptions = collect($markets ?? [])->map(fn ($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                ])->values()->all();

                $sellerOptions = collect($sellers ?? [])->map(fn ($u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                ])->values()->all();
            @endphp

            <x-tab selected="{{ __('Account') }}" :border="true">
                {{-- TAB 1: Account (name/email/password/roles) --}}
                <x-tab.items tab="{{ __('Account') }}">
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-input
                                label="{{ __('Full Name') }} *"
                                wire:model="user.name"
                                icon="user"
                                required
                            />
                            <x-input
                                label="{{ __('Email Address') }} *"
                                wire:model="user.email"
                                icon="envelope"
                                required
                            />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-password
                                label="{{ __('New Password') }}"
                                wire:model="password"
                                rules
                                generator
                                x-on:generate="$wire.set('password_confirmation', $event.detail.password)"
                                hint="{{ __('Leave empty to keep current password') }}"
                            />
                            <x-password
                                label="{{ __('Confirm Password') }}"
                                wire:model="password_confirmation"
                                rules
                            />
                        </div>

                        {{-- Roles (pivot) --}}
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                <x-icon name="shield-check" class="w-4 h-4 inline" /> {{ __('User Roles') }}
                            </label>

                            <div class="flex flex-wrap gap-3">
                                @foreach($roles as $role)
                                    <x-cst_checkbox
                                        wire:key="user-update-{{ $user?->id ?? 'new' }}-role-{{ $role->id }}"
                                        :id="'user_' . ($user?->id ?? 'new') . '_role_' . $role->id"
                                        :label="$role->display_name ?? ucfirst($role->name)"
                                        :value="$role->id"
                                        model="roleIds"
                                    />
                                @endforeach
                            </div>

                            <div class="text-xs text-gray-500 mt-3">
                                {{ __('Select one or more roles. Additional details will be added after creating the user.') }}
                            </div>
                        </div>
                    </div>
                </x-tab.items>

                {{-- TAB 2: General (common data) --}}
                <x-tab.items tab="{{ __('General') }}">
                    <div class="space-y-6">
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <x-icon name="document-text" class="w-4 h-4 inline" /> {{ __('Account Status') }}
                                </label>
                                <div class="flex items-center">
                                    <input
                                        type="checkbox"
                                        wire:model="user.is_active"
                                        id="is_active_update"
                                        class="rounded border-gray-300 dark:border-gray-700 text-green-600 shadow-sm focus:ring-green-500 w-5 h-5"
                                    >
                                    <label for="is_active_update" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ __('Active Account') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Contact + address (common) --}}
                        <div class="space-y-4">
                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('Contact & Address') }}</div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-input label="{{ __('Phone Number') }}" wire:model="user.phone" icon="phone" />
                                <x-input label="{{ __('Mobile Number') }}" wire:model="user.mobile" icon="device-phone-mobile" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-input label="{{ __('Address Line 1') }}" wire:model="user.address_line1" icon="map-pin" />
                                <x-input label="{{ __('Address Line 2') }}" wire:model="user.address_line2" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <x-input label="{{ __('City') }}" wire:model="user.city" />
                                <x-input label="{{ __('State / Province') }}" wire:model="user.state" />
                                <x-input label="{{ __('Postal Code') }}" wire:model="user.postal_code" />
                            </div>

                            <x-input label="{{ __('Country') }}" wire:model="user.country" icon="globe-americas" />
                        </div>

                        {{-- Notes --}}
                        <x-textarea
                            label="{{ __('Internal Notes') }}"
                            wire:model="user.notes"
                            rows="4"
                        />
                    </div>
                </x-tab.items>

                {{-- TAB 3: Role details (supplier/seller) --}}
                <x-tab.items tab="{{ __('Role Details') }}">
                    <div class="space-y-10">
                        {{-- Buyer details (Business Information) --}}
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('Buyer Details') }}</div>
                                @if($isBuyerRole)
                                    <x-badge color="blue" text="Buyer" sm />
                                @else
                                    <x-badge color="slate" text="Not a buyer" sm />
                                @endif
                            </div>

                            @if($isBuyerRole)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-input label="{{ __('Company Name') }}" wire:model="user.company_name" icon="building-office" />
                                    <x-input label="{{ __('Tax ID / VAT') }}" wire:model="user.tax_id" icon="document-text" />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-select.styled
                                        label="{{ __('Business Type') }}"
                                        wire:model="user.business_type"
                                        :options="[
                                            ['label' => 'Individual', 'value' => 'Individual'],
                                            ['label' => 'Company', 'value' => 'Company'],
                                            ['label' => 'Corporation', 'value' => 'Corporation'],
                                            ['label' => 'Partnership', 'value' => 'Partnership'],
                                            ['label' => 'LLC', 'value' => 'LLC']
                                        ]"
                                        select="label:label|value:value"
                                    />

                                    <x-input
                                        label="{{ __('Website') }}"
                                        wire:model="user.website"
                                        icon="globe-alt"
                                        placeholder="https://example.com"
                                    />
                                </div>

                                <x-textarea
                                    label="{{ __('Business Description') }}"
                                    wire:model="user.business_description"
                                    rows="3"
                                />
                            @else
                                <x-alert color="warning">
                                    {{ __('Assign the Buyer role in the Account tab to edit buyer business fields.') }}
                                </x-alert>
                            @endif
                        </div>

                        {{-- Market worker details --}}
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('Market Worker Details') }}</div>
                                @if($isMarketWorkerRole)
                                    <x-badge color="slate" text="Worker" sm />
                                @else
                                    <x-badge color="slate" text="Not a worker" sm />
                                @endif
                            </div>

                            @if($isMarketWorkerRole)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-select.styled
                                        :label="__('Seller Owner')"
                                        wire:model="sellerOwnerId"
                                        :options="$sellerOptions"
                                        select="label:name|value:id"
                                        searchable
                                        :placeholder="__('Select seller')"
                                    />

                                    <x-select.styled
                                        :label="__('Assigned Markets')"
                                        wire:model="marketIds"
                                        :options="$marketOptions"
                                        select="label:name|value:id"
                                        searchable
                                        multiple
                                        :placeholder="__('Select markets')"
                                    />
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ __('Worker can be assigned to multiple markets. Markets must belong to the selected seller.') }}
                                </div>
                            @else
                                <x-alert color="warning">
                                    {{ __('Assign the Market Worker role in the Account tab to manage seller/market assignments.') }}
                                </x-alert>
                            @endif
                        </div>

                        {{-- Supplier details --}}
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('Supplier Details') }}</div>

                                @if($isSupplierRole)
                                    <x-badge color="purple" text="Supplier" sm />
                                @endif
                                @if($isSupplierRole === false)
                                    <x-badge color="slate" text="Not a supplier" sm />
                                @endif
                            </div>

                            @if($isSupplierRole)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-input label="{{ __('Supplier Code') }}" wire:model="user.supplier_code" icon="qr-code" placeholder="SUP-" />
                                    <x-input label="{{ __('D-U-N-S Number') }}" wire:model="user.duns_number" icon="identification" />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-input label="{{ __('Ariba Network ID (ANID)') }}" wire:model="user.ariba_network_id" icon="link" placeholder="AN" />
                                    <x-select.styled
                                        label="{{ __('Currency') }}"
                                        wire:model="user.currency"
                                        :options="[
                                            ['label' => 'USD - US Dollar', 'value' => 'USD'],
                                            ['label' => 'EUR - Euro', 'value' => 'EUR'],
                                            ['label' => 'GBP - British Pound', 'value' => 'GBP'],
                                            ['label' => 'JPY - Japanese Yen', 'value' => 'JPY'],
                                            ['label' => 'CNY - Chinese Yuan', 'value' => 'CNY']
                                        ]"
                                        select="label:label|value:value"
                                    />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-number label="{{ __('Credit Limit') }}" wire:model="user.credit_limit" icon="currency-dollar" min="0" step="0.01" />
                                    <x-select.styled
                                        label="{{ __('Supplier Status') }}"
                                        wire:model="user.supplier_status"
                                        :options="[
                                            ['label' => 'Active', 'value' => 'active'],
                                            ['label' => 'Pending', 'value' => 'pending'],
                                            ['label' => 'Inactive', 'value' => 'inactive'],
                                            ['label' => 'Blocked', 'value' => 'blocked']
                                        ]"
                                        select="label:label|value:value"
                                    />
                                </div>
                            @endif

                            @if($isSupplierRole === false)
                                <x-alert color="warning">
                                    {{ __('Assign the Supplier role in the Account tab to edit supplier fields.') }}
                                </x-alert>
                            @endif
                        </div>

                        {{-- Seller details --}}
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('Seller Details') }}</div>

                                @if($isSellerRole)
                                    <x-badge color="green" text="Seller" sm />
                                @endif
                                @if($isSellerRole === false)
                                    <x-badge color="slate" text="Not a seller" sm />
                                @endif
                            </div>

                            @if($isSellerRole)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-number
                                        label="{{ __('Commission Rate (%)') }}"
                                        wire:model="user.commission_rate"
                                        icon="percent-badge"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                    />

                                    <div class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                        <input
                                            type="checkbox"
                                            wire:model="user.verified_seller"
                                            id="verified_seller_update"
                                            class="rounded border-gray-300 dark:border-gray-700 text-green-600 shadow-sm focus:ring-green-500 w-5 h-5"
                                        >
                                        <label for="verified_seller_update" class="ml-3 flex-1 cursor-pointer">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Verified Seller') }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Mark as verified seller') }}</div>
                                        </label>
                                    </div>
                                </div>
                            @endif

                            @if($isSellerRole === false)
                                <x-alert color="warning">
                                    {{ __('Assign the Seller role in the Account tab to edit seller fields.') }}
                                </x-alert>
                            @endif
                        </div>
                    </div>
                </x-tab.items>
            </x-tab>
        </form>

        <x-slot:footer>
            <div class="flex justify-between items-center w-full">
                <x-button flat label="{{ __('Cancel') }}" wire:click="$toggle('modal')" />
                <x-button
                    type="submit"
                    form="user-update-{{ $user?->id }}"
                    color="primary"
                    icon="check"
                    loading="save"
                >
                    {{ __('Save Changes') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-slide>
</div>
