<?php

namespace App\Providers;

use App\Listeners\LogAuthenticationEvents;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use TallStackUi\Facades\TallStackUi;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register authentication event listeners
        Event::listen(Login::class, [LogAuthenticationEvents::class, 'handleLogin']);
        Event::listen(Logout::class, [LogAuthenticationEvents::class, 'handleLogout']);
        Event::listen(Registered::class, [LogAuthenticationEvents::class, 'handleRegistered']);
        Event::listen(Failed::class, [LogAuthenticationEvents::class, 'handleFailed']);

        // Register model observers for automatic workflow event tracking
        // Observers automatically fire events and record to workflow_events table
        \App\Models\Request::observe(\App\Observers\RequestObserver::class);
        \App\Models\RequestItem::observe(\App\Observers\RequestItemObserver::class);
        \App\Models\Quote::observe(\App\Observers\QuoteObserver::class);
        \App\Models\QuoteItem::observe(\App\Observers\QuoteItemObserver::class);

        // Notification listeners (keep these - they don't create duplicates)
        Event::listen(\App\Events\RequestStatusChanged::class, \App\Listeners\SendRequestStatusNotification::class);
        Event::listen(\App\Events\SupplierInvited::class, \App\Listeners\SendSupplierInvitationNotification::class);
        Event::listen(\App\Events\QuoteSubmitted::class, \App\Listeners\NotifyBuyerOfQuoteSubmission::class);
        Event::listen(\App\Events\SlaReminderDue::class, \App\Listeners\SendSlaReminderNotification::class);

        TallStackUi::personalize()
            // ==================== SLIDE ====================
            ->slide()
            ->block([
                'wrapper.first' => 'fixed inset-0 bg-[var(--color-backdrop)] backdrop-blur-[var(--backdrop-blur-md)] transform transition-opacity',
                'wrapper.second' => 'fixed inset-0 overflow-hidden',
                'wrapper.third' => 'absolute inset-0 overflow-hidden',
                'wrapper.fourth' => 'pointer-events-none fixed flex max-w-full',
                'wrapper.fifth' => 'flex flex-col bg-[var(--color-surface-raised)] py-6 shadow-[var(--shadow-pop)] dark:bg-[var(--dark-5)]',
                'blur.sm' => 'backdrop-blur-sm',
                'blur.md' => 'backdrop-blur-md',
                'blur.lg' => 'backdrop-blur-lg',
                'blur.xl' => 'backdrop-blur-xl',
                'title.text' => 'whitespace-normal font-medium text-md text-[var(--color-text-high)] dark:text-[var(--dark-11)]',
                'title.close' => 'h-5 w-5 cursor-pointer text-[var(--color-text-muted)] hover:text-[var(--color-text)]',
                'body' => 'soft-scrollbar dark:text-[var(--dark-10)] grow overflow-y-auto rounded-b-xl px-6 py-5 text-[var(--color-text)]',
                'footer' => 'flex border-t border-t-[var(--color-border)] px-4 pt-6 dark:border-t-[var(--dark-7)]',
                'header' => 'px-6',
            ])
            ->and
            // ==================== MODAL ====================
            ->modal()
            ->block([
                'wrapper.first' => 'fixed inset-0 bg-[var(--color-backdrop)] backdrop-blur-[var(--backdrop-blur-md)] transform transition-opacity',
                'wrapper.second' => 'fixed inset-0 z-[var(--z-modal)] w-screen overflow-y-auto',
                'wrapper.third' => 'mx-auto flex min-h-full w-full transform justify-center p-4',
                'wrapper.fourth' => 'dark:bg-[var(--dark-5)] relative flex w-full transform flex-col rounded-xl bg-[var(--color-surface-raised)] text-left shadow-[var(--shadow-pop)] transition-all',
                'positions.top' => 'items-end sm:items-start',
                'positions.center' => 'items-end sm:items-center',
                'blur.sm' => 'backdrop-blur-sm',
                'blur.md' => 'backdrop-blur-md',
                'blur.lg' => 'backdrop-blur-lg',
                'blur.xl' => 'backdrop-blur-xl',
                'title.wrapper' => 'dark:border-b-[var(--dark-7)] flex items-center justify-between border-b border-b-[var(--color-border)] px-4 py-2.5',
                'title.text' => 'text-md text-[var(--color-text-high)] dark:text-[var(--dark-11)] whitespace-normal font-medium',
                'title.close' => 'text-[var(--color-text-muted)] hover:text-[var(--color-text)] h-5 w-5 cursor-pointer',
                'body' => 'dark:text-[var(--dark-10)] grow rounded-b-xl py-5 text-[var(--color-text)] px-4',
                'footer' => 'dark:text-[var(--dark-10)] dark:border-t-[var(--dark-7)] flex justify-end gap-2 rounded-b-xl border-t border-t-[var(--color-border)] p-4 text-[var(--color-text)]',
            ])
            ->and
            // ==================== DIALOG ====================
            ->dialog()
            ->block([
                'background' => 'fixed inset-0 bg-[var(--color-backdrop-strong)] backdrop-blur-[var(--backdrop-blur-sm)] transform transition-opacity',
                'wrapper.first' => 'fixed inset-0 z-[var(--z-modal)] w-screen overflow-y-auto',
                'wrapper.second' => 'flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0',
                'wrapper.third' => 'relative w-full max-w-sm transform overflow-hidden bg-[var(--color-surface-raised)] rounded-xl p-4 text-left shadow-[var(--shadow-pop)] transition-all sm:my-8 dark:bg-[var(--dark-5)]',
                'icon.wrapper' => 'mx-auto flex h-12 w-12 items-center justify-center rounded-full',
                'icon.size' => 'h-8 w-8',
                'text.wrapper' => 'mt-3 text-center sm:mt-5',
                'text.title' => 'text-lg font-semibold leading-6 text-[var(--color-text-high)] dark:text-[var(--dark-11)]',
                'text.description.wrapper' => 'mt-2',
                'text.description.text' => 'text-sm text-[var(--color-text-muted)] dark:text-[var(--dark-9)]',
                'buttons.wrapper' => 'mt-4 space-y-2 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3 sm:space-y-0',
                'buttons.confirm' => 'cursor-pointer group inline-flex w-full items-center justify-center rounded-md px-4 py-2 text-sm font-semibold text-white outline-hidden transition ease-in focus:ring-2 focus:ring-offset-2',
                'buttons.close.wrapper' => 'flex justify-end',
                'buttons.close.icon' => 'h-5 w-5 cursor-pointer text-[var(--color-text-muted)] hover:text-[var(--color-text)]',
            ])
            ->and
            // ==================== TOAST ====================
            ->toast()
            ->block([
                'wrapper.third' => 'dark:bg-[var(--dark-5)] pointer-events-auto w-full max-w-sm overflow-hidden rounded-xl bg-[var(--color-surface-raised)] shadow-[var(--shadow-pop)] ring-1 ring-[var(--color-border)] dark:ring-[var(--dark-7)]',
                'content.text' => 'dark:text-[var(--dark-11)] text-sm font-medium text-[var(--color-text-high)]',
                'content.description' => 'dark:text-[var(--dark-9)] mt-1 text-sm text-[var(--color-text-muted)]',
                'buttons.close.class' => 'inline-flex text-[var(--color-text-muted)] hover:text-[var(--color-text)] dark:text-[var(--dark-8)] dark:hover:text-[var(--dark-10)] focus:outline-hidden focus:ring-0 cursor-pointer',
                'progress.wrapper' => 'dark:bg-[var(--dark-7)] relative h-1 w-full rounded-full bg-[var(--color-border)]',
                'progress.bar' => 'bg-primary-500 dark:bg-[var(--dark-9)] absolute h-full w-24 duration-300 ease-linear',
            ])
            ->and
            // ==================== CARD ====================
            ->card()
            ->block([
                'wrapper.second' => 'dark:bg-[var(--dark-5)] flex w-full flex-col rounded-lg bg-[var(--color-surface-raised)] shadow-md',
                'header.wrapper.base' => 'dark:border-b-[var(--dark-7)] flex items-center justify-between p-4',
                'header.wrapper.border' => 'border-b border-[var(--color-border)]',
                'header.text.color' => 'text-[var(--color-text-high)] dark:text-[var(--dark-11)]',
                'body' => 'text-[var(--color-text)] dark:text-[var(--dark-10)] grow rounded-b-xl px-4 py-5',
                'footer.wrapper' => 'text-[var(--color-text)] dark:text-[var(--dark-10)] dark:border-t-[var(--dark-7)] rounded-lg rounded-t-none border-t border-t-[var(--color-border)] p-4 px-6',
            ])
            ->and
            // ==================== ALERT ====================
            ->alert()
            ->block([
                'wrapper' => 'rounded-lg p-4',
                'text.title' => 'text-lg font-semibold text-[var(--color-text-high)] dark:text-[var(--dark-11)]',
                'text.description' => 'text-sm text-[var(--color-text)] dark:text-[var(--dark-10)]',
                'close.size' => 'w-5 h-5 text-[var(--color-text-muted)] hover:text-[var(--color-text)] dark:text-[var(--dark-8)] dark:hover:text-[var(--dark-10)]',
            ])
            ->and
            // ==================== BANNER ====================
            ->banner()
            ->block([
                'wrapper' => 'relative flex flex-row items-center justify-between px-6 py-2',
                'text' => 'grow text-center text-sm font-medium text-[var(--color-text)] dark:text-[var(--dark-10)]',
                'slot.left' => 'absolute left-0 ml-4 text-sm font-medium text-[var(--color-text-muted)] dark:text-[var(--dark-9)]',
                'close' => 'h-4 w-4 cursor-pointer text-[var(--color-text-muted)] hover:text-[var(--color-text)] dark:text-[var(--dark-8)] dark:hover:text-[var(--dark-10)]',
            ])
            ->and
            // ==================== DROPDOWN ====================
            ->dropdown()
            ->block([
                'floating.default' => 'rounded-md bg-[var(--color-surface-raised)] dark:bg-[var(--dark-5)] shadow-[var(--shadow-pop)] ring-1 ring-[var(--color-border)] dark:ring-[var(--dark-7)] focus:outline-hidden',
                'action.text' => 'text-sm text-[var(--color-text)] dark:text-[var(--dark-10)] font-medium',
                'action.icon' => 'h-5 w-5 cursor-pointer text-[var(--color-text-muted)] dark:text-[var(--dark-8)] transition hover:text-[var(--color-text)] dark:hover:text-[var(--dark-10)]',
            ])
            ->and
            // ==================== TABLE ====================
            ->table()
            ->block([
                'wrapper' => 'overflow-hidden dark:ring-[var(--dark-7)] rounded-lg shadow-md ring-1 ring-[var(--color-border)]',
                'table.wrapper' => 'relative soft-scrollbar overflow-auto max-h-[calc(100vh-400px)]',
                'table.base' => 'dark:divide-[var(--dark-7)] min-w-full divide-y divide-[var(--color-border)]',
                'table.thead.normal' => 'bg-[var(--color-surface)] dark:bg-[var(--dark-4)] sticky top-0 z-10',
                'table.thead.striped' => 'bg-[var(--color-surface-raised)] dark:bg-[var(--dark-5)] sticky top-0 z-10',
                'table.th' => 'dark:text-[var(--dark-11)] px-3 py-3.5 text-left text-sm font-semibold text-[var(--color-text-high)] bg-inherit',
                'table.tbody' => 'dark:bg-[var(--dark-5)] dark:divide-[var(--dark-7)] divide-y divide-[var(--color-border)] bg-[var(--color-surface-raised)]',
                'table.td' => 'dark:text-[var(--dark-10)] whitespace-nowrap px-3 py-4 text-sm text-[var(--color-text)]',
                'table.tr' => 'hover:bg-[var(--color-surface)] dark:hover:bg-[var(--dark-4)] transition-colors',
                'loading.table' => 'cursor-not-allowed select-none opacity-25',
                'loading.icon' => 'text-primary-500 dark:text-[var(--dark-9)] absolute bottom-0 left-0 right-0 top-0 m-auto grid h-10 w-10 animate-spin place-items-center',
                'empty' => 'dark:text-[var(--dark-9)] col-span-full whitespace-nowrap px-3 py-4 text-sm text-[var(--color-text-muted)] text-center',
                'filter.wrapper' => 'mb-4 flex items-end gap-x-2 sm:gap-x-0',
                'slots.header' => 'mb-2 dark:text-[var(--dark-10)] text-[var(--color-text)]',
                'slots.footer' => 'mt-2 dark:text-[var(--dark-10)] text-[var(--color-text)]',
            ]);
    }
}
