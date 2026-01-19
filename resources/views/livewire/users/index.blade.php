<div>
    <x-card>
        <x-heading-title title="{{__('Users')}}" icon="user-group" padding="p-5" hover="-"/>

        <div class="mb-2 mt-4 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
            <livewire:users.create @created="$refresh"/>

            <div class="w-full sm:w-64">
                <x-select.styled
                    :label="__('Filter by Role')"
                    wire:model.live="roleFilter"
                    :options="[
                        ['label' => 'All Roles', 'value' => 'all'],
                        ['label' => 'Buyer', 'value' => 'buyer'],
                        ['label' => 'Seller', 'value' => 'seller'],
                        ['label' => 'Supplier', 'value' => 'supplier'],
                        ['label' => 'Admin', 'value' => 'admin'],
                    ]"
                    select="label:label|value:value"
                />
            </div>
        </div>

        <x-table :$headers :$sort :rows="$this->rows" paginate :paginator="null" filter loading :quantity="[5, 10, 20, 'all']">
            @interact('column_name', $row)
            <a href="{{ route('users.show', $row) }}" class="text-blue-600 hover:underline">
                <x-badge text="{{ $row->name }}" icon="eye" position="left"/>
            </a>
            @endinteract

            @interact('column_email', $row)
            {{ $row->email }}
            @endinteract

            @interact('column_company_name', $row)
            {{ $row->company_name ?? '-' }}
            @endinteract

            @interact('column_roles', $row)
            @php
                $roleNames = $row->roles?->pluck('name')->all() ?? [];
            @endphp
            <div class="flex gap-1 flex-wrap">
                @if(in_array('buyer', $roleNames, true) || $row->is_buyer)
                    <x-badge color="blue" text="Buyer" icon="shopping-cart" position="left" sm />
                @endif

                @if(in_array('seller', $roleNames, true) || $row->is_seller)
                    <x-badge color="green" text="{{ $row->verified_seller ? 'Verified Seller' : 'Seller' }}" icon="building-storefront" position="left" sm />
                @endif

                @if(in_array('supplier', $roleNames, true) || $row->is_supplier)
                    @if($row->supplier_status === 'active')
                        <x-badge color="purple" text="Active Supplier" icon="cube" position="left" sm />
                    @elseif($row->supplier_status === 'pending')
                        <x-badge color="yellow" text="Pending Supplier" icon="clock" position="left" sm />
                    @elseif($row->supplier_status === 'blocked')
                        <x-badge color="red" text="Blocked Supplier" icon="shield-exclamation" position="left" sm />
                    @elseif($row->supplier_status === 'inactive')
                        <x-badge color="slate" text="Inactive Supplier" icon="x-circle" position="left" sm />
                    @else
                        <x-badge color="slate" text="Supplier ({{ $row->supplier_status }})" icon="cube" position="left" sm />
                    @endif
                @endif

                @if(in_array('admin', $roleNames, true) || $row->is_admin)
                    <x-badge color="slate" text="Admin" icon="shield-check" position="left" sm />
                @endif

                @if(in_array('market_worker', $roleNames, true))
                    <x-badge color="slate" text="Worker" icon="users" position="left" sm />
                @endif
            </div>
            @endinteract

            @interact('column_created_at', $row)
            {{ $row->created_at->diffForHumans() }}
            @endinteract

            @interact('column_action', $row)
            <div class="flex gap-1">
                @can('edit_users')
                    <x-button.circle icon="pencil" wire:click="$dispatch('load::user', { 'user' : '{{ $row->id }}'})"/>
                @endcan
                @can('delete_users')
                    <livewire:users.delete :user="$row" :key="uniqid('', true)" @deleted="$refresh"/>
                @endcan
            </div>
            @endinteract
        </x-table>
    </x-card>

    <livewire:users.update @updated="$refresh"/>
</div>
