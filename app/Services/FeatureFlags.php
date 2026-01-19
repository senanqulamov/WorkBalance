<?php

namespace App\Services;

use App\Models\FeatureFlag;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class FeatureFlags
{
    public static function enabled(string $key): bool
    {
        $flag = Cache::remember("feature_flag_{$key}", 60, fn () => FeatureFlag::where('key', $key)->first());
        if (!$flag) {
            return false;
        }
        if (!$flag->enabled) {
            return false;
        }
        // Audience scoping (basic)
        $aud = $flag->audience ?? [];
        if (empty($aud)) {
            return true;
        }
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        if (!empty($aud['roles'])) {
            foreach ($aud['roles'] as $role) {
                if ($user->hasRole($role)) {
                    return true;
                }
            }
            return false;
        }
        return true;
    }
}
