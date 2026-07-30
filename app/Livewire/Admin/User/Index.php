<?php

namespace App\Livewire\Admin\User;

use App\Constants\GlobalMessages;
use App\Services\s\UserService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterRole = '';

    public ?int $editingId = null;
    public bool $showForm = false;
    public bool $showPassword = false;

    #[Validate('required|string|max:255')]
    public string $nama = '';

    #[Validate('required|string|max:100')]
    public string $username = '';

    #[Validate('required|in:admin,guru_bk')]
    public string $role = 'guru_bk';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('required|in:L,P')]
    public string $jenis_kelamin = 'L';

    #[Validate('required|string|max:20')]
    public string $no_hp = '';

    public string $password = '';
    public string $password_confirmation = '';

    public array $roleOptions = [
        ['value' => 'admin', 'label' => 'Admin'],
        ['value' => 'guru_bk', 'label' => 'Konselor'],
    ];
    public array $jenisKelaminOptions = [
        ['value' => 'L', 'label' => 'Laki-laki'],
        ['value' => 'P', 'label' => 'Perempuan'],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function with(): array
    {
        $service = app(UserService::class);
        $filters = [
            'search' => $this->search,
            'role' => $this->filterRole ?: null,
            'per_page' => 15,
        ];
        return [
            'records' => $service->getPaginated($filters),
            'stats' => $service->getStats(),
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showForm = true;
        $this->dispatch('open-modal', 'form-user');
    }

    public function edit(int $id): void
    {
        $user = app(UserService::class)->findById($id);
        $this->editingId = $id;
        $this->nama = $user->nama;
        $this->username = $user->username;
        $this->role = $user->role;
        $this->email = $user->email;
        $this->jenis_kelamin = $user->jenis_kelamin;
        $this->no_hp = $user->no_hp;
        $this->password = '';
        $this->password_confirmation = '';
        $this->showForm = true;
        $this->dispatch('open-modal', 'form-user');
    }

    public function save(UserService $service): void
    {
        $this->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:100',
            'role' => 'required|in:admin,guru_bk',
            'email' => 'required|email|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'no_hp' => 'required|string|max:20',
        ]);

        if (!$this->editingId) {
            $this->validate(['password' => 'required|string|min:6|confirmed']);
        } elseif (!empty($this->password)) {
            $this->validate(['password' => 'string|min:6|confirmed']);
        }

        $data = [
            'nama' => $this->nama,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'jenis_kelamin' => $this->jenis_kelamin,
            'no_hp' => $this->no_hp,
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
            $this->dispatch('close-modal', 'form-user');
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
        $this->dispatch('close-modal', 'form-user');
    }

    public function toggleShowPassword(): void
    {
        $this->showPassword = !$this->showPassword;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterRole = '';
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->nama = '';
        $this->username = '';
        $this->role = 'guru_bk';
        $this->email = '';
        $this->jenis_kelamin = 'L';
        $this->no_hp = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->showPassword = false;
        $this->editingId = null;
    }
}
