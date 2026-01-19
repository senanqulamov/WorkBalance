<div class="space-y-6">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-600 via-purple-500 to-indigo-500 text-white shadow-2xl shadow-purple-500/30">
        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>

        <div class="relative p-6">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    @if($selectedAction || $selectedType)
                        <button wire:click="goBack" class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30 hover:bg-white/30 transition">
                            <x-icon name="arrow-left" class="w-5 h-5 text-white" />
                        </button>
                    @endif
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30">
                        <x-icon name="arrow-up-tray" class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight">
                            {{ __('Import / Export') }}
                        </h1>
                        <p class="text-sm text-purple-100 mt-0.5">
                            {{ __('Manage your data efficiently') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 via-transparent to-indigo-500/5 dark:from-purple-500/10 dark:to-indigo-500/10"></div>

        <div class="relative p-6">
            @if(!$selectedAction)
                {{-- Step 1: Select Action (Import or Export) --}}
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ __('What would you like to do?') }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('Choose between importing data or exporting your existing data') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
                    {{-- Import Card --}}
                    <button wire:click="selectAction('import')" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 p-8 border-2 border-blue-200 dark:border-blue-800 hover:border-blue-400 dark:hover:border-blue-600 transition-all hover:shadow-xl">
                        <div class="relative z-10">
                            <div class="w-16 h-16 rounded-2xl bg-blue-600 dark:bg-blue-500 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                <x-icon name="arrow-down-tray" class="w-8 h-8 text-white" />
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                {{ __('Import Data') }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Upload CSV or Excel files to import your data in bulk') }}
                            </p>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-400/0 to-indigo-400/0 group-hover:from-blue-400/10 group-hover:to-indigo-400/10 transition-all"></div>
                    </button>

                    {{-- Export Card --}}
                    <button wire:click="selectAction('export')" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 p-8 border-2 border-green-200 dark:border-green-800 hover:border-green-400 dark:hover:border-green-600 transition-all hover:shadow-xl">
                        <div class="relative z-10">
                            <div class="w-16 h-16 rounded-2xl bg-green-600 dark:bg-green-500 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                <x-icon name="arrow-up-tray" class="w-8 h-8 text-white" />
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                {{ __('Export Data') }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Download your data as CSV or Excel files for backup or analysis') }}
                            </p>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-br from-green-400/0 to-emerald-400/0 group-hover:from-green-400/10 group-hover:to-emerald-400/10 transition-all"></div>
                    </button>
                </div>

            @elseif($selectedAction && !$selectedType)
                {{-- Step 2: Select Type --}}
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ $selectedAction === 'import' ? __('What do you want to import?') : __('What do you want to export?') }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('Select the type of data you want to :action', ['action' => $selectedAction]) }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-w-6xl mx-auto">
                    @foreach($this->availableTypes as $typeKey => $typeInfo)
                        @if(($selectedAction === 'import' && $typeInfo['import']) || ($selectedAction === 'export' && $typeInfo['export']))
                            <button wire:click="selectType('{{ $typeKey }}')" class="group relative overflow-hidden rounded-xl bg-white dark:bg-slate-800 p-6 border-2 border-gray-200 dark:border-gray-700 hover:border-purple-400 dark:hover:border-purple-600 transition-all hover:shadow-lg">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-100 to-indigo-100 dark:from-purple-900/30 dark:to-indigo-900/30 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                        <x-icon name="{{ $typeInfo['icon'] }}" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                                    </div>
                                    <div class="flex-1 text-left">
                                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">
                                            {{ $typeInfo['label'] }}
                                        </h3>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">
                                            {{ $typeInfo['description'] }}
                                        </p>
                                    </div>
                                </div>
                            </button>
                        @endif
                    @endforeach
                </div>

            @elseif($selectedAction && $selectedType)
                {{-- Step 3: Instructions and Form --}}
                @php
                    $instructions = $this->getInstructions();
                @endphp

                <div class="max-w-5xl mx-auto space-y-6">
                    {{-- Instructions Card --}}
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 border-2 border-blue-200 dark:border-blue-800">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-10 h-10 rounded-lg bg-blue-600 dark:bg-blue-500 flex items-center justify-center flex-shrink-0">
                                <x-icon name="information-circle" class="w-6 h-6 text-white" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                                    {{ $instructions['title'] ?? '' }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $instructions['description'] ?? '' }}
                                </p>
                            </div>
                        </div>

                        @if($selectedAction === 'import' && isset($instructions['steps']))
                            <div class="mb-4">
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-3">{{ __('Steps') }}:</h4>
                                <ol class="space-y-2">
                                    @foreach($instructions['steps'] as $index => $step)
                                        <li class="flex items-start gap-3">
                                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold">
                                                {{ $index + 1 }}
                                            </span>
                                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $step }}</span>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        @endif

                        @if($selectedAction === 'export' && isset($instructions['info']))
                            <div class="mt-3 p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                <p class="text-sm text-blue-800 dark:text-blue-300">
                                    <x-icon name="information-circle" class="w-4 h-4 inline mr-1" />
                                    {{ $instructions['info'] }}
                                </p>
                            </div>
                        @endif
                    </div>

                    @if($selectedAction === 'import')
                        {{-- Import-specific content --}}

                        {{-- Fields Documentation --}}
                        @if(isset($instructions['fields']))
                            <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                                <h4 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <x-icon name="table-cells" class="w-5 h-5 text-purple-600" />
                                    {{ __('Required Fields') }}
                                </h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-slate-700">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">{{ __('Field Name') }}</th>
                                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">{{ __('Required') }}</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">{{ __('Description') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($instructions['fields'] as $field)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                                                    <td class="px-4 py-3 text-sm font-mono font-semibold text-gray-900 dark:text-white">
                                                        {{ $field['name'] }}
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        @if($field['required'])
                                                            <x-badge text="{{ __('Yes') }}" color="red" sm />
                                                        @else
                                                            <x-badge text="{{ __('No') }}" color="gray" sm />
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                                        {{ $field['description'] }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- Example Data --}}
                        @if(isset($instructions['example']))
                            <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                                <h4 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <x-icon name="document-text" class="w-5 h-5 text-green-600" />
                                    {{ __('Example Data') }}
                                </h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                                        <thead class="bg-gray-50 dark:bg-slate-700">
                                            <tr>
                                                @foreach(array_keys($instructions['example'][0]) as $header)
                                                    <th class="px-3 py-2 text-left font-mono font-bold text-gray-700 dark:text-gray-300">
                                                        {{ $header }}
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($instructions['example'] as $row)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                                                    @foreach($row as $value)
                                                        <td class="px-3 py-2 font-mono text-gray-600 dark:text-gray-400">
                                                            {{ $value }}
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- Download Template Button --}}
                        <div class="flex justify-center">
                            <x-button
                                wire:click="downloadTemplate"
                                color="blue"
                                icon="arrow-down-tray"
                                lg
                            >
                                {{ __('Download Template File') }}
                            </x-button>
                        </div>

                        {{-- Upload Form --}}
                        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border-2 border-dashed border-gray-300 dark:border-gray-700">
                            <h4 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <x-icon name="cloud-arrow-up" class="w-5 h-5 text-purple-600" />
                                {{ __('Upload Your File') }}
                            </h4>

                            <div class="space-y-4">
                                <x-input
                                    type="file"
                                    wire:model="uploadedFile"
                                    accept=".csv"
                                    label="{{ __('Select File') }}"
                                    hint="{{ __('Supported formats: CSV - Max size: 10MB') }}"
                                />

                                @error('uploadedFile')
                                    <div class="text-sm text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </div>
                                @enderror

                                @if($uploadedFile)
                                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                        <div class="flex items-center gap-3">
                                            <x-icon name="document-check" class="w-6 h-6 text-green-600 dark:text-green-400" />
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-green-800 dark:text-green-300">
                                                    {{ __('File ready for import') }}
                                                </p>
                                                <p class="text-xs text-green-600 dark:text-green-400">
                                                    {{ $uploadedFile->getClientOriginalName() }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex justify-end gap-3">
                                        <x-button
                                            wire:click="$set('uploadedFile', null)"
                                            color="slate"
                                            icon="x-mark"
                                        >
                                            {{ __('Cancel') }}
                                        </x-button>
                                        <x-button
                                            wire:click="processImport"
                                            color="green"
                                            icon="check"
                                        >
                                            {{ __('Start Import') }}
                                        </x-button>
                                    </div>
                                @endif
                            </div>
                        </div>

                    @else
                        {{-- Export Form --}}
                        @php
                            $exportFilters = $this->getExportFilters();
                        @endphp

                        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                            <h4 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <x-icon name="adjustments-horizontal" class="w-5 h-5 text-green-600" />
                                {{ __('Export Options') }}
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                @if($exportFilters['date'] ?? false)
                                    <x-date
                                        wire:model="exportDateFrom"
                                        label="{{ __('From Date') }}"
                                    />
                                    <x-date
                                        wire:model="exportDateTo"
                                        label="{{ __('To Date') }}"
                                    />
                                @endif

                                @if($exportFilters['category'] ?? false)
                                    <x-select.styled
                                        wire:model="exportCategory"
                                        label="{{ __('Category Filter') }}"
                                        :options="collect($exportFilters['categories'] ?? [])->map(fn($cat) => ['value' => $cat, 'label' => $cat])->prepend(['value' => '', 'label' => __('All Categories')])->toArray()"
                                        select="label:label|value:value"
                                    />
                                @endif

                                @if($exportFilters['status'] ?? false)
                                    <x-select.styled
                                        wire:model="exportStatus"
                                        label="{{ __('Status Filter') }}"
                                        :options="$exportFilters['statuses'] ?? []"
                                        select="label:label|value:value"
                                    />
                                @endif
                            </div>

                            <div class="flex justify-end">
                                <x-button
                                    wire:click="processExport"
                                    color="green"
                                    icon="arrow-down-tray"
                                    lg
                                >
                                    {{ __('Export Now') }}
                                </x-button>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @script
    <script>
        $wire.on('download-file', (event) => {
            window.location.href = event.url;
        });
    </script>
    @endscript
</div>
