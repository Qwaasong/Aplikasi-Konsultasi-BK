<?php

use App\Services\UserService;
use App\Constants\GlobalMessages;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\WithPagination;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    // ── Filter ──────────────────────────────
    public string $search    = '';
    public string $filterRole = '';

    // ── Form state ──────────────────────────
    public ?int  $editingId = null;
    public bool  $showForm  = false;
    public bool  $showPassword = false;

    #[Validate('required|string|max:255')]
    public string $nama = '';

    #[Validate('required|string|max:100')]
    public string $username = '';

    #[Validate('required|in:admin,konselor')]
    public string $role = 'konselor';

    // Password: wajib saat create, opsional saat edit
    public string $password = '';
    public string $password_confirmation = '';

    // ── Options ─────────────────────────────
    public array $roleOptions = [
        ['value' => 'admin',    'label' => 'Admin'],
        ['value' => 'konselor', 'label' => 'Konselor'],
    ];

    // ─────────────────────────────────────────
    // DATA UNTUK TEMPLATE
    // ─────────────────────────────────────────

    public function with(): array
    {
        $service = app(UserService::class);

        $filters = [
            'search'   => $this->search,
            'role'     => $this->filterRole ?: null,
            'per_page' => 15,
        ];

        return [
            'records' => $service->getPaginated($filters),
            'stats'   => $service->getStats(),
        ];
    }

    // ─────────────────────────────────────────
    // FORM ACTIONS
    // ─────────────────────────────────────────

    public function create(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showForm  = true;
    }

    public function edit(int $id): void
    {
        $user = app(UserService::class)->findById($id);

        $this->editingId = $id;
        $this->nama      = $user->nama;
        $this->username  = $user->username;
        $this->role      = $user->role;
        $this->password  = '';
        $this->password_confirmation = '';

        $this->showForm  = true;
    }

    public function save(UserService $service): void
    {
        // Validasi dasar
        $this->validate([
            'nama'     => 'required|string|max:255',
            'username' => 'required|string|max:100',
            'role'     => 'required|in:admin,konselor',
        ]);

        // Validasi password: wajib saat create, min 6 jika diisi saat edit
        if (!$this->editingId) {
            $this->validate([
                'password' => 'required|string|min:6|confirmed',
            ]);
        } elseif (!empty($this->password)) {
            $this->validate([
                'password' => 'string|min:6|confirmed',
            ]);
        }

        $data = [
            'nama'     => $this->nama,
            'username' => $this->username,
            'role'     => $this->role,
            'password' => $this->password,
        ];

        try {
            if ($this->editingId) {
                $service->update($this->editingId, $data);
                session()->flash('success', GlobalMessages::SUCCESS_UPDATE);
            } else {
                $service->create($data);
                session()->flash('success', GlobalMessages::SUCCESS_SAVE);
            }

            $this->showForm = false;
            $this->resetForm();
        } catch (\Illuminate\Validation\ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
        }
    }

    public function delete(int $id, UserService $service): void
    {
        try {
            $service->delete($id);
            session()->flash('success', GlobalMessages::SUCCESS_DELETE);
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', $e->errors()['user'][0] ?? GlobalMessages::ERROR_GENERIC);
        }
    }

    public function cancelForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function toggleShowPassword(): void
    {
        $this->showPassword = !$this->showPassword;
    }

    // ─────────────────────────────────────────
    // FILTER
    // ─────────────────────────────────────────

    public function resetFilters(): void
    {
        $this->search     = '';
        $this->filterRole = '';
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // ─────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->nama                  = '';
        $this->username              = '';
        $this->role                  = 'konselor';
        $this->password              = '';
        $this->password_confirmation = '';
        $this->showPassword          = false;
        $this->editingId             = null;
    }
}; ?>

<div class="flex-1 flex flex-col min-w-0 bg-white h-full">

    {{-- ── Header ───────────────────────────────── --}}
    <header class="h-20 border-b border-gray-200 px-8 flex items-center justify-between shrink-0">

        <x-molecules.search-input model="search" />

        <x-atoms.button wire:click="create">
            <x-atoms.icon variant="plus" size="md" />
            Tambah User
        </x-atoms.button>

    </header>

    {{-- ── Stats Bar ────────────────────────────── --}}
    <div class="px-8 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-6 text-sm text-gray-600 shrink-0">
        <span>Total: <strong class="text-gray-900">{{ $stats['total'] }}</strong></span>
        <span>Admin: <strong class="text-gray-900">{{ $stats['admin'] }}</strong></span>
        <span>Konselor: <strong class="text-gray-900">{{ $stats['konselor'] }}</strong></span>

        {{-- Filter Role --}}
        <div class="ml-auto flex items-center gap-2">
            <span class="text-gray-400 text-xs">Filter:</span>

            <select wire:model.live="filterRole"
                class="text-xs border border-gray-200 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-brand-teal">
                <option value="">Semua Role</option>
                <option value="admin">Admin</option>
                <option value="konselor">Konselor</option>
            </select>

            <button wire:click="resetFilters" class="text-xs text-brand-teal hover:underline">
                Reset
            </button>
        </div>
    </div>

    {{-- ── Flash Message ────────────────────────── --}}
    <div class="px-4 py-2">
        <x-shared.flash-message />
    </div>

    {{-- ── Tabel User ───────────────────────────── --}}
    <x-organisms.data-table empty="Belum ada data user.">
        @foreach($records as $user)
            <tr wire:key="user-{{ $user->id }}"
                class="group border-b border-gray-100 bg-white transition-all duration-200 h-12 relative
                       hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,0.1)] hover:z-10 cursor-pointer">

                {{-- Checkbox --}}
                <td class="w-16 text-center align-middle rounded-l-md py-2" onclick="event.stopPropagation()">
                    <input type="checkbox"
                        class="w-4 h-4 rounded border-gray-300 text-brand-teal accent-brand-teal cursor-pointer">
                </td>

                {{-- Avatar + Nama --}}
                <td class="px-4 py-2 w-1/3 align-middle">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-icon-bg text-primary flex items-center justify-center font-bold text-xs shrink-0">
                            {{ strtoupper(substr($user->nama, 0, 1)) }}{{ strtoupper(substr(strstr($user->nama, ' ') ?: '_', 1, 1)) }}
                        </div>
                        <span class="font-semibold text-gray-900">{{ $user->nama }}</span>
                    </div>
                </td>

                {{-- Username --}}
                <td class="px-4 py-2 w-1/4 text-sm text-gray-500 align-middle font-mono">
                    {{ $user->username }}
                </td>

                {{-- Role Badge --}}
                <td class="px-4 py-2 align-middle">
                    <span @class([
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold',
                        'bg-blue-100 text-blue-700'   => $user->role === 'admin',
                        'bg-teal-100 text-teal-700'   => $user->role === 'konselor',
                    ])>
                        {{ ucfirst($user->role) }}
                    </span>
                </td>

                {{-- Akun Sendiri --}}
                <td class="px-4 py-2 align-middle text-xs text-gray-400">
                    @if($user->id === auth()->id())
                        <span class="inline-flex items-center gap-1 text-amber-600 font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm0 8.625a1.125 1.125 0 100 2.25 1.125 1.125 0 000-2.25zM15.375 12a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0zM7.5 10.875a1.125 1.125 0 100 2.25 1.125 1.125 0 000-2.25z" clip-rule="evenodd" />
                            </svg>
                            Akun Anda
                        </span>
                    @endif
                </td>

                {{-- Aksi --}}
                <td class="px-4 py-2 w-40 text-right align-middle relative rounded-r-md">
                    <x-molecules.table-action :id="$user->id">
                        <x-slot:edit><span class="sr-only">Edit</span></x-slot:edit>
                        <x-slot:delete><span class="sr-only">Hapus</span></x-slot:delete>
                    </x-molecules.table-action>
                </td>
            </tr>
        @endforeach
    </x-organisms.data-table>

    {{-- Pagination --}}
    <div class="px-6 py-3 border-t border-gray-100 shrink-0">
        {{ $records->links() }}
    </div>


    {{-- ═══════════════════════════════════════════ --}}
    {{-- MODAL FORM (Tambah / Edit)                  --}}
    {{-- ═══════════════════════════════════════════ --}}
    @if($showForm)
        <x-shared.modal name="form-user" :show="true" maxWidth="md">
            <div class="flex flex-col max-h-[90vh]">

                {{-- Header Modal --}}
                <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
                    <h2 class="text-base font-bold text-gray-900">
                        {{ $editingId ? 'Edit Data User' : 'Tambah User Baru' }}
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Isi semua field yang wajib diisi (*)
                    </p>
                </div>

                {{-- Body --}}
                <div class="px-6 py-5 overflow-y-auto modal-scroll grow space-y-4"
                     style="scrollbar-width: thin;">

                    {{-- Nama --}}
                    <div>
                        <x-atoms.input-label for="nama" size="sm">Nama Lengkap *</x-atoms.input-label>
                        <x-atoms.text-input
                            id="nama"
                            type="text"
                            wire:model="nama"
                            placeholder="Nama lengkap user"
                            size="md"
                        />
                        @error('nama')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Username --}}
                    <div>
                        <x-atoms.input-label for="username" size="sm">Username *</x-atoms.input-label>
                        <x-atoms.text-input
                            id="username"
                            type="text"
                            wire:model="username"
                            placeholder="Username untuk login"
                            size="md"
                        />
                        @error('username')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div>
                        <x-atoms.input-label for="role" size="sm">Role *</x-atoms.input-label>
                        <x-molecules.input-dropdown
                            id="role"
                            wire:model="role"
                            size="md"
                            :options="$roleOptions"
                        />
                        @error('role')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-gray-100 pt-2">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                            {{ $editingId ? 'Ganti Password (kosongkan jika tidak ingin mengubah)' : 'Password *' }}
                        </p>

                        {{-- Password --}}
                        <div class="mb-4">
                            <x-atoms.input-label for="password" size="sm">
                                Password {{ $editingId ? '' : '*' }}
                            </x-atoms.input-label>
                            <div class="relative">
                                <x-atoms.text-input
                                    id="password"
                                    :type="$showPassword ? 'text' : 'password'"
                                    wire:model="password"
                                    placeholder="{{ $editingId ? 'Kosongkan jika tidak diubah' : 'Minimal 6 karakter' }}"
                                    size="md"
                                    class="pr-10"
                                />
                                <button
                                    type="button"
                                    wire:click="toggleShowPassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    @if($showPassword)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    @endif
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div>
                            <x-atoms.input-label for="password_confirmation" size="sm">
                                Konfirmasi Password {{ $editingId ? '' : '*' }}
                            </x-atoms.input-label>
                            <x-atoms.text-input
                                id="password_confirmation"
                                :type="$showPassword ? 'text' : 'password'"
                                wire:model="password_confirmation"
                                placeholder="Ulangi password"
                                size="md"
                            />
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="bg-bg-light px-6 py-4 border-t border-gray-100 flex justify-end gap-3 shrink-0 rounded-b-xl">
                    <x-atoms.button variant="secondary" wire:click="cancelForm">Batal</x-atoms.button>
                    <x-atoms.button wire:click="save">
                        <span wire:loading.remove wire:target="save">
                            {{ $editingId ? 'Perbarui' : 'Simpan' }}
                        </span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </x-atoms.button>
                </div>
            </div>
        </x-shared.modal>
    @endif

</div>