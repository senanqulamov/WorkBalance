<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-400 dark:text-slate-100 flex items-center gap-3">
            <x-icon name="cog-6-tooth" class="w-8 h-8 text-red-500"/>
            {{ __('System Settings') }}
        </h1>
        <p class="text-gray-500 dark:text-slate-400 mt-2">
            {{ __('Configure and manage your SAP procurement dashboard settings') }}
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar Navigation -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="font-semibold text-slate-300 dark:text-slate-100">{{ __('Settings') }}</h3>
                </div>
                <nav class="p-2">
                    @php
                        $tabs = [
                            'general' => ['icon' => 'adjustments-horizontal', 'label' => __('General')],
                            'sap' => ['icon' => 'server-stack', 'label' => __('SAP Integration')],
                            'mail' => ['icon' => 'envelope', 'label' => __('Email')],
                            'database' => ['icon' => 'circle-stack', 'label' => __('Database')],
                            'cache' => ['icon' => 'bolt', 'label' => __('Cache & Queue')],
                            'security' => ['icon' => 'shield-check', 'label' => __('Security')],
                            'api' => ['icon' => 'code-bracket', 'label' => __('API')],
                            'notifications' => ['icon' => 'bell', 'label' => __('Notifications')],
                            'business' => ['icon' => 'briefcase', 'label' => __('Business Rules')],
                            'files' => ['icon' => 'document', 'label' => __('File Uploads')],
                            'system' => ['icon' => 'computer-desktop', 'label' => __('System Info')],
                        ];
                    @endphp

                    @foreach($tabs as $key => $tab)
                        <button
                            wire:click="$set('activeTab', '{{ $key }}')"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition cursor-pointer {{ $activeTab === $key ? 'bg-red-500 text-white' : 'text-slate-300 dark:text-slate-400 hover:bg-slate-600 dark:hover:bg-slate-800' }}">
                            <x-icon name="{{ $tab['icon'] }}" class="w-5 h-5"/>
                            {{ $tab['label'] }}
                        </button>
                    @endforeach
                </nav>
            </div>

            <!-- Quick Actions -->
            <div class="mt-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                <h4 class="font-semibold text-sm text-slate-300 dark:text-slate-100 mb-3">{{ __('Quick Actions') }}</h4>
                <div class="space-y-2">
                    <button wire:click="clearCache" class="w-full text-left px-3 py-2 rounded-lg text-sm text-slate-400 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <x-icon name="trash" class="w-4 h-4 inline mr-2"/>
                        {{ __('Clear Cache') }}
                    </button>
                    <button wire:click="clearLogs" class="w-full text-left px-3 py-2 rounded-lg text-sm text-slate-400 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <x-icon name="document-minus" class="w-4 h-4 inline mr-2"/>
                        {{ __('Clear Logs') }}
                    </button>
                    <button wire:click="toggleMaintenance"
                            class="w-full text-left px-3 py-2 rounded-lg text-sm {{ $maintenance_mode ? 'text-red-600 hover:bg-red-50' : 'text-slate-400 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }} transition">
                        <x-icon name="wrench-screwdriver" class="w-4 h-4 inline mr-2"/>
                        {{ $maintenance_mode ? __('Disable Maintenance') : __('Enable Maintenance') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <x-card>

                @if($maintenance_mode)
                    <x-alert color="yellow" icon="exclamation-triangle" class="mb-4">
                        <strong>{{ __('Maintenance Mode Active') }}</strong> - {{ __('Public access is currently restricted') }}
                    </x-alert>
                @endif

                <!-- Tab Content -->
                <div class="space-y-6">
                    {{-- General Settings --}}
                    @if($activeTab === 'general')
                        <div>
                            <h3 class="text-xl font-semibold mb-4">{{ __('General Settings') }}</h3>
                            <form wire:submit="saveGeneral" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-input label="{{ __('Application Name') }}" wire:model="app_name" hint="{{ __('Display name of your application') }}" />
                                    <x-input label="{{ __('Application URL') }}" wire:model="app_url" hint="{{ __('Base URL') }}" />
                                    <x-select.styled
                                        label="{{ __('Timezone') }}"
                                        wire:model="app_timezone"
                                        :options="[
                                            ['label' => 'UTC', 'value' => 'UTC'],
                                            ['label' => 'America/New_York', 'value' => 'America/New_York'],
                                            ['label' => 'Europe/London', 'value' => 'Europe/London'],
                                            ['label' => 'Europe/Berlin', 'value' => 'Europe/Berlin'],
                                            ['label' => 'Asia/Tokyo', 'value' => 'Asia/Tokyo'],
                                            ['label' => 'Asia/Dubai', 'value' => 'Asia/Dubai'],
                                        ]"
                                        select="label:label|value:value"
                                        searchable
                                    />
                                    <x-select.styled
                                        label="{{ __('Default Language') }}"
                                        wire:model="app_locale"
                                        :options="[
                                            ['label' => 'English', 'value' => 'en'],
                                            ['label' => 'Deutsch', 'value' => 'de'],
                                            ['label' => 'Español', 'value' => 'es'],
                                            ['label' => 'Français', 'value' => 'fr'],
                                            ['label' => 'Türkçe', 'value' => 'tr'],
                                            ['label' => 'Azərbaycanca', 'value' => 'az'],
                                        ]"
                                        select="label:label|value:value"
                                        searchable
                                    />
                                    <x-select.styled
                                        label="{{ __('Environment') }}"
                                        wire:model="app_env"
                                        :options="[
                                            ['label' => 'Production', 'value' => 'production'],
                                            ['label' => 'Staging', 'value' => 'staging'],
                                            ['label' => 'Development', 'value' => 'development'],
                                            ['label' => 'Local', 'value' => 'local'],
                                        ]"
                                        select="label:label|value:value"
                                    />
                                    <div class="flex items-center gap-3 p-4 bg-slate-800 dark:bg-slate-800 rounded-lg">
                                        <x-toggle wire:model.live="app_debug"/>
                                        <div>
                                            <p class="font-medium text-sm">{{ __('Debug Mode') }}</p>
                                            <p class="text-xs text-slate-300">{{ __('Show detailed error messages') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <x-button type="submit" color="primary" icon="check">{{ __('Save General Settings') }}</x-button>
                            </form>
                        </div>
                    @endif

                    {{-- SAP Integration --}}
                    @if($activeTab === 'sap')
                        <div>
                            <h3 class="text-xl font-semibold mb-4 flex items-center justify-between">
                                <span>{{ __('SAP Integration Settings') }}</span>
                                <div class="flex items-center gap-3">
                                    <div class="hidden md:block text-sm">
                                        <span class="font-medium {{ $sap_enabled ? 'text-emerald-600' : 'text-slate-500' }}">{{ $sap_enabled ? __('Enabled') : __('Disabled') }}</span>
                                    </div>
                                    <div class="md:ml-2">
                                        <x-toggle wire:model.live="sap_enabled" />
                                    </div>
                                </div>
                            </h3>

                            <form wire:submit="saveSap" class="space-y-4">
                                @if($sap_enabled)
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 bg-slate-800 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg">
                                        <!-- Left: Connection fields -->
                                        <div class="md:col-span-2 space-y-4 dark:text-slate-300">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <x-input label="{{ __('SAP Host') }}" wire:model="sap_host" hint="{{ __('SAP server hostname') }}"/>
                                                <x-input label="{{ __('SAP Client') }}" wire:model="sap_client" hint="{{ __('SAP client number') }}"/>
                                                <x-input class="col-span-2" label="{{ __('SAP Username') }}" wire:model="sap_username"/>
                                                <x-password class="col-span-2" label="{{ __('SAP Password') }}" wire:model="sap_password"/>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <x-select.styled
                                                    label="{{ __('Language') }}"
                                                    wire:model="sap_language"
                                                    :options="[
                                                        ['label' => 'English', 'value' => 'EN'],
                                                        ['label' => 'German', 'value' => 'DE'],
                                                        ['label' => 'French', 'value' => 'FR'],
                                                    ]"
                                                    select="label:label|value:value"
                                                />

                                                <x-input label="{{ __('Sync Interval (minutes)') }}" wire:model="sap_sync_interval" type="number"/>

                                                <div class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-lg border border-slate-100 dark:border-slate-700">
                                                    <x-toggle wire:model.live="sap_auto_sync"/>
                                                    <div>
                                                        <p class="font-medium text-sm">{{ __('Auto Sync') }}</p>
                                                        <p class="text-xs text-slate-500 dark:text-slate-300">{{ __('Automatically sync data') }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Advanced / Notes -->
                                            <div class="mt-2 p-4 bg-white dark:bg-slate-800 rounded-lg border border-slate-100 dark:border-slate-700">
                                                <p class="text-sm text-slate-300 dark:text-slate-300">{{ __('Notes: Use a service account for integration. Ensure network access from application server to SAP host and that the credentials have permission to call BAPIs you intend to use.') }}</p>
                                            </div>
                                        </div>

                                        <!-- Right: Status & actions -->
                                        <aside class="md:col-span-1 space-y-4">
                                            <div class="p-4 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-3">
                                                        <x-icon name="cloud" class="w-5 h-5 text-slate-600 dark:text-slate-300"/>
                                                        <div>
                                                            <p class="text-xs text-slate-500">{{ __('Connection') }}</p>
                                                            <p class="font-medium text-sm">
                                                                @if(isset($sap_test_status) && $sap_test_status === true)
                                                                    <span class="inline-flex items-center gap-2 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">{{ __('Connected') }}</span>
                                                                @elseif(isset($sap_test_status) && $sap_test_status === false)
                                                                    <span class="inline-flex items-center gap-2 px-2 py-0.5 rounded-full bg-red-50 text-red-700 border border-red-100">{{ __('Failed') }}</span>
                                                                @else
                                                                    <span class="inline-flex items-center gap-2 px-2 py-0.5 rounded-full bg-slate-50 dark:bg-slate-900 text-slate-600">{{ __('Unknown') }}</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-3 space-y-2">
                                                    <div class="text-xs text-slate-500">{{ __('Last Sync') }}</div>
                                                    <div class="text-sm text-slate-600 dark:text-slate-200">{{ $last_sap_sync ?? __('Never') }}</div>
                                                </div>

                                                <div class="mt-4 flex flex-col gap-2">
                                                    <x-button wire:click.prevent="testSapConnection" type="button" color="secondary" icon="signal">{{ __('Test Connection') }}</x-button>
                                                    <x-button wire:click.prevent="runSapSyncNow" type="button" color="black" icon="arrow-path">{{ __('Sync Now') }}</x-button>
                                                    <x-button wire:click.prevent="clearSapCredentials" type="button" color="ghost" icon="trash">{{ __('Clear Credentials') }}</x-button>
                                                </div>
                                            </div>

                                            <div class="p-4 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                                                <h4 class="text-sm font-medium mb-2">{{ __('Security') }}</h4>
                                                <p class="text-xs text-slate-500 mb-3">{{ __('Credentials are stored encrypted. Rotate regularly and limit access.') }}</p>
                                                <div class="text-xs">
                                                    <a href="https://help.sap.com" target="_blank" class="text-blue-600 dark:text-blue-400 underline">{{ __('SAP Help') }}</a>
                                                </div>
                                            </div>

                                            <div class="p-3 text-xs text-slate-500 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-100 dark:border-slate-700">
                                                <strong>{{ __('Tip:') }}</strong> {{ __('If information does not appear as expected, refresh the page or reopen the section. If the issue persists, contact your system administrator.') }}
                                            </div>
                                        </aside>
                                    </div>

                                    <div class="flex gap-2">
                                        <x-button type="submit" color="primary" icon="check">{{ __('Save SAP Settings') }}</x-button>
                                        <x-button type="button" wire:click="testSapConnection" color="secondary" icon="signal">{{ __('Test Connection') }}</x-button>
                                    </div>
                                @else
                                    <!-- Lightweight notice to replace x-alert to avoid component style conflicts -->
                                    <div class="p-4 rounded-lg border-l-4 border-blue-500 bg-blue-50 dark:bg-slate-900">
                                        <div class="flex items-start gap-3">
                                            <x-icon name="information-circle" class="w-5 h-5 text-blue-500 mt-0.5" />
                                            <div>
                                                <p class="text-sm text-slate-300 dark:text-slate-300">{{ __('Enable SAP integration to configure connection settings') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </form>
                        </div>
                    @endif

                    {{-- Mail Settings --}}
                    @if($activeTab === 'mail')
                        <div>
                            <h3 class="text-xl font-semibold mb-4">{{ __('Email Configuration') }}</h3>
                            <form wire:submit="saveMail" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-select.styled
                                        label="{{ __('Mail Driver') }}"
                                        wire:model="mail_driver"
                                        :options="[
                                            ['label' => 'SMTP', 'value' => 'smtp'],
                                            ['label' => 'Sendmail', 'value' => 'sendmail'],
                                            ['label' => 'Mailgun', 'value' => 'mailgun'],
                                            ['label' => 'SES', 'value' => 'ses'],
                                        ]"
                                        select="label:label|value:value"
                                    />
                                    <x-input label="{{ __('SMTP Host') }}" wire:model="mail_host"/>
                                    <x-input label="{{ __('SMTP Port') }}" wire:model="mail_port" type="number"/>
                                    <x-select.styled
                                        label="{{ __('Encryption') }}"
                                        wire:model="mail_encryption"
                                        :options="[
                                            ['label' => 'TLS', 'value' => 'tls'],
                                            ['label' => 'SSL', 'value' => 'ssl'],
                                        ]"
                                        select="label:label|value:value"
                                    />
                                    <x-input label="{{ __('Username') }}" wire:model="mail_username"/>
                                    <x-password label="{{ __('Password') }}" wire:model="mail_password"/>
                                    <x-input label="{{ __('From Address') }}" wire:model="mail_from_address" type="email"/>
                                    <x-input label="{{ __('From Name') }}" wire:model="mail_from_name"/>
                                </div>
                                <div class="flex gap-2">
                                    <x-button type="submit" color="primary" icon="check">{{ __('Save Mail Settings') }}</x-button>
                                    <x-button type="button" wire:click="testMailConnection" color="secondary" icon="envelope">{{ __('Test Connection') }}</x-button>
                                </div>
                            </form>
                        </div>
                    @endif

                    {{-- Database Settings --}}
                    @if($activeTab === 'database')
                        <div>
                            <h3 class="text-xl font-semibold mb-4">{{ __('Database Configuration') }}</h3>
                            <form wire:submit="saveDatabase" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-select.styled
                                        label="{{ __('Connection') }}"
                                        wire:model="db_connection"
                                        :options="[
                                            ['label' => 'MySQL', 'value' => 'mysql'],
                                            ['label' => 'PostgreSQL', 'value' => 'pgsql'],
                                            ['label' => 'SQL Server', 'value' => 'sqlsrv'],
                                        ]"
                                        select="label:label|value:value"
                                    />
                                    <x-input label="{{ __('Host') }}" wire:model="db_host"/>
                                    <x-input label="{{ __('Port') }}" wire:model="db_port" type="number"/>
                                    <x-input label="{{ __('Database') }}" wire:model="db_database"/>
                                    <x-input label="{{ __('Username') }}" wire:model="db_username"/>
                                    <x-password label="{{ __('Password') }}" wire:model="db_password"/>
                                </div>
                                <div class="flex gap-2">
                                    <x-button type="submit" color="primary" icon="check">{{ __('Save Database Settings') }}</x-button>
                                    <x-button type="button" wire:click="testDatabaseConnection" color="secondary" icon="circle-stack">{{ __('Test Connection') }}</x-button>
                                </div>
                            </form>
                        </div>
                    @endif

                    {{-- Cache & Queue --}}
                    @if($activeTab === 'cache')
                        <div>
                            <h3 class="text-xl font-semibold mb-4">{{ __('Cache & Queue Settings') }}</h3>
                            <div class="space-y-6">
                                <div>
                                    <h4 class="font-medium mb-3">{{ __('Cache Configuration') }}</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <x-select.styled
                                            label="{{ __('Cache Driver') }}"
                                            wire:model="cache_driver"
                                            :options="[
                                                ['label' => 'Redis', 'value' => 'redis'],
                                                ['label' => 'Memcached', 'value' => 'memcached'],
                                                ['label' => 'File', 'value' => 'file'],
                                            ]"
                                            select="label:label|value:value"
                                        />
                                        <x-input label="{{ __('Default TTL (seconds)') }}" wire:model="cache_ttl" type="number"/>
                                        <div class="flex items-center gap-3 p-4 bg-slate-800 dark:bg-slate-800 rounded-lg">
                                            <x-toggle wire:model.live="cache_enabled"/>
                                            <div>
                                                <p class="font-medium text-sm">{{ __('Enable Caching') }}</p>
                                                <p class="text-xs text-slate-300">{{ __('Improve performance') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-medium mb-3">{{ __('Queue Configuration') }}</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <x-select.styled
                                            label="{{ __('Queue Driver') }}"
                                            wire:model="queue_driver"
                                            :options="[
                                                ['label' => 'Redis', 'value' => 'redis'],
                                                ['label' => 'Database', 'value' => 'database'],
                                                ['label' => 'Sync', 'value' => 'sync'],
                                            ]"
                                            select="label:label|value:value"
                                        />
                                        <x-input label="{{ __('Retry After (seconds)') }}" wire:model="queue_retry_after" type="number"/>
                                        <x-input label="{{ __('Number of Workers') }}" wire:model="queue_workers" type="number"/>
                                    </div>
                                </div>
                                <div class="flex gap-2 mt-4">
                                    <x-button wire:click="saveCache" color="primary" icon="check">{{ __('Save Cache Settings') }}</x-button>
                                    <x-button wire:click="saveQueue" color="secondary" icon="check">{{ __('Save Queue Settings') }}</x-button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Security Settings --}}
                    @if($activeTab === 'security')
                        <div>
                            <h3 class="text-xl font-semibold mb-4">{{ __('Security Settings') }}</h3>
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-input label="{{ __('Session Lifetime (minutes)') }}" wire:model="session_lifetime" type="number"/>
                                    <x-input label="{{ __('Password Min Length') }}" wire:model="password_min_length" type="number"/>
                                    <x-input label="{{ __('Max Login Attempts') }}" wire:model="max_login_attempts" type="number"/>
                                    <x-input label="{{ __('Lockout Duration (minutes)') }}" wire:model="lockout_duration" type="number"/>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex items-center gap-3 p-4 bg-slate-800 dark:bg-slate-800 rounded-lg">
                                        <x-toggle wire:model.live="force_https"/>
                                        <div>
                                            <p class="font-medium text-sm">{{ __('Force HTTPS') }}</p>
                                            <p class="text-xs text-slate-300">{{ __('Redirect to HTTPS') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 p-4 bg-slate-800 dark:bg-slate-800 rounded-lg">
                                        <x-toggle wire:model.live="csrf_protection"/>
                                        <div>
                                            <p class="font-medium text-sm">{{ __('CSRF Protection') }}</p>
                                            <p class="text-xs text-slate-300">{{ __('Enable CSRF tokens') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 p-4 bg-slate-800 dark:bg-slate-800 rounded-lg">
                                        <x-toggle wire:model.live="password_require_special"/>
                                        <div>
                                            <p class="font-medium text-sm">{{ __('Require Special Characters') }}</p>
                                            <p class="text-xs text-slate-300">{{ __('In passwords') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 p-4 bg-slate-800 dark:bg-slate-800 rounded-lg">
                                        <x-toggle wire:model.live="password_require_number"/>
                                        <div>
                                            <p class="font-medium text-sm">{{ __('Require Numbers') }}</p>
                                            <p class="text-xs text-slate-300">{{ __('In passwords') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 p-4 bg-slate-800 dark:bg-slate-800 rounded-lg">
                                        <x-toggle wire:model.live="password_require_uppercase"/>
                                        <div>
                                            <p class="font-medium text-sm">{{ __('Require Uppercase') }}</p>
                                            <p class="text-xs text-slate-300">{{ __('In passwords') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <x-button wire:click="saveSecurity" color="primary" icon="check">{{ __('Save Security Settings') }}</x-button>
                            </div>
                        </div>
                    @endif

                    {{-- API Settings --}}
                    @if($activeTab === 'api')
                        <div>
                            <h3 class="text-xl font-semibold mb-4">{{ __('API Settings') }}</h3>
                            <form wire:submit="saveApi" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex items-center gap-3 p-4 bg-slate-800 dark:bg-slate-800 rounded-lg">
                                        <x-toggle wire:model.live="api_enabled"/>
                                        <div>
                                            <p class="font-medium text-sm">{{ __('Enable API') }}</p>
                                            <p class="text-xs text-slate-300">{{ __('Allow API access') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 p-4 bg-slate-800 dark:bg-slate-800 rounded-lg">
                                        <x-toggle wire:model.live="api_key_required"/>
                                        <div>
                                            <p class="font-medium text-sm">{{ __('Require API Key') }}</p>
                                            <p class="text-xs text-slate-300">{{ __('Authentication required') }}</p>
                                        </div>
                                    </div>
                                    <x-input label="{{ __('Rate Limit (per minute)') }}" wire:model="api_rate_limit" type="number"/>
                                    <x-input label="{{ __('API Version') }}" wire:model="api_version"/>
                                </div>
                                <x-button type="submit" color="primary" icon="check">{{ __('Save API Settings') }}</x-button>
                            </form>
                        </div>
                    @endif

                    {{-- Notifications --}}
                    @if($activeTab === 'notifications')
                        <div>
                            <h3 class="text-xl font-semibold mb-4">{{ __('Notification Settings') }}</h3>
                            <form wire:submit="saveNotifications" class="space-y-4">
                                <h4 class="font-medium">{{ __('Event Notifications') }}</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex items-center gap-3 p-4 bg-slate-800 dark:bg-slate-800 rounded-lg">
                                        <x-toggle wire:model.live="notify_rfq_created"/>
                                        <div>
                                            <p class="font-medium text-sm">{{ __('RFQ Created') }}</p>
                                            <p class="text-xs text-slate-300">{{ __('Notify on new RFQ') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 p-4 bg-slate-800 dark:bg-slate-800 rounded-lg">
                                        <x-toggle wire:model.live="notify_quote_submitted"/>
                                        <div>
                                            <p class="font-medium text-sm">{{ __('Quote Submitted') }}</p>
                                            <p class="text-xs text-slate-300">{{ __('Notify on quote') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 p-4 bg-slate-800 dark:bg-slate-800 rounded-lg">
                                        <x-toggle wire:model.live="notify_order_placed"/>
                                        <div>
                                            <p class="font-medium text-sm">{{ __('Order Placed') }}</p>
                                            <p class="text-xs text-slate-300">{{ __('Notify on order') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 p-4 bg-slate-800 dark:bg-slate-800 rounded-lg">
                                        <x-toggle wire:model.live="notify_sla_breach"/>
                                        <div>
                                            <p class="font-medium text-sm">{{ __('SLA Breach') }}</p>
                                            <p class="text-xs text-slate-300">{{ __('Notify on SLA issues') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <h4 class="font-medium mt-6">{{ __('Notification Channels') }}</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex items-center gap-3 p-4 bg-slate-800 dark:bg-slate-800 rounded-lg">
                                        <x-toggle wire:model.live="notify_via_email"/>
                                        <div>
                                            <p class="font-medium text-sm">{{ __('Email') }}</p>
                                            <p class="text-xs text-slate-300">{{ __('Send via email') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 p-4 bg-slate-800 dark:bg-slate-800 rounded-lg">
                                        <x-toggle wire:model.live="notify_via_sms"/>
                                        <div>
                                            <p class="font-medium text-sm">{{ __('SMS') }}</p>
                                            <p class="text-xs text-slate-300">{{ __('Send via SMS') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 p-4 bg-slate-800 dark:bg-slate-800 rounded-lg">
                                        <x-toggle wire:model.live="notify_via_slack"/>
                                        <div>
                                            <p class="font-medium text-sm">{{ __('Slack') }}</p>
                                            <p class="text-xs text-slate-300">{{ __('Send to Slack') }}</p>
                                        </div>
                                    </div>
                                </div>
                                @if($notify_via_slack)
                                    <x-input label="{{ __('Slack Webhook URL') }}" wire:model="slack_webhook" type="url"/>
                                @endif
                                <x-button type="submit" color="primary" icon="check">{{ __('Save Notification Settings') }}</x-button>
                            </form>
                        </div>
                    @endif

                    {{-- Business Rules --}}
                    @if($activeTab === 'business')
                        <div>
                            <h3 class="text-xl font-semibold mb-4">{{ __('Business Rules') }}</h3>
                            <form wire:submit="saveBusinessRules" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-input label="{{ __('RFQ Default Duration (days)') }}" wire:model="rfq_default_duration" type="number"/>
                                    <x-input label="{{ __('Quote Validity (days)') }}" wire:model="quote_validity_days" type="number"/>
                                    <x-input label="{{ __('Min Order Amount') }}" wire:model="min_order_amount" type="number" step="0.01" prefix="$"/>
                                    <x-input label="{{ __('Max Order Amount') }}" wire:model="max_order_amount" type="number" step="0.01" prefix="$"/>
                                    <x-input label="{{ __('Approval Threshold') }}" wire:model="approval_threshold" type="number" step="0.01" prefix="$"/>
                                    <div class="flex items-center gap-3 p-4 bg-slate-800 dark:bg-slate-800 rounded-lg">
                                        <x-toggle wire:model.live="require_approval"/>
                                        <div>
                                            <p class="font-medium text-sm">{{ __('Require Approval') }}</p>
                                            <p class="text-xs text-slate-300">{{ __('For high-value orders') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <x-button type="submit" color="primary" icon="check">{{ __('Save Business Rules') }}</x-button>
                            </form>
                        </div>
                    @endif

                    {{-- File Upload --}}
                    @if($activeTab === 'files')
                        <div>
                            <h3 class="text-xl font-semibold mb-4">{{ __('File Upload Settings') }}</h3>
                            <form wire:submit="saveFileUpload" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-input label="{{ __('Max Upload Size (KB)') }}" wire:model="max_upload_size" type="number"/>
                                    <x-input label="{{ __('Allowed Extensions') }}" wire:model="allowed_extensions" hint="{{ __('Comma separated') }}"/>
                                    <div class="flex items-center gap-3 p-4 bg-slate-800 dark:bg-slate-800 rounded-lg">
                                        <x-toggle wire:model.live="scan_uploads"/>
                                        <div>
                                            <p class="font-medium text-sm">{{ __('Scan Uploads') }}</p>
                                            <p class="text-xs text-slate-300">{{ __('Virus scanning') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <x-button type="submit" color="primary" icon="check">{{ __('Save File Upload Settings') }}</x-button>
                            </form>
                        </div>
                    @endif

                    {{-- System Info --}}
                    @if($activeTab === 'system')
                        <div>
                            <h3 class="text-xl font-semibold mb-4">{{ __('System Information') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($systemInfo as $key => $value)
                                    <div class="p-4 bg-slate-800 dark:bg-slate-800 rounded-lg">
                                        <p class="text-xs text-slate-300 uppercase tracking-wide mb-1">{{ str_replace('_', ' ', $key) }}</p>
                                        <p class="font-semibold">{{ $value }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6">
                                <h4 class="font-medium mb-3">{{ __('Feature Flags') }}</h4>
                                <livewire:settings.feature-flags/>
                            </div>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</div>
