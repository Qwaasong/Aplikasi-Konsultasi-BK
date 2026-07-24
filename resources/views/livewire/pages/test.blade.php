<?php

use App\Livewire\Pages\TestPage;
use Livewire\Attributes\Layout;

new #[Layout('layouts.guest')] class extends TestPage {}; ?>
<div>
    {{-- Test Komponen --}}
    <livewire:partials.sidebar />
    <livewire:partials.siswa-index />
</div>