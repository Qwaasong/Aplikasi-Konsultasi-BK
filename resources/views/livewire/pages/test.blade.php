<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.guest')] class extends Component {
    //
}; ?>
<div>
    {{-- Test Komponen --}}
    <form class="space-y-6">
        <x-molecules.auth-header title="Welcome Back" subtitle="Login to your account" linkText="Don't have an account?" linkHref="#"/>
        <x-molecules.input-field label="Email" id="email" type="email" name="email" placeholder="Enter your email"/>
        <x-molecules.input-field label="Password" id="password" type="password" name="password" placeholder="Enter your password"/>
        <x-molecules.auth-remember/>
        <x-atoms.button variant="primary" size="lg" type="submit">{{ __('Login') }}</x-atoms.button>
    </form>
</div>