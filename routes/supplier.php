<?php

use App\Livewire\Supplier\Dashboard as SupplierDashboard;
use App\Livewire\Supplier\Invitations\Index as InvitationsIndex;
use App\Livewire\Supplier\Quotes\Index as QuotesIndex;
use App\Livewire\Supplier\Quotes\Create as QuotesCreate;
use App\Livewire\Supplier\Quotes\Edit as SupplierQuotesEdit;
use App\Livewire\Supplier\Messages\Index as MessagesIndex;
use App\Livewire\Supplier\Rfq\Index as SupplierRfqIndex;
use App\Livewire\Supplier\Rfq\Show as SupplierRfqShow;
use App\Livewire\Supplier\Rfq\QuoteForm as SupplierRfqQuoteForm;
use App\Livewire\Supplier\Products\Index as SupplierProductsIndex;
use App\Livewire\Supplier\Products\Show as SupplierProductsShow;
use App\Livewire\Supplier\Markets\Index as SupplierMarketsIndex;
use App\Livewire\Supplier\Markets\Show as SupplierMarketsShow;
use App\Livewire\Supplier\Orders\Index as SupplierOrdersIndex;
use App\Livewire\Supplier\Orders\Create as SupplierOrdersCreate;
use App\Livewire\Supplier\Orders\Show as SupplierOrdersShow;
use App\Livewire\Supplier\Orders\Edit as SupplierOrdersEdit;
use App\Livewire\Supplier\Logs\Index as SupplierLogsIndex;
use App\Livewire\Shared\ImportExport;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Supplier Portal Routes
|--------------------------------------------------------------------------
|
| These routes are for suppliers to view RFQ invitations, submit quotes,
| and manage their interactions with buyers.
|
*/

Route::middleware(['auth', 'supplier', 'can:access_supplier_portal'])->prefix('supplier')->name('supplier.')->group(function () {
    // Supplier Dashboard
    Route::get('/dashboard', SupplierDashboard::class)->name('dashboard');

    // Invitations
    Route::get('/invitations', InvitationsIndex::class)->name('invitations.index')->middleware('can:manage_supplier_invitations');

    // Quotes
    Route::get('/quotes', QuotesIndex::class)->name('quotes.index')->middleware('can:view_quotes');
    Route::get('/quotes/create/{invitation}', QuotesCreate::class)->name('quotes.create')->middleware('can:submit_quotes');
    Route::get('/quotes/{quote}/edit', SupplierQuotesEdit::class)->name('quotes.edit')->middleware('can:edit_quotes');

    // Messages
    Route::get('/messages', MessagesIndex::class)->name('messages.index');

    // RFQs (View + Show + Quote submission)
    Route::get('/rfq', SupplierRfqIndex::class)->name('rfq.index')->middleware('can:view_rfqs');
    Route::get('/rfq/{request}', SupplierRfqShow::class)->name('rfq.show')->middleware('can:view_rfqs');
    Route::get('/rfq/{request}/quote', SupplierRfqQuoteForm::class)->name('rfq.quote')->middleware('can:submit_quotes');

    // Products (View-only + Show page)
    Route::get('/products', SupplierProductsIndex::class)->name('products.index')->middleware('can:view_products');
    Route::get('/products/{product}', SupplierProductsShow::class)->name('products.show')->middleware('can:view_products');

    // Markets (View-only + Show page)
    Route::get('/markets', SupplierMarketsIndex::class)->name('markets.index')->middleware('can:view_markets');
    Route::get('/markets/{market}', SupplierMarketsShow::class)->name('markets.show')->middleware('can:view_markets');

    // Orders (View-only - supplier's own orders)
    Route::get('/orders', SupplierOrdersIndex::class)->name('orders.index')->middleware('can:view_orders');
    Route::get('/orders/create', SupplierOrdersCreate::class)->name('orders.create')->middleware('can:create_orders');
    Route::get('/orders/{order}', SupplierOrdersShow::class)->name('orders.show')->middleware('can:view_orders');
    Route::get('/orders/{order}/edit', SupplierOrdersEdit::class)->name('orders.edit')->middleware('can:edit_orders');

    // Logs
    Route::get('/logs', SupplierLogsIndex::class)->name('logs.index')->middleware('can:view_logs');

    // Import/Export
    Route::get('/import-export', ImportExport::class)->name('import-export')->middleware('can:view_dashboard');
});
