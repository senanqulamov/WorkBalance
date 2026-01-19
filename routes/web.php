<?php

use App\Http\Controllers\LocaleController;
use App\Livewire\Buyer\Dashboard as BuyerDashboard;
use App\Livewire\Buyer\Logs\Index as BuyerLogsIndex;
use App\Livewire\Buyer\Markets\Index as BuyerMarketsIndex;
use App\Livewire\Buyer\Products\Index as BuyerProductsIndex;
use App\Livewire\Buyer\Rfq\Index as BuyerRfqIndex;
use App\Livewire\Buyer\Rfq\Show as BuyerRfqShow;
use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\Logs\Index as LogsIndex;
use App\Livewire\Markets\Index as MarketsIndex;
use App\Livewire\Markets\Show as MarketShow;
use App\Livewire\Orders\Index as OrdersIndex;
use App\Livewire\Orders\Show as OrderShow;
use App\Livewire\Products\Index as ProductsIndex;
use App\Livewire\Products\Show as ProductShow;
use App\Livewire\Seller\Dashboard as SellerDashboard;
use App\Livewire\Seller\Markets\Index as SellerMarketsIndex;
use App\Livewire\Seller\Markets\Show as SellerMarketShow;
use App\Livewire\Seller\Products\Index as SellerProductsIndex;
use App\Livewire\Seller\Products\Show as SellerProductShow;
use App\Livewire\Seller\Orders\Index as SellerOrdersIndex;
use App\Livewire\Seller\Orders\Show as SellerOrderShow;
use App\Livewire\Seller\Logs\Index as SellerLogsIndex;
use App\Livewire\Settings\Index as SettingsIndex;
use App\Livewire\User\Profile;
use App\Livewire\Users\Index;
use App\Livewire\Users\Show as UsersShow;
use App\Livewire\Rfq\Create as RfqCreate;
use App\Livewire\Rfq\Index as RfqIndex;
use App\Livewire\Rfq\Show as RfqShow;
use App\Livewire\Rfq\QuoteForm as RfqQuoteForm;
use App\Livewire\Privacy\Index as PrivacyIndex;
use App\Livewire\Privacy\Users\Show as PrivacyUserShow;
use App\Livewire\Privacy\Roles\Show as PrivacyRoleShow;
use App\Livewire\Shared\ImportExport;
use App\Http\Controllers\DownloadController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Health\Index as HealthIndex;
use App\Livewire\Notifications\Index as NotificationsIndex;
use App\Livewire\Monitoring\Rfq\Index as MonitoringRfqIndex;
use App\Livewire\Monitoring\Rfq\Create as MonitoringRfqCreate;
use App\Livewire\Monitoring\Rfq\Show as MonitoringRfqShow;
use App\Livewire\Sla\Index as SlaIndex;
use App\Livewire\Seller\Workers\Index as SellerWorkersIndex;

Route::view('/', 'welcome')->name('welcome');

Route::get('/lang/{locale}', [LocaleController::class, 'switch'])->name('lang.switch');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardIndex::class)->name('dashboard')->middleware('can:view_dashboard');

    // Role-based Dashboards
    Route::get('/buyer/dashboard', BuyerDashboard::class)->name('buyer.dashboard')->middleware('can:view_dashboard');

    // Buyer Panel Routes
    Route::get('/buyer/rfq', BuyerRfqIndex::class)->name('buyer.rfq.index')->middleware('can:view_rfqs');
    Route::get('/buyer/rfq/{request}', BuyerRfqShow::class)->name('buyer.rfq.show')->middleware('can:view_rfqs');
    Route::get('/buyer/products', BuyerProductsIndex::class)->name('buyer.products.index')->middleware('can:view_products');
    Route::get('/buyer/markets', BuyerMarketsIndex::class)->name('buyer.markets.index')->middleware('can:view_markets');
    Route::get('/buyer/logs', BuyerLogsIndex::class)->name('buyer.logs.index')->middleware('can:view_logs');
    Route::get('/buyer/import-export', ImportExport::class)->name('buyer.import-export')->middleware('can:view_dashboard');

    Route::get('/seller/dashboard', SellerDashboard::class)->name('seller.dashboard')->middleware(['seller', 'can:view_dashboard']);
    Route::get('/seller/markets', SellerMarketsIndex::class)->name('seller.markets.index')->middleware(['seller', 'can:view_markets']);
    Route::get('/seller/markets/{market}', SellerMarketShow::class)->name('seller.markets.show')->middleware(['seller', 'can:view_markets']);
    Route::get('/seller/products', SellerProductsIndex::class)->name('seller.products.index')->middleware(['seller', 'can:view_products']);
    Route::get('/seller/products/{product}', SellerProductShow::class)->name('seller.products.show')->middleware(['seller', 'can:view_products']);
    Route::get('/seller/orders', SellerOrdersIndex::class)->name('seller.orders.index')->middleware(['seller', 'can:view_orders']);
    Route::get('/seller/orders/{order}', SellerOrderShow::class)->name('seller.orders.show')->middleware(['seller', 'can:view_orders']);
    Route::get('/seller/logs', SellerLogsIndex::class)->name('seller.logs.index')->middleware(['seller', 'can:view_logs']);

    // Seller Workers
    Route::get('/seller/workers', SellerWorkersIndex::class)->name('seller.workers.index')->middleware(['seller', 'can:view_dashboard']);

    Route::get('/seller/import-export', ImportExport::class)->name('seller.import-export')->middleware(['seller', 'can:view_dashboard']);

    Route::get('/users', Index::class)->name('users.index')->middleware('can:view_users');
    Route::get('/users/{user}', UsersShow::class)->name('users.show')->middleware('can:view_users');
    Route::get('/user/profile', Profile::class)->name('user.profile');

    // Products
    Route::get('/products', ProductsIndex::class)->name('products.index')->middleware('can:view_products');
    Route::get('/products/{product}', ProductShow::class)->name('products.show')->middleware('can:view_products');

    // Orders
    Route::get('/orders', OrdersIndex::class)->name('orders.index')->middleware('can:view_orders');
    Route::get('/orders/{order}', OrderShow::class)->name('orders.show')->middleware('can:view_orders');

    // Markets
    Route::get('/markets', MarketsIndex::class)->name('markets.index')->middleware('can:view_markets');
    Route::get('/markets/{market}', MarketShow::class)->name('markets.show')->middleware('can:view_markets');

    // Logs
    Route::get('/logs', LogsIndex::class)->name('logs.index')->middleware('can:view_logs');

    // Settings
    Route::get('/settings', SettingsIndex::class)->name('settings.index')->middleware('can:view_settings');
    Route::get('/settings/flags', \App\Livewire\Settings\FeatureFlags::class)->name('settings.flags')->middleware('can:manage_feature_flags');

    // Privacy & Roles Management
    Route::get('/privacy', PrivacyIndex::class)->name('privacy.index')->middleware('can:manage_roles');
    Route::get('/privacy/users/{user}', PrivacyUserShow::class)->name('privacy.users.show')->middleware('can:manage_roles');
    Route::get('/privacy/roles/{role}', PrivacyRoleShow::class)->name('privacy.roles.show')->middleware('can:manage_roles');

    // RFQs (buyer-facing)
    Route::get('/rfq', RfqIndex::class)->name('rfq.index')->middleware('can:view_rfqs');
    Route::get('/rfq/create', RfqCreate::class)->name('rfq.create')->middleware('can:create_rfqs');
    Route::get('/rfq/{request}', RfqShow::class)->name('rfq.show')->middleware('can:view_rfqs');
    Route::get('/rfq/{request}/quote', RfqQuoteForm::class)->name('rfq.quote')->middleware('can:submit_quotes');

    // Admin enhancements
    Route::get('/health', HealthIndex::class)->name('health.index')->middleware('can:view_health');
    Route::get('/notifications', NotificationsIndex::class)->name('notifications.index')->middleware('can:view_notifications');
    Route::get('/monitoring/rfq', MonitoringRfqIndex::class)->name('monitoring.rfq.index')->middleware('can:view_monitoring');
    Route::get('/monitoring/rfq/create', MonitoringRfqCreate::class)->name('monitoring.rfq.create')->middleware('can:create_rfqs');
    Route::get('/monitoring/rfq/{request}', MonitoringRfqShow::class)->name('monitoring.rfq.show')->middleware('can:view_rfqs');
    Route::get('/sla', SlaIndex::class)->name('sla.index')->middleware('can:manage_sla');

    // Download routes for import/export
    Route::get('/download/template', [DownloadController::class, 'downloadTemplate'])->name('download.template');
    Route::get('/download/export', [DownloadController::class, 'downloadExport'])->name('download.export');
});

require __DIR__.'/auth.php';
