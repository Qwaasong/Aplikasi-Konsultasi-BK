<?php

use Livewire\Volt\Component;

new class extends Component {
    /**
     * Log the current user out of the application.
     */
}; 

?>

<nav>
    <!-- Placeholder Navigation -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <x-atoms.application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>