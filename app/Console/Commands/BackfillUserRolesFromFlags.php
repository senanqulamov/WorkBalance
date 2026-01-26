<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class BackfillUserRolesFromFlags extends Command
{
    protected $signature = 'users:backfill-roles {--dry-run : Show what would change without writing}';

    protected $description = 'Backfill role_user pivot rows from legacy boolean flags (is_buyer/is_seller/is_supplier/is_admin).';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $roles = Role::query()->whereIn('name', ['admin', 'buyer', 'seller', 'supplier', 'market_worker'])->get()->keyBy('name');

        foreach (['admin', 'buyer', 'seller', 'supplier'] as $required) {
            if (!isset($roles[$required])) {
                $this->error("Missing role '{$required}'. Run RolesAndPermissionsSeeder first.");
                return Command::FAILURE;
            }
        }

        $users = User::query()->with('roles:id,name')->get();

        $updated = 0;

        foreach ($users as $user) {
            $toAttach = [];

            if ($user->is_admin) {
                $toAttach[] = $roles['admin']->id;
            }
            if ($user->is_buyer) {
                $toAttach[] = $roles['buyer']->id;
            }
            if ($user->is_seller) {
                $toAttach[] = $roles['seller']->id;
            }
            if ($user->is_supplier) {
                $toAttach[] = $roles['supplier']->id;
            }

            $toAttach = array_values(array_unique(array_filter($toAttach)));

            if (empty($toAttach)) {
                continue;
            }

            $already = $user->roles->pluck('id')->all();
            $missing = array_values(array_diff($toAttach, $already));

            if (empty($missing)) {
                continue;
            }

            $updated++;

            $this->line("User #{$user->id} {$user->email}: attaching roles [" . implode(',', $missing) . "]");

            if (!$dryRun) {
                $user->roles()->syncWithoutDetaching($missing);
            }
        }

        $this->info($dryRun
            ? "Dry run complete. Users needing updates: {$updated}."
            : "Backfill complete. Users updated: {$updated}."
        );

        return Command::SUCCESS;
    }
}
