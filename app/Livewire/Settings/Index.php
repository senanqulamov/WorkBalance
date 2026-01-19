<?php

namespace App\Livewire\Settings;

use App\Livewire\Traits\Alert;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    use Alert;

    public string $activeTab = 'general';

    // General Settings
    public string $app_name = 'DPanel';
    public string $app_url = 'http://localhost';
    public string $app_timezone = 'UTC';
    public string $app_locale = 'en';
    public string $app_env = 'production';
    public bool $app_debug = false;
    public bool $maintenance_mode = false;

    // Mail Settings
    public string $mail_driver = 'smtp';
    public string $mail_host = 'smtp.mailtrap.io';
    public string $mail_port = '2525';
    public string $mail_username = '';
    public string $mail_password = '';
    public string $mail_encryption = 'tls';
    public string $mail_from_address = 'noreply@dpanel.com';
    public string $mail_from_name = 'DPanel';

    // SAP Integration Settings
    public bool $sap_enabled = false;
    public string $sap_host = '';
    public string $sap_client = '';
    public string $sap_username = '';
    public string $sap_password = '';
    public string $sap_language = 'EN';
    public int $sap_sync_interval = 60;
    public bool $sap_auto_sync = true;

    // Database Settings
    public string $db_connection = 'mysql';
    public string $db_host = '127.0.0.1';
    public string $db_port = '3306';
    public string $db_database = 'dpanel';
    public string $db_username = 'root';
    public string $db_password = '';

    // Cache Settings
    public string $cache_driver = 'redis';
    public int $cache_ttl = 3600;
    public bool $cache_enabled = true;

    // Queue Settings
    public string $queue_driver = 'redis';
    public int $queue_retry_after = 90;
    public int $queue_workers = 3;

    // Security Settings
    public int $session_lifetime = 120;
    public bool $force_https = true;
    public bool $csrf_protection = true;
    public int $password_min_length = 8;
    public bool $password_require_special = true;
    public bool $password_require_number = true;
    public bool $password_require_uppercase = true;
    public int $max_login_attempts = 5;
    public int $lockout_duration = 15;

    // API Settings
    public bool $api_enabled = true;
    public int $api_rate_limit = 60;
    public bool $api_key_required = true;
    public string $api_version = 'v1';

    // Notification Settings
    public bool $notify_rfq_created = true;
    public bool $notify_quote_submitted = true;
    public bool $notify_order_placed = true;
    public bool $notify_sla_breach = true;
    public bool $notify_via_email = true;
    public bool $notify_via_sms = false;
    public bool $notify_via_slack = false;
    public string $slack_webhook = '';

    // Business Rules
    public int $rfq_default_duration = 7;
    public int $quote_validity_days = 30;
    public float $min_order_amount = 0;
    public float $max_order_amount = 1000000;
    public bool $require_approval = true;
    public float $approval_threshold = 10000;

    // File Upload Settings
    public int $max_upload_size = 10240;
    public string $allowed_extensions = 'pdf,doc,docx,xls,xlsx,jpg,png';
    public bool $scan_uploads = true;

    // SAP runtime/status (UI helpers)
    public ?bool $sap_test_status = null;
    public ?string $last_sap_sync = null;

    public function mount(): void
    {
        // Check admin permission
        if (!Auth::user()->is_admin) {
            abort(403, 'Only administrators can access settings.');
        }

        // Load demo values
        $this->loadSettings();
    }

    protected function loadSettings(): void
    {
        $this->app_name = config('app.name', 'DPanel');
        $this->app_url = config('app.url', 'http://localhost');
        $this->app_timezone = config('app.timezone', 'UTC');
        $this->app_locale = config('app.locale', 'en');
        $this->app_env = config('app.env', 'production');
        $this->app_debug = config('app.debug', false);

        // Load cached settings if available
        $cached = Cache::get('system_settings', []);
        foreach ($cached as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function render(): View
    {
        return view('livewire.settings.index', [
            'systemInfo' => $this->getSystemInfo(),
        ]);
    }

    protected function getSystemInfo(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database' => config('database.default'),
            'cache' => config('cache.default'),
            'queue' => config('queue.default'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'environment' => config('app.env'),
        ];
    }

    public function saveGeneral(): void
    {
        $this->checkPermission('edit_settings');

        $this->validate([
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
            'app_timezone' => 'required|string',
            'app_locale' => 'required|string',
            'app_env' => 'required|in:local,development,staging,production',
        ]);

        $this->saveToCache('general');

        // Update .env file
        $this->updateEnvFile([
            'APP_NAME' => $this->app_name,
            'APP_URL' => $this->app_url,
            'APP_TIMEZONE' => $this->app_timezone,
            'APP_LOCALE' => $this->app_locale,
            'APP_ENV' => $this->app_env,
            'APP_DEBUG' => $this->app_debug ? 'true' : 'false',
        ]);

        $this->success(__('General settings saved successfully'));
    }

    public function saveMail(): void
    {
        $this->checkPermission('edit_settings');

        $this->validate([
            'mail_driver' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|numeric',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
            'mail_encryption' => 'required|in:tls,ssl',
        ]);

        $this->saveToCache('mail');

        // Update .env file
        $this->updateEnvFile([
            'MAIL_MAILER' => $this->mail_driver,
            'MAIL_HOST' => $this->mail_host,
            'MAIL_PORT' => $this->mail_port,
            'MAIL_USERNAME' => $this->mail_username,
            'MAIL_PASSWORD' => $this->mail_password,
            'MAIL_ENCRYPTION' => $this->mail_encryption,
            'MAIL_FROM_ADDRESS' => $this->mail_from_address,
            'MAIL_FROM_NAME' => '"' . $this->mail_from_name . '"',
        ]);

        $this->success(__('Mail settings saved successfully'));
    }

    public function saveSap(): void
    {
        $this->checkPermission('edit_settings');

        if ($this->sap_enabled) {
            $this->validate([
                'sap_host' => 'required|string',
                'sap_client' => 'required|string',
                'sap_username' => 'required|string',
                'sap_password' => 'required|string',
                'sap_sync_interval' => 'required|numeric|min:1',
            ]);
        }

        $this->saveToCache('sap');
        $this->success('SAP integration settings saved successfully');
    }

    public function saveDatabase(): void
    {
        $this->checkPermission('edit_settings');

        $this->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
        ]);

        $this->saveToCache('database');
        $this->success('Database settings saved successfully');
    }

    public function saveSecurity(): void
    {
        $this->checkPermission('edit_settings');

        $this->validate([
            'session_lifetime' => 'required|numeric|min:1',
            'password_min_length' => 'required|numeric|min:6|max:32',
            'max_login_attempts' => 'required|numeric|min:1',
            'lockout_duration' => 'required|numeric|min:1',
        ]);

        $this->saveToCache('security');
        $this->success('Security settings saved successfully');
    }

    public function saveApi(): void
    {
        $this->checkPermission('edit_settings');

        $this->validate([
            'api_rate_limit' => 'required|numeric|min:1',
            'api_version' => 'required|string',
        ]);

        $this->saveToCache('api');
        $this->success('API settings saved successfully');
    }

    public function saveNotifications(): void
    {
        $this->checkPermission('edit_settings');

        if ($this->notify_via_slack) {
            $this->validate([
                'slack_webhook' => 'required|url',
            ]);
        }

        $this->saveToCache('notifications');
        $this->success('Notification settings saved successfully');
    }

    public function saveBusinessRules(): void
    {
        $this->checkPermission('edit_settings');

        $this->validate([
            'rfq_default_duration' => 'required|numeric|min:1',
            'quote_validity_days' => 'required|numeric|min:1',
            'min_order_amount' => 'required|numeric|min:0',
            'max_order_amount' => 'required|numeric|min:0',
            'approval_threshold' => 'required|numeric|min:0',
        ]);

        $this->saveToCache('business_rules');
        $this->success('Business rules saved successfully');
    }

    public function saveFileUpload(): void
    {
        $this->checkPermission('edit_settings');

        $this->validate([
            'max_upload_size' => 'required|numeric|min:1',
            'allowed_extensions' => 'required|string',
        ]);

        $this->saveToCache('file_upload');
        $this->success('File upload settings saved successfully');
    }

    protected function saveToCache(string $group): void
    {
        $settings = Cache::get('system_settings', []);

        $properties = get_object_vars($this);
        foreach ($properties as $key => $value) {
            if (!in_array($key, ['activeTab', 'listeners', 'queryString'])) {
                $settings[$key] = $value;
            }
        }

        Cache::put('system_settings', $settings, now()->addYear());

        // Log the change
        DB::table('logs')->insert([
            'user_id' => Auth::id(),
            'type' => 'system',
            'action' => 'settings_updated',
            'message' => "Updated {$group} settings",
            'created_at' => now(),
        ]);
    }

    public function toggleMaintenance(): void
    {
        $this->checkPermission('edit_settings');

        $this->maintenance_mode = !$this->maintenance_mode;
        $this->saveToCache('maintenance');

        $status = $this->maintenance_mode ? 'enabled' : 'disabled';
        $this->success("Maintenance mode {$status}");
    }

    public function testMailConnection(): void
    {
        $this->checkPermission('edit_settings');

        try {
            // Temporarily configure mail settings for testing
            config([
                'mail.mailers.smtp.host' => $this->mail_host,
                'mail.mailers.smtp.port' => $this->mail_port,
                'mail.mailers.smtp.username' => $this->mail_username,
                'mail.mailers.smtp.password' => $this->mail_password,
                'mail.mailers.smtp.encryption' => $this->mail_encryption,
            ]);

            // Try to send a test email (or just verify connection)
            $transport = \Mail::mailer('smtp')->getSymfonyTransport();

            // For now, just validate the configuration
            if (empty($this->mail_host) || empty($this->mail_port)) {
                throw new \Exception(__('Mail host and port are required'));
            }

            $this->success(__('Mail configuration validated successfully!'));
        } catch (\Exception $e) {
            $this->error(__('Mail connection failed: ') . $e->getMessage());
        }
    }

    public function testSapConnection(): void
    {
        $this->checkPermission('edit_settings');

        if (!$this->sap_enabled) {
            $this->error('SAP integration is not enabled');
            return;
        }

        try {
            // Simulate SAP connection test
            $this->sap_test_status = true;
            $this->success('SAP connection test successful! (Demo Mode)');
        } catch (\Exception $e) {
            $this->sap_test_status = false;
            $this->error('SAP connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Clear stored SAP credentials (UI action)
     */
    public function clearSapCredentials(): void
    {
        $this->checkPermission('edit_settings');

        $this->sap_username = '';
        $this->sap_password = '';

        // Persist changes
        $this->saveToCache('sap');

        $this->success('SAP credentials cleared');
    }

    /**
     * Trigger an immediate SAP sync (demo)
     */
    public function runSapSyncNow(): void
    {
        $this->checkPermission('edit_settings');

        if (!$this->sap_enabled) {
            $this->error('SAP integration is not enabled');
            return;
        }

        try {
            // In a real app you'd dispatch a job here; for demo just set last sync
            $this->last_sap_sync = now()->toDateTimeString();
            $this->success('SAP sync started (demo)');
        } catch (\Exception $e) {
            $this->error('SAP sync failed: ' . $e->getMessage());
        }
    }

    public function testDatabaseConnection(): void
    {
        $this->checkPermission('edit_settings');

        try {
            DB::connection()->getPdo();
            $this->success('Database connection successful!');
        } catch (\Exception $e) {
            $this->error('Database connection failed: ' . $e->getMessage());
        }
    }

    public function clearCache(): void
    {
        $this->checkPermission('edit_settings');

        Cache::flush();
        $this->success('Cache cleared successfully!');
    }

    public function clearLogs(): void
    {
        $this->checkPermission('edit_settings');

        DB::table('logs')->truncate();
        $this->success('Logs cleared successfully!');
    }

    protected function checkPermission(string $permission): void
    {
        if (!Auth::user()->hasPermission($permission)) {
            $this->error('You do not have permission to perform this action.');
            throw new \Exception('Unauthorized');
        }
    }
}
