<?php

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->beforeEach(function() {
        // Ensure roles and permissions exist for factories and auth gates
        \Artisan::call('db:seed', [
            '--class' => Database\Seeders\RolesAndPermissionsSeeder::class,
            '--force' => true,
        ]);
    })
    ->in('Feature');

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

function something()
{
    // ..
}
