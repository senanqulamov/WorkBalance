# Custom Error Pages Documentation

This directory contains modern, styled error pages for the DPanel application. All pages follow a consistent 2025 modern design aesthetic with dark theme support, smooth animations, and responsive layouts.

## Available Error Pages

### 401 - Unauthorized
**File:** `401.blade.php`
- **Color Scheme:** Purple/Pink gradient
- **Icon:** User profile icon
- **Animation:** Floating animation
- **Use Case:** When a user tries to access a resource without authentication
- **Primary Action:** Login button

### 403 - Forbidden/Access Denied
**File:** `403.blade.php`
- **Color Scheme:** Red/Orange gradient
- **Icon:** Lock icon
- **Animation:** Floating animation
- **Use Case:** When a user lacks permission to access a resource (e.g., buyers trying to quote on their own RFQs)
- **Primary Actions:** Go Back, Go to Dashboard

### 404 - Page Not Found
**File:** `404.blade.php`
- **Color Scheme:** Blue/Cyan gradient
- **Icon:** Confused face icon
- **Animation:** Floating with rotation
- **Use Case:** When a requested page doesn't exist
- **Features:** Includes search box for quick navigation
- **Primary Actions:** Go Back, Go to Dashboard

### 419 - Session Expired
**File:** `419.blade.php`
- **Color Scheme:** Indigo/Purple gradient
- **Icon:** Clock icon
- **Animation:** Slow spin
- **Use Case:** When CSRF token expires or session times out
- **Features:** Security explanation info box
- **Primary Actions:** Refresh Page, Login Again

### 429 - Too Many Requests
**File:** `429.blade.php`
- **Color Scheme:** Amber/Yellow gradient
- **Icon:** Lightning bolt icon
- **Animation:** Shake animation
- **Use Case:** When rate limiting is triggered
- **Features:** Rate limit info, retry timer, tips for reducing requests
- **Primary Actions:** Go Back, Wait & Retry

### 500 - Internal Server Error
**File:** `500.blade.php`
- **Color Scheme:** Yellow/Orange gradient
- **Icon:** Warning triangle icon
- **Animation:** Shake animation
- **Use Case:** When server encounters an unexpected error
- **Features:** Status information cards, debug info (when APP_DEBUG=true)
- **Primary Actions:** Try Again, Go to Dashboard

### 503 - Service Unavailable
**File:** `503.blade.php`
- **Color Scheme:** Orange/Red gradient
- **Icon:** Settings gear icon
- **Animation:** Slow bounce
- **Use Case:** During maintenance mode
- **Features:** Progress indicator, maintenance status cards
- **Primary Action:** Refresh Page

### Generic Error Layout
**File:** `layout.blade.php`
- **Color Scheme:** Gray gradient
- **Use Case:** Fallback for any other HTTP error codes
- **Features:** Dynamic error code and message display

## Design Features

### Modern 2025 Aesthetic
- **Dark Theme:** Uses the application's dark color palette
  - Background: `#0E1116` (dark-bg)
  - Surface: `#1A1F27` (dark-surface)
  - Border: `#2A2F37` (dark-border)
  - Text: `#E5E7EB` (dark-text)
  - Muted: `#9CA3AF` (dark-muted)

### Animations
- **Float:** Smooth up-down floating motion (3-4s)
- **Pulse Glow:** Breathing glow effect for backgrounds (2s)
- **Shake:** Attention-grabbing shake (0.5s)
- **Spin:** Slow rotation for loading states (8s)
- **Bounce:** Gentle bounce motion (2s)

### Components
1. **Large Animated Icon** - Visual representation of the error type
2. **Gradient Error Code** - Bold, colorful error number
3. **Clear Messaging** - User-friendly error descriptions
4. **Action Buttons** - Clear CTAs with hover effects
5. **Additional Context** - Quick links, tips, or status information
6. **Responsive Layout** - Works on all screen sizes

### Interactive Elements
- **Hover Effects:** Smooth transitions on buttons and links
- **Icon Animations:** Icons respond to hover states
- **Button Groups:** Primary and secondary actions clearly distinguished

## Integration

### Laravel Error Handling
These error pages are automatically used by Laravel when:
1. An exception with the corresponding HTTP status code is thrown
2. An `abort()` function is called with the status code
3. The application enters maintenance mode (503)

### Example Usage in Code
```php
// 403 Forbidden
abort(403, 'This RFQ is not open for quotes.');

// 404 Not Found
abort(404);

// 500 Internal Server Error
// Automatically triggered on exceptions
throw new \Exception('Something went wrong');
```

### Configuration
No additional configuration is required. Laravel automatically looks for error views in `resources/views/errors/` directory.

## Customization

### Changing Colors
Each page uses Tailwind CSS classes. To change the color scheme:
1. Find the gradient classes: `from-{color}-500 to-{color}-500`
2. Replace with your desired colors
3. Update the glow effect background: `bg-{color}-500/10`

### Modifying Actions
Common actions to update:
- `route('dashboard')` - Change dashboard route
- `route('login')` - Change login route
- Support email: `support@{{ config('app.domain') }}`

### Adding Quick Links
Each page has a "Quick Links" or "Additional Help" section at the bottom. Add more links by editing the respective blade file.

## Browser Support
- Modern browsers (Chrome, Firefox, Safari, Edge)
- CSS animations fallback gracefully
- Responsive on mobile, tablet, and desktop

## Accessibility
- Semantic HTML structure
- Proper heading hierarchy
- Color contrast meets WCAG standards
- Screen reader friendly
- Keyboard navigation support

## Performance
- Inline CSS for critical animations (no external dependencies)
- Optimized SVG icons
- Uses Vite for asset compilation
- Minimal JavaScript (only for refresh/retry actions)

## Testing
To test error pages locally:
```php
// Add to routes/web.php for testing
Route::get('/test-error/{code}', function ($code) {
    abort($code, 'Test error message');
});
```

Then visit: `/test-error/403`, `/test-error/404`, etc.

## Support
For issues or customization help, contact the development team.

---

**Last Updated:** December 2025
**Version:** 1.0.0
**Compatible with:** Laravel 11+, TallStackUI v4
