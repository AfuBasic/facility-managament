<?php

namespace App\Livewire\Concerns;

trait WithNotifications
{
    /**
     * Dispatch a success toast notification
     */
    protected function success(string $message): void
    {
        $this->dispatch('toast', message: $message, type: 'success');
    }

    /**
     * Dispatch an error toast notification
     */
    protected function error(string $message): void
    {
        $this->dispatch('toast', message: $message, type: 'error');
    }

    /**
     * Dispatch an info toast notification
     */
    protected function info(string $message): void
    {
        $this->dispatch('toast', message: $message, type: 'info');
    }

    /**
     * Dispatch a warning toast notification
     */
    protected function warning(string $message): void
    {
        $this->dispatch('toast', message: $message, type: 'warning');
    }
}
