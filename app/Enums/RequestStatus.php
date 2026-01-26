<?php

namespace App\Enums;

enum RequestStatus: string
{
    case DRAFT = 'draft';
    case OPEN = 'open';
    case CLOSED = 'closed';
    case AWARDED = 'awarded';
    case CANCELLED = 'cancelled';

    /**
     * Get a human-readable label for the status
     */
    public function label(): string
    {
        return match($this) {
            self::DRAFT => __('Draft'),
            self::OPEN => 'Open',
            self::CLOSED => 'Closed',
            self::AWARDED => 'Awarded',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::OPEN => 'blue',
            self::CLOSED => 'orange',
            self::AWARDED => 'green',
            self::CANCELLED => 'red',
        };
    }

    /**
     * Check if the status is a final status (no further changes expected)
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::CLOSED, self::AWARDED, self::CANCELLED]);
    }

    /**
     * Check if the status allows editing
     */
    public function allowsEditing(): bool
    {
        return in_array($this, [self::DRAFT, self::OPEN]);
    }

    /**
     * Get all statuses as an array for select inputs
     */
    public static function forSelect(): array
    {
        return [
            self::DRAFT->value => self::DRAFT->label(),
            self::OPEN->value => self::OPEN->label(),
            self::CLOSED->value => self::CLOSED->label(),
            self::AWARDED->value => self::AWARDED->label(),
            self::CANCELLED->value => self::CANCELLED->label(),
        ];
    }
}
