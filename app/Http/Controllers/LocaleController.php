<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class LocaleController
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        $supported = array_keys(config('languages.supported', []));
        if (! in_array($locale, $supported, true)) {
            $locale = config('languages.fallback', 'en');
        }

        // Persist in session and cookie (30 days)
        $request->session()->put('locale', $locale);
        Cookie::queue('locale', $locale, 60 * 24 * 30);

        $previous = url()->previous();
        // If previous equals current (no referer), send to welcome or dashboard if authenticated
        if ($previous === url()->current()) {
            $previous = auth()->check() ? route('dashboard') : route('welcome');
        }

        return redirect()->to($previous);
    }
}
