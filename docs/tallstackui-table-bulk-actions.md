# TallStackUI Table - Custom Bulk Actions Guide

## Overview

TallStackUI's `x-table` component provides built-in support for row selection through the `selectable` attribute. This guide demonstrates how to build custom bulk actions on top of this functionality.

## Table of Contents

1. [Basic Setup](#basic-setup)
2. [Understanding Selection State](#understanding-selection-state)
3. [Building Custom Bulk Actions](#building-custom-bulk-actions)
4. [Complete Example](#complete-example)
5. [Advanced Patterns](#advanced-patterns)

---

## Basic Setup

### Enable Selectable Rows

To enable row selection in your table, add the `selectable` attribute and specify which property to use as the identifier:

```blade
<x-table 
    :$headers 
    :$rows 
    selectable
    selectable-property="id"
    wire:model="selected"
>
    <!-- Your table columns here -->
</x-table>
```

### Component Setup

In your Livewire component, define the `$selected` property to store selected row IDs:

```php
use Livewire\Component;

class UsersIndex extends Component
{
    public array $selected = [];
    
    public $headers = [
        ['label' => 'Name', 'index' => 'name'],
        ['label' => 'Email', 'index' => 'email'],
        // ... more headers
    ];
    
    public function render()
    {
        return view('livewire.users.index');
    }
}
```

---

## Understanding Selection State

### How TallStackUI Manages Selection

The TallStackUI table uses Alpine.js to manage selection state:

- `x-data="tallstackui_table(...)"` - Initializes the table component
- Selected items are synced to your Livewire property via `wire:model`
- The checkbox in the header allows "select all" functionality
- Individual row checkboxes are automatically wired up

### Accessing Selected Items

You can access selected items in multiple ways:

**1. In the Livewire component:**
```php
public function bulkDelete()
{
    // $this->selected contains array of IDs
    User::whereIn('id', $this->selected)->delete();
    $this->selected = []; // Clear selection
}
```

**2. In the Blade template with Alpine.js:**
```blade
<div x-data="{ 
    get selectedCount() { 
        return @entangle('selected').length 
    } 
}">
    <span x-show="selectedCount > 0" x-text="selectedCount + ' selected'"></span>
</div>
```

---

## Building Custom Bulk Actions

### Method 1: Action Bar Above Table

Display an action bar when items are selected:

```blade
<div>
    <!-- Bulk Actions Bar -->
    @if(count($selected) > 0)
        <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-between">
            <div class="flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5 text-blue-600" />
                <span class="font-medium">{{ count($selected) }} item(s) selected</span>
            </div>
            
            <div class="flex gap-2">
                <x-button 
                    wire:click="bulkExport" 
                    color="secondary" 
                    icon="arrow-down-tray"
                >
                    Export
                </x-button>
                
                <x-button 
                    wire:click="bulkDelete" 
                    color="red" 
                    icon="trash"
                >
                    Delete
                </x-button>
                
                <x-button.circle 
                    wire:click="clearSelection" 
                    color="slate" 
                    icon="x-mark"
                />
            </div>
        </div>
    @endif

    <!-- Table -->
    <x-table 
        :$headers 
        :$rows 
        selectable
        selectable-property="id"
        wire:model="selected"
    >
        <!-- Table content -->
    </x-table>
</div>
```

### Method 2: Dropdown Actions Menu

Use a dropdown for bulk actions:

```blade
@if(count($selected) > 0)
    <div class="mb-4">
        <x-dropdown>
            <x-slot:action>
                <x-button color="secondary">
                    Actions ({{ count($selected) }})
                    <x-icon name="chevron-down" class="ml-2 w-4 h-4" />
                </x-button>
            </x-slot:action>
            
            <x-dropdown.items>
                <x-dropdown.items.item 
                    wire:click="bulkApprove" 
                    icon="check-circle"
                    text="Approve Selected"
                />
                <x-dropdown.items.item 
                    wire:click="bulkReject" 
                    icon="x-circle"
                    text="Reject Selected"
                />
                <x-dropdown.items.item separator />
                <x-dropdown.items.item 
                    wire:click="bulkExport" 
                    icon="arrow-down-tray"
                    text="Export to CSV"
                />
                <x-dropdown.items.item 
                    wire:click="bulkDelete" 
                    icon="trash"
                    text="Delete Selected"
                    class="text-red-600"
                />
            </x-dropdown.items>
        </x-dropdown>
    </div>
@endif
```

### Method 3: Inline Actions in Header Slot

Place bulk actions directly in the table header:

```blade
<x-table 
    :$headers 
    :$rows 
    selectable
    selectable-property="id"
    wire:model="selected"
>
    <x-slot:header>
        <div class="flex items-center justify-between py-4">
            <h2 class="text-lg font-semibold">Users</h2>
            
            @if(count($selected) > 0)
                <div class="flex gap-2 items-center">
                    <x-badge color="blue" text="{{ count($selected) }} selected" />
                    <x-button wire:click="bulkEmail" sm icon="envelope">Email</x-button>
                    <x-button wire:click="bulkDelete" sm color="red" icon="trash">Delete</x-button>
                </div>
            @endif
        </div>
    </x-slot:header>
    
    <!-- Table columns -->
</x-table>
```

---

## Complete Example

Here's a full working example with a Livewire component and view:

### Livewire Component

```php
<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class Index extends Component
{
    use WithPagination;
    
    public array $selected = [];
    
    public array $headers = [
        ['label' => 'Name', 'index' => 'name'],
        ['label' => 'Email', 'index' => 'email'],
        ['label' => 'Role', 'index' => 'role'],
        ['label' => 'Created', 'index' => 'created_at'],
    ];
    
    public array $sort = [
        'column' => 'created_at',
        'direction' => 'desc',
    ];
    
    #[Computed]
    public function rows()
    {
        return User::query()
            ->orderBy($this->sort['column'], $this->sort['direction'])
            ->paginate(10);
    }
    
    public function bulkDelete()
    {
        if (empty($this->selected)) {
            return;
        }
        
        User::whereIn('id', $this->selected)->delete();
        
        $this->selected = [];
        
        session()->flash('success', 'Selected users deleted successfully!');
    }
    
    public function bulkExport()
    {
        if (empty($this->selected)) {
            return;
        }
        
        $users = User::whereIn('id', $this->selected)->get();
        
        // Export logic here (CSV, Excel, etc.)
        
        session()->flash('success', 'Export started!');
    }
    
    public function bulkActivate()
    {
        User::whereIn('id', $this->selected)
            ->update(['status' => 'active']);
        
        $this->selected = [];
        
        session()->flash('success', 'Selected users activated!');
    }
    
    public function bulkDeactivate()
    {
        User::whereIn('id', $this->selected)
            ->update(['status' => 'inactive']);
        
        $this->selected = [];
        
        session()->flash('success', 'Selected users deactivated!');
    }
    
    public function clearSelection()
    {
        $this->selected = [];
    }
    
    public function render()
    {
        return view('livewire.users.index');
    }
}
```

### Blade View

```blade
<div>
    <x-card>
        <x-heading-title title="Users" icon="user-group" padding="p-5" />

        <!-- Bulk Actions Bar -->
        @if(count($selected) > 0)
            <div class="mx-5 mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <x-icon name="check-circle" class="w-5 h-5 text-blue-600" />
                        <span class="font-medium text-blue-900 dark:text-blue-100">
                            {{ count($selected) }} user(s) selected
                        </span>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        <x-button 
                            wire:click="bulkActivate" 
                            color="green" 
                            icon="check-circle"
                            sm
                        >
                            Activate
                        </x-button>
                        
                        <x-button 
                            wire:click="bulkDeactivate" 
                            color="yellow" 
                            icon="pause-circle"
                            sm
                        >
                            Deactivate
                        </x-button>
                        
                        <x-button 
                            wire:click="bulkExport" 
                            color="secondary" 
                            icon="arrow-down-tray"
                            sm
                        >
                            Export
                        </x-button>
                        
                        <x-button 
                            wire:click="bulkDelete" 
                            color="red" 
                            icon="trash"
                            sm
                        >
                            Delete
                        </x-button>
                        
                        <x-button.circle 
                            wire:click="clearSelection" 
                            color="slate" 
                            icon="x-mark"
                            sm
                            title="Clear selection"
                        />
                    </div>
                </div>
            </div>
        @endif

        <!-- Table -->
        <x-table 
            :$headers 
            :$sort 
            :rows="$this->rows" 
            paginate 
            filter 
            loading
            selectable
            selectable-property="id"
            wire:model="selected"
        >
            @interact('column_name', $row)
                <div class="font-medium">{{ $row->name }}</div>
            @endinteract

            @interact('column_email', $row)
                {{ $row->email }}
            @endinteract

            @interact('column_role', $row)
                <x-badge :text="$row->role" color="blue" />
            @endinteract

            @interact('column_created_at', $row)
                {{ $row->created_at->diffForHumans() }}
            @endinteract
        </x-table>
    </x-card>
</div>
```

---

## Advanced Patterns

### 1. Confirmation Before Bulk Actions

Add confirmation dialogs for destructive actions:

```php
public function bulkDelete()
{
    $this->dialog()
        ->question('Delete Users', 'Are you sure you want to delete ' . count($this->selected) . ' users?')
        ->confirm('Delete', 'confirmBulkDelete')
        ->cancel('Cancel')
        ->send();
}

public function confirmBulkDelete()
{
    User::whereIn('id', $this->selected)->delete();
    $this->selected = [];
    
    $this->toast()
        ->success('Success', 'Users deleted successfully')
        ->send();
}
```

### 2. Progress Indicator for Bulk Actions

For long-running bulk operations:

```php
use Livewire\Attributes\Locked;

#[Locked]
public $bulkProgress = 0;

public function bulkProcess()
{
    $total = count($this->selected);
    
    foreach ($this->selected as $index => $id) {
        // Process item
        User::find($id)->process();
        
        // Update progress
        $this->bulkProgress = round(($index + 1) / $total * 100);
    }
    
    $this->bulkProgress = 0;
    $this->selected = [];
}
```

In your view:

```blade
@if($bulkProgress > 0)
    <div class="mb-4">
        <x-progress :percent="$bulkProgress" color="blue" />
        <p class="text-sm text-gray-600 mt-2">Processing... {{ $bulkProgress }}%</p>
    </div>
@endif
```

### 3. Selective Bulk Actions Based on State

Show different actions based on what's selected:

```php
#[Computed]
public function selectedUsers()
{
    return User::whereIn('id', $this->selected)->get();
}

#[Computed]
public function canActivate()
{
    return $this->selectedUsers()->where('status', '!=', 'active')->isNotEmpty();
}

#[Computed]
public function canDeactivate()
{
    return $this->selectedUsers()->where('status', 'active')->isNotEmpty();
}
```

In your view:

```blade
@if($this->canActivate)
    <x-button wire:click="bulkActivate">Activate</x-button>
@endif

@if($this->canDeactivate)
    <x-button wire:click="bulkDeactivate">Deactivate</x-button>
@endif
```

### 4. Select All Across Pages

To select all items across all pages (not just current page):

```php
public bool $selectAll = false;

public function updatedSelectAll($value)
{
    if ($value) {
        $this->selected = User::pluck('id')->toArray();
    } else {
        $this->selected = [];
    }
}
```

In your view:

```blade
@if(count($selected) > 0 && count($selected) < $this->rows->total())
    <div class="mb-4 p-3 bg-gray-100 dark:bg-gray-800 rounded text-center">
        <span>{{ count($selected) }} users selected on this page. </span>
        <button 
            wire:click="$set('selectAll', true)" 
            class="text-blue-600 hover:underline font-medium"
        >
            Select all {{ $this->rows->total() }} users
        </button>
    </div>
@endif

@if($selectAll)
    <div class="mb-4 p-3 bg-blue-100 dark:bg-blue-900 rounded text-center">
        <span>All {{ $this->rows->total() }} users are selected. </span>
        <button 
            wire:click="$set('selectAll', false)" 
            class="text-blue-600 hover:underline font-medium"
        >
            Clear selection
        </button>
    </div>
@endif
```

### 5. Bulk Actions with Real-time Updates

Use events to update the table after bulk actions:

```php
use Livewire\Attributes\On;

public function bulkDelete()
{
    User::whereIn('id', $this->selected)->delete();
    $this->selected = [];
    
    $this->dispatch('users-updated');
}

#[On('users-updated')]
public function refresh()
{
    $this->resetPage();
}
```

### 6. Export Selected Items

Export selected rows to CSV:

```php
public function bulkExportCsv()
{
    $users = User::whereIn('id', $this->selected)
        ->get(['name', 'email', 'created_at']);
    
    $filename = 'users_export_' . now()->format('Y-m-d_His') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ];
    
    $callback = function() use ($users) {
        $file = fopen('php://output', 'w');
        
        // Header row
        fputcsv($file, ['Name', 'Email', 'Created At']);
        
        // Data rows
        foreach ($users as $user) {
            fputcsv($file, [
                $user->name,
                $user->email,
                $user->created_at->format('Y-m-d H:i:s'),
            ]);
        }
        
        fclose($file);
    };
    
    return response()->stream($callback, 200, $headers);
}
```

---

## Best Practices

1. **Clear Selection After Actions**: Always reset `$this->selected = []` after bulk operations
2. **Use Confirmation**: Add confirmation dialogs for destructive actions
3. **Provide Feedback**: Use toast notifications to confirm successful actions
4. **Handle Errors**: Wrap bulk operations in try-catch blocks
5. **Optimize Queries**: Use `whereIn()` for bulk operations instead of loops
6. **Check Permissions**: Verify user has permission to perform bulk actions
7. **Limit Selection**: Consider adding a maximum selection limit for performance
8. **Loading States**: Show loading indicators during bulk operations

---

## Summary

TallStackUI's table component provides a solid foundation for building custom bulk actions. By combining:

- The `selectable` attribute on the table
- The `wire:model` binding for selection state
- Custom action buttons or dropdowns
- Livewire methods for processing bulk operations

You can create powerful and user-friendly bulk action interfaces tailored to your application's needs.

For more information, visit the [TallStackUI Documentation](https://tallstackui.com/docs/table).
