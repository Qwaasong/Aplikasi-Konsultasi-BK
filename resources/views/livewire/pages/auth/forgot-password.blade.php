<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $username = '';

    /**
     * Send a password reset link to the provided username.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'username' => ['required', 'string'],
        ]);

        $status = Password::sendResetLink(
            $this->only('username')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('username', __($status));

            return;
        }

        $this->reset('username');

        session()->flash('status', __($status));
    }
}; ?>

<div>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Lupa password? Tidak masalah. Beritahu kami username Anda dan kami akan mengirimkan link reset password yang memungkinkan Anda memilih yang baru.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink">
        <!-- Username -->
        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input wire:model="username" id="username" class="block mt-1 w-full" type="text" name="username"
                required autofocus />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Kirim Link Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</div>