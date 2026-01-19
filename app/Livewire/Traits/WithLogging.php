<?php

namespace App\Livewire\Traits;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

trait WithLogging
{
    /**
     * Log an action to the database.
     */
    protected function logAction(
        string $type,
        string $message,
        ?string $action = null,
        ?string $model = null,
        ?int $modelId = null,
        ?array $metadata = null
    ): void {
        try {
            // Enhance metadata with additional context
            $enhancedMetadata = array_merge($metadata ?? [], [
                'session_id' => session()->getId(),
                'request_method' => request()->method(),
                'route_name' => request()->route()?->getName(),
                'referer' => request()->header('referer'),
                'timestamp' => now()->toIso8601String(),
            ]);

            Log::create([
                'user_id' => Auth::id(),
                'type' => $type,
                'action' => $action,
                'model' => $model,
                'model_id' => $modelId,
                'message' => $message,
                'metadata' => $enhancedMetadata,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silent fail - don't break the app if logging fails
            logger()->error('Failed to log action: ' . $e->getMessage());
        }
    }

    /**
     * Log a page view.
     */
    protected function logPageView(string $pageName): void
    {
        $this->logAction(
            type: 'page_view',
            message: "User viewed {$pageName} page",
            action: 'view.' . strtolower(str_replace(' ', '_', $pageName)),
            metadata: [
                'page' => $pageName,
                'url' => request()->fullUrl(),
            ]
        );
    }

    /**
     * Log a create action.
     */
    protected function logCreate(string $model, int $modelId, array $data = []): void
    {
        $modelName = class_basename($model);
        $this->logAction(
            type: 'create',
            message: "Created {$modelName} #{$modelId}",
            action: strtolower($modelName) . '.create',
            model: $modelName,
            modelId: $modelId,
            metadata: ['data' => $data]
        );
    }

    /**
     * Log an update action.
     */
    protected function logUpdate(string $model, int $modelId, array $changes = []): void
    {
        $modelName = class_basename($model);
        $this->logAction(
            type: 'update',
            message: "Updated {$modelName} #{$modelId}",
            action: strtolower($modelName) . '.update',
            model: $modelName,
            modelId: $modelId,
            metadata: ['changes' => $changes]
        );
    }

    /**
     * Log a delete action.
     */
    protected function logDelete(string $model, int $modelId, array $data = []): void
    {
        $modelName = class_basename($model);
        $this->logAction(
            type: 'delete',
            message: "Deleted {$modelName} #{$modelId}",
            action: strtolower($modelName) . '.delete',
            model: $modelName,
            modelId: $modelId,
            metadata: ['data' => $data]
        );
    }

    /**
     * Log an authentication action.
     */
    protected function logAuth(string $type, string $message): void
    {
        $this->logAction(
            type: 'auth',
            message: $message,
            action: 'auth.' . $type
        );
    }

    /**
     * Log an error.
     */
    protected function logError(string $message, ?\Throwable $exception = null): void
    {
        $metadata = [];
        if ($exception) {
            $metadata = [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        }

        $this->logAction(
            type: 'error',
            message: $message,
            action: 'error',
            metadata: $metadata
        );
    }

    /**
     * Log an export action.
     */
    protected function logExport(string $model, string $format = 'csv', int $recordCount = 0): void
    {
        $modelName = class_basename($model);
        $this->logAction(
            type: 'export',
            message: "Exported {$recordCount} {$modelName} records as {$format}",
            action: strtolower($modelName) . '.export',
            model: $modelName,
            metadata: [
                'format' => $format,
                'record_count' => $recordCount,
            ]
        );
    }

    /**
     * Log an import action.
     */
    protected function logImport(string $model, int $successCount = 0, int $failureCount = 0): void
    {
        $modelName = class_basename($model);
        $this->logAction(
            type: 'import',
            message: "Imported {$successCount} {$modelName} records ({$failureCount} failures)",
            action: strtolower($modelName) . '.import',
            model: $modelName,
            metadata: [
                'success_count' => $successCount,
                'failure_count' => $failureCount,
            ]
        );
    }

    /**
     * Log a bulk action.
     */
    protected function logBulkAction(string $model, string $action, int $count, array $ids = []): void
    {
        $modelName = class_basename($model);
        $this->logAction(
            type: 'bulk',
            message: "Performed bulk {$action} on {$count} {$modelName} records",
            action: strtolower($modelName) . '.bulk.' . $action,
            model: $modelName,
            metadata: [
                'action' => $action,
                'count' => $count,
                'ids' => $ids,
            ]
        );
    }

    /**
     * Log a system event.
     */
    protected function logSystem(string $event, string $message, array $metadata = []): void
    {
        $this->logAction(
            type: 'system',
            message: $message,
            action: 'system.' . $event,
            metadata: $metadata
        );
    }

    /**
     * Log a security event.
     */
    protected function logSecurity(string $event, string $message, array $metadata = []): void
    {
        $this->logAction(
            type: 'security',
            message: $message,
            action: 'security.' . $event,
            metadata: array_merge($metadata, [
                'severity' => 'high',
            ])
        );
    }

    /**
     * Log a configuration change.
     */
    protected function logConfigChange(string $key, $oldValue, $newValue): void
    {
        $this->logAction(
            type: 'config',
            message: "Configuration '{$key}' changed",
            action: 'config.change',
            metadata: [
                'key' => $key,
                'old_value' => $oldValue,
                'new_value' => $newValue,
            ]
        );
    }
}
