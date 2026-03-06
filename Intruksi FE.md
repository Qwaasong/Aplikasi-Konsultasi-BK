# Instruksi Frontend — Aplikasi Konsultasi BK

> **Untuk siapa dokumen ini?** Untuk kamu (dan AI assistant) yang akan membangun tampilan (frontend) aplikasi ini.
> Dokumen ini mengajarkan **cara berpikir** dan **cara membuat** komponen frontend dengan benar.

---

## Daftar Isi

1. [Filosofi: Kenapa Harus Terstruktur?](#1-filosofi-kenapa-harus-terstruktur)
2. [Dua Jenis Komponen: Dumb vs Smart](#2-dua-jenis-komponen-dumb-vs-smart)
3. [Atomic Design: Dari Kecil ke Besar](#3-atomic-design-dari-kecil-ke-besar)
4. [Atoms — Batu Bata Terkecil](#4-atoms--batu-bata-terkecil)
5. [Molecules — Gabungan Atoms](#5-molecules--gabungan-atoms)
6. [Organisms — Section UI Lengkap](#6-organisms--section-ui-lengkap)
7. [Shared Components — Komponen Lintas Halaman](#7-shared-components--komponen-lintas-halaman)
8. [Layout — Kerangka Halaman](#8-layout--kerangka-halaman)
9. [Pages — Halaman Utuh (Livewire Volt)](#9-pages--halaman-utuh-livewire-volt)
10. [Cara Berpikir Saat Membangun Halaman Baru](#10-cara-berpikir-saat-membangun-halaman-baru)
11. [Aturan Wajib (JANGAN Dilanggar)](#11-aturan-wajib-jangan-dilanggar)

---

## 1. Filosofi: Kenapa Harus Terstruktur?

Bayangkan kamu membangun rumah dari LEGO. Kamu tidak langsung membuat rumah utuh — kamu mulai dari:

1. **Batu bata kecil** (1x1, 2x1) → ini namanya **Atom**
2. **Kamu gabungkan** beberapa batu bata jadi **jendela** atau **pintu** → ini **Molecule**
3. Jendela + pintu + dinding digabung jadi **satu sisi rumah** → ini **Organism**
4. Semua sisi rumah disusun dalam **denah** → ini **Template/Layout**
5. Rumah yang sudah jadi dan berisi furniture → ini **Page**

**Keuntungannya:**

- Kalau jendela rusak, kamu ganti jendelanya saja — bukan bangun ulang seluruh rumah
- Kalau mau buat rumah lain, kamu bisa pakai ulang jendela dan pintu yang sama
- Kalau ada bug di tombol, kamu perbaiki di **satu file saja** — otomatis semua halaman ikut terperbaiki

---

## 2. Dua Jenis Komponen: Dumb vs Smart

Ini adalah **konsep paling penting** yang harus kamu pahami sebelum membuat apapun.

### Dumb Component (Presentational / Bodoh)

**Apa itu?** Komponen yang **hanya menampilkan** sesuatu. Ia tidak tahu dari mana datanya, tidak bisa mengubah database, tidak bisa memvalidasi. Ia hanya terima perintah dan tampilkan.

**Teknologi:** Blade Component (`<x-nama-komponen />`)

**Analogi:** Seperti **bingkai foto**. Bingkai tidak peduli foto apa yang dipasang di dalamnya. Ia hanya menampilkan apapun yang diberikan kepadanya.

```blade
{{-- Ini Dumb Component: ia hanya tampilkan teks di dalam tombol --}}
<button class="bg-blue-600 text-white px-4 py-2 rounded">
    {{ $slot }}  {{-- Apapun yang diberikan, ia tampilkan --}}
</button>
```

### Smart Component (Stateful / Pintar)

**Apa itu?** Komponen yang **punya otak**. Ia tahu data apa yang sedang ditampilkan, bisa bereaksi terhadap aksi user (klik, ketik), bisa memvalidasi input, dan bisa memanggil Service Layer untuk menyimpan/mengambil data.

**Teknologi:** Livewire Component (class PHP + Blade view) atau Livewire Volt (single-file)

**Analogi:** Seperti **kasir toko**. Kasir menerima uang (input), menghitung kembalian (logika), dan memberikan struk (output). Kasir punya "state" — ia tahu berapa total belanja saat ini.

```php
// Ini Smart Component: ia punya state ($nama) dan aksi (save)
new class extends Component {
    public string $nama = '';  // ← State (ia "ingat" nilainya)

    public function save(UserService $service) // ← Memanggil Service Layer
    {
        $this->validate();                      // ← Bisa validasi
        $service->create(['nama' => $this->nama]); // ← Bisa ubah database (via Service)
    }
};
```

### Tabel Perbandingan

| Aspek                             | Dumb (Blade `<x-...>`)         | Smart (Livewire)                    |
| --------------------------------- | ------------------------------ | ----------------------------------- |
| Punya variabel/state?             | ❌ Tidak                       | ✅ Ya (`public $nama`)              |
| Bisa validasi input?              | ❌ Tidak                       | ✅ Ya (`$this->validate()`)         |
| Bisa panggil Service/DB?          | ❌ DILARANG                    | ✅ Ya (wajib via Service)           |
| Bisa bereaksi terhadap klik?      | ❌ Tidak (ia hanya meneruskan) | ✅ Ya (`wire:click="save"`)         |
| Reusability (bisa dipakai ulang)? | ✅ Sangat tinggi               | ⚠️ Terbatas pada konteksnya         |
| Contoh                            | Tombol, Input, Badge, Tabel    | Halaman Login, Form CRUD, Dashboard |

### Aturan Emas

> **Blade Component = SELALU Dumb.**
> **Livewire Component = SELALU Smart.**
> Tidak ada pengecualian.

---

## 3. Atomic Design: Dari Kecil ke Besar

Berikut urutan ukuran komponen, dari terkecil ke terbesar:

```
Atom → Molecule → Organism → Layout/Template → Page
(kecil)                                      (besar)
```

**Struktur folder:**

```text
resources/views/
├── components/                    ← Semua DUMB Components
│   ├── atoms/                     ← Level 1: Elemen terkecil
│   ├── molecules/                 ← Level 2: Gabungan atoms
│   ├── organisms/                 ← Level 3: Section UI lengkap
│   └── shared/                    ← Komponen lintas halaman
│
├── layouts/                       ← Kerangka halaman
│   ├── app.blade.php              ← Untuk halaman setelah login
│   └── guest.blade.php            ← Untuk halaman login/register
│
└── livewire/                      ← Semua SMART Components
    ├── layout/                    ← Navigasi
    ├── pages/                     ← Halaman utuh (Volt single-file)
    │   ├── admin/
    │   ├── konselor/
    │   └── auth/
    └── partials/                  ← Potongan Smart yang reusable
```

---

## 4. Atoms — Batu Bata Terkecil

### Apa itu Atom?

Atom adalah elemen HTML **paling dasar** yang tidak bisa dipecah lagi. Satu atom = satu fungsi visual.

**Contoh atom:** Button, Text Input, Label, Checkbox, Badge, Avatar, Icon

### Di mana menyimpannya?

```
resources/views/components/atoms/
├── button.blade.php
├── text-input.blade.php
├── input-label.blade.php
├── input-error.blade.php
├── badge.blade.php
├── avatar.blade.php
└── checkbox.blade.php
```

### Cara membuat Atom

**Prinsip utama:**

1. Atom hanya menerima konfigurasi lewat `@props` (properti) dan `{{ $attributes }}` (atribut HTML)
2. Atom **TIDAK BOLEH** hardcode `wire:model` atau `wire:click` — biarkan parent yang memberikannya
3. Gunakan `$attributes->merge()` agar parent bisa menambahkan class atau atribut lain

### Contoh Lengkap: Button

```blade
{{-- File: resources/views/components/atoms/button.blade.php --}}

{{--
    PENJELASAN @props:
    - 'variant' menentukan warna/gaya tombol (primary = biru, danger = merah, dll)
    - 'size' menentukan ukuran (sm = kecil, md = sedang, lg = besar)
    - 'type' menentukan tipe HTML button (button, submit, reset)

    Semua props PUNYA nilai default, jadi parent tidak wajib mengisinya.
--}}
@props([
    'variant' => 'primary',
    'size'    => 'md',
    'type'    => 'button',
])

@php
    // Class dasar yang SELALU ada di semua tombol
    $baseClasses = 'inline-flex items-center justify-center font-semibold rounded-md
                    transition ease-in-out duration-150 focus:outline-none focus:ring-2
                    focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    // Warna berbeda berdasarkan variant
    $variants = [
        'primary'   => 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500',
        'secondary' => 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-indigo-500',
        'danger'    => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        'ghost'     => 'bg-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-100',
    ];

    // Ukuran berbeda berdasarkan size
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];
@endphp

{{--
    PENJELASAN $attributes->merge():
    Ini memungkinkan parent menambahkan atribut TAMBAHAN.
    Misal parent menulis: <x-atoms.button wire:click="save" class="ml-2">
    Maka 'wire:click="save"' dan 'class="ml-2"' akan DIGABUNGKAN ke button ini.
--}}
<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "$baseClasses {$variants[$variant]} {$sizes[$size]}"]) }}
>
    {{ $slot }}
</button>
```

**Cara parent menggunakan:**

```blade
{{-- Primary button (default) --}}
<x-atoms.button>Klik Saya</x-atoms.button>

{{-- Danger button, ukuran kecil, dengan aksi Livewire --}}
<x-atoms.button variant="danger" size="sm" wire:click="delete({{ $id }})">
    Hapus
</x-atoms.button>

{{-- Submit button di form --}}
<x-atoms.button variant="primary" type="submit">
    Simpan Data
</x-atoms.button>
```

> **Perhatikan:** `wire:click="delete({{ $id }})"` ditulis oleh **parent** (Smart Component), bukan di dalam file `button.blade.php`. Atom hanya meneruskannya lewat `$attributes->merge()`.

### Contoh Lengkap: Text Input

```blade
{{-- File: resources/views/components/atoms/text-input.blade.php --}}
@props(['disabled' => false])

<input
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'w-full border-gray-300 rounded-md shadow-sm
                    focus:border-indigo-500 focus:ring-indigo-500
                    disabled:bg-gray-100 disabled:cursor-not-allowed'
    ]) }}
>
```

### Contoh Lengkap: Badge

```blade
{{-- File: resources/views/components/atoms/badge.blade.php --}}
@props([
    'color' => 'gray',
])

@php
    $colors = [
        'gray'   => 'bg-gray-100 text-gray-700',
        'green'  => 'bg-green-100 text-green-700',
        'yellow' => 'bg-yellow-100 text-yellow-800',
        'red'    => 'bg-red-100 text-red-700',
        'blue'   => 'bg-blue-100 text-blue-700',
    ];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$colors[$color]}"]) }}>
    {{ $slot }}
</span>
```

**Penggunaan:**

```blade
<x-atoms.badge color="green">Aktif</x-atoms.badge>
<x-atoms.badge color="red">Ditolak</x-atoms.badge>
```

### Contoh Lengkap: Input Label

```blade
{{-- File: resources/views/components/atoms/input-label.blade.php --}}
@props(['value' => null])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-gray-700']) }}>
    {{ $value ?? $slot }}
</label>
```

### Contoh Lengkap: Input Error

```blade
{{-- File: resources/views/components/atoms/input-error.blade.php --}}
@props(['messages' => []])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
```

---

## 5. Molecules — Gabungan Atoms

### Apa itu Molecule?

Molecule adalah **gabungan 2 atau lebih Atoms** yang bersama-sama membentuk satu fungsi UI yang bermakna. Molecule masih **Dumb** (Blade Component).

**Analogi:** Atom `Label` + Atom `Input` + Atom `Error` = Molecule `Form Group`. Seperti 3 bahan (tepung + air + garam) yang digabung jadi satu adonan.

### Di mana menyimpannya?

```
resources/views/components/molecules/
├── form-group.blade.php
├── search-input.blade.php
├── stat-card.blade.php
├── dropdown.blade.php
└── nav-link.blade.php
```

### Contoh Lengkap: Form Group

Ini adalah molecule **paling sering dipakai**. Ia menggabungkan Label + Input + Error jadi satu kesatuan, sehingga kamu tidak perlu menulis ketiganya berulang-ulang.

```blade
{{-- File: resources/views/components/molecules/form-group.blade.php --}}

{{--
    PENJELASAN:
    Molecule ini menggabungkan 3 atoms: input-label, text-input, input-error.

    Dengan molecule ini, daripada menulis:
        <x-atoms.input-label for="nama" value="Nama" />
        <x-atoms.text-input id="nama" wire:model="nama" class="mt-1 block w-full" />
        <x-atoms.input-error :messages="$errors->get('nama')" class="mt-1" />

    Kamu cukup menulis:
        <x-molecules.form-group label="Nama" name="nama" wire:model="nama" />

    Jauh lebih ringkas!
--}}

@props([
    'label',              {{-- Teks label (wajib) --}}
    'name',               {{-- Nama field, dipakai untuk id, name, dan error (wajib) --}}
    'type' => 'text',     {{-- Tipe input: text, email, password, number, dll --}}
    'required' => false,  {{-- Apakah field ini wajib diisi? --}}
])

<div {{ $attributes->only('class') }}>
    {{-- Atom 1: Label --}}
    <x-atoms.input-label :for="$name" :value="$label" />

    {{-- Atom 2: Input --}}
    {{--
        $attributes->except('class') akan meneruskan SEMUA atribut dari parent
        KECUALI 'class' (karena class sudah dipakai di <div> pembungkus).

        Ini artinya jika parent menulis wire:model="nama", maka wire:model
        akan diteruskan ke <input> di dalam atom text-input.
    --}}
    <x-atoms.text-input
        :id="$name"
        :name="$name"
        :type="$type"
        class="mt-1 block w-full"
        :required="$required"
        {{ $attributes->except('class') }}
    />

    {{-- Atom 3: Error message --}}
    <x-atoms.input-error :messages="$errors->get($name)" class="mt-1" />
</div>
```

**Cara parent menggunakan:**

```blade
{{-- Di dalam form Livewire --}}
<form wire:submit="save" class="space-y-4">
    <x-molecules.form-group label="Nama Lengkap" name="nama" wire:model="nama" required />
    <x-molecules.form-group label="Email" name="email" type="email" wire:model="email" required />
    <x-molecules.form-group label="Alamat" name="alamat" wire:model="alamat" />
</form>
```

### Contoh Lengkap: Search Input

```blade
{{-- File: resources/views/components/molecules/search-input.blade.php --}}

{{--
    Molecule ini terdiri dari: Icon SVG (inline) + Atom text-input.
    Menampilkan input pencarian dengan ikon kaca pembesar di sebelah kiri.
--}}
@props([
    'placeholder' => 'Cari...',
])

<div class="relative" {{ $attributes->only('class') }}>
    {{-- Ikon kaca pembesar (posisi absolute di kiri input) --}}
    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </div>

    {{-- Atom text-input dengan padding kiri ekstra (supaya teks tidak tertimpa ikon) --}}
    <x-atoms.text-input
        type="search"
        :placeholder="$placeholder"
        class="pl-10"
        {{ $attributes->whereStartsWith('wire:') }}
    />
</div>
```

**Penggunaan:**

```blade
<x-molecules.search-input
    wire:model.live.debounce.300ms="search"
    placeholder="Cari siswa..."
    class="w-64"
/>
```

### Contoh Lengkap: Stat Card

```blade
{{-- File: resources/views/components/molecules/stat-card.blade.php --}}

{{--
    Molecule untuk menampilkan statistik di dashboard.
    Contoh: "Total Siswa: 125"
--}}
@props([
    'title',            {{-- Judul statistik (misal: "Total Siswa") --}}
    'value',            {{-- Nilainya (misal: "125") --}}
    'color' => 'indigo', {{-- Warna aksen --}}
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow p-6']) }}>
    <p class="text-sm font-medium text-gray-500">{{ $title }}</p>
    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $value }}</p>
    @if($slot->isNotEmpty())
        <p class="mt-1 text-sm text-{{ $color }}-600">{{ $slot }}</p>
    @endif
</div>
```

---

## 6. Organisms — Section UI Lengkap

### Apa itu Organism?

Organism adalah **section besar** dari halaman yang terbentuk dari gabungan Molecules dan Atoms. Organism bisa **Dumb** (Blade) atau **Smart** (Livewire).

**Kapan Dumb?** Ketika organism hanya menampilkan data yang diberikan oleh parent.
**Kapan Smart?** Ketika organism punya state sendiri (misal: form konsultasi yang bisa submit).

### Contoh: Data Table (Dumb Organism)

```blade
{{-- File: resources/views/components/organisms/data-table.blade.php --}}

{{--
    Organism Dumb: Tabel data yang reusable.

    Ia TIDAK tahu data apa yang ditampilkan — parent yang mengisi baris-barisnya
    lewat $slot. Organism ini hanya menyediakan "kerangka" tabel.
--}}
@props([
    'headers' => [],                    {{-- Array nama kolom header --}}
    'empty'   => 'Belum ada data.',     {{-- Pesan saat data kosong --}}
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow overflow-hidden']) }}>
    <table class="min-w-full divide-y divide-gray-200">
        {{-- Header tabel --}}
        <thead class="bg-gray-50">
            <tr>
                @foreach($headers as $header)
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>

        {{-- Body tabel: parent mengisi ini lewat $slot --}}
        <tbody class="divide-y divide-gray-200">
            {{ $slot }}
        </tbody>
    </table>

    {{-- Pagination (opsional, parent bisa mengirim lewat named slot) --}}
    @if(isset($pagination))
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
            {{ $pagination }}
        </div>
    @endif
</div>
```

**Penggunaan (di dalam Smart Component / Page):**

```blade
<x-organisms.data-table :headers="['Nama', 'Email', 'Role', 'Aksi']">
    @forelse($users as $user)
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4">{{ $user->name }}</td>
            <td class="px-6 py-4 text-gray-500">{{ $user->email }}</td>
            <td class="px-6 py-4">
                <x-atoms.badge :color="$user->role === 'admin' ? 'blue' : 'green'">
                    {{ ucfirst($user->role) }}
                </x-atoms.badge>
            </td>
            <td class="px-6 py-4 text-right">
                <x-atoms.button variant="ghost" size="sm" wire:click="edit({{ $user->id }})">
                    Edit
                </x-atoms.button>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                <x-shared.empty-state message="Belum ada user." />
            </td>
        </tr>
    @endforelse

    {{-- Named slot untuk pagination --}}
    <x-slot:pagination>
        {{ $users->links() }}
    </x-slot:pagination>
</x-organisms.data-table>
```

---

## 7. Shared Components — Komponen Lintas Halaman

Shared components adalah komponen yang **dipakai di banyak halaman** secara konsisten. Mereka masih **Dumb** tetapi sangat umum digunakan.

### Lokasi: `resources/views/components/shared/`

### Flash Message (Wajib Ada)

```blade
{{-- File: resources/views/components/shared/flash-message.blade.php --}}

{{--
    Komponen ini menampilkan pesan sukses atau error setelah aksi.
    Menggunakan Alpine.js untuk auto-hide setelah beberapa detik.

    CARA KERJA:
    1. Smart Component (Livewire) memanggil: session()->flash('success', 'Berhasil!');
    2. Halaman me-render ulang
    3. Komponen ini mendeteksi session('success') dan menampilkan pesan hijau
    4. Setelah 4 detik, Alpine.js menyembunyikannya dengan animasi fade-out
--}}

@if (session('success'))
    <div x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 4000)"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="mb-4 p-4 rounded-md bg-green-50 border border-green-200">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="text-sm text-green-700">{{ session('success') }}</span>
        </div>
    </div>
@endif

@if (session('error'))
    <div x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 6000)"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="mb-4 p-4 rounded-md bg-red-50 border border-red-200">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span class="text-sm text-red-700">{{ session('error') }}</span>
        </div>
    </div>
@endif
```

**Penggunaan (letakkan di atas konten halaman):**

```blade
<x-shared.flash-message />
```

### Loading Spinner

```blade
{{-- File: resources/views/components/shared/loading-spinner.blade.php --}}
@props(['size' => 'md'])

@php
    $sizes = ['sm' => 'h-4 w-4', 'md' => 'h-6 w-6', 'lg' => 'h-8 w-8'];
@endphp

<svg {{ $attributes->merge(['class' => "animate-spin text-indigo-600 {$sizes[$size]}"]) }}
     fill="none" viewBox="0 0 24 24">
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
    <path class="opacity-75" fill="currentColor"
          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
</svg>
```

### Empty State

```blade
{{-- File: resources/views/components/shared/empty-state.blade.php --}}
@props([
    'message' => 'Belum ada data.',
])

<div {{ $attributes->merge(['class' => 'text-center py-8']) }}>
    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-2.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
    </svg>
    <p class="mt-2 text-sm text-gray-500">{{ $message }}</p>
    @if(isset($action))
        <div class="mt-4">{{ $action }}</div>
    @endif
</div>
```

---

## 8. Layout — Kerangka Halaman

Layout mendefinisikan **kerangka HTML** yang membungkus semua halaman. Sudah ada 2 layout:

| Layout          | File                                      | Kapan Dipakai                          |
| --------------- | ----------------------------------------- | -------------------------------------- |
| `layouts.app`   | `resources/views/layouts/app.blade.php`   | Setelah login (dashboard, kelola data) |
| `layouts.guest` | `resources/views/layouts/guest.blade.php` | Sebelum login (login, register)        |

**Cara pakai di Volt Page:**

```php
new #[Layout('layouts.app')] class extends Component {
    // halaman authenticated
};

new #[Layout('layouts.guest')] class extends Component {
    // halaman auth (login, register)
};
```

---

## 9. Pages — Halaman Utuh (Livewire Volt)

Page adalah **Smart Component** yang merepresentasikan satu halaman utuh. Di project ini, kita menggunakan **Livewire Volt** (single-file: PHP + Blade dalam satu file `.blade.php`).

### Lokasi: `resources/views/livewire/pages/{role}/{fitur}/`

### Contoh Page Lengkap

```blade
<?php
// ============================================================
// BAGIAN PHP (Smart Logic)
// ============================================================

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use App\Services\SiswaService;

new #[Layout('layouts.app')] class extends Component {

    // --- State (data yang "diingat" oleh halaman) ---
    public $siswaList = [];
    public ?int $editingId = null;

    // --- Form Properties + Validasi ---
    #[Validate('required|string|max:255')]
    public string $nama = '';

    #[Validate('required|string|max:20')]
    public string $nis = '';

    // --- mount(): dipanggil SEKALI saat halaman pertama kali dibuka ---
    public function mount(SiswaService $service)
    {
        $this->siswaList = $service->getAll();
    }

    // --- save(): dipanggil saat form disubmit ---
    public function save(SiswaService $service)
    {
        $this->validate();

        $data = ['nama' => $this->nama, 'nis' => $this->nis];

        if ($this->editingId) {
            $service->update($this->editingId, $data);
            session()->flash('success', 'Data siswa berhasil diperbarui!');
        } else {
            $service->create($data);
            session()->flash('success', 'Data siswa berhasil ditambahkan!');
        }

        $this->reset(['nama', 'nis', 'editingId']);
        $this->siswaList = $service->getAll();
    }

    // --- delete(): dipanggil saat tombol hapus diklik ---
    public function delete(int $id, SiswaService $service)
    {
        $service->delete($id);
        $this->siswaList = $service->getAll();
        session()->flash('success', 'Data siswa berhasil dihapus!');
    }
}; ?>

{{-- ============================================================ --}}
{{-- BAGIAN BLADE (Tampilan) - Menggunakan Dumb Components         --}}
{{-- ============================================================ --}}

<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Shared: Flash Message --}}
        <x-shared.flash-message />

        {{-- Form (menggunakan Molecules) --}}
        <form wire:submit="save" class="space-y-4 mb-8 bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold text-gray-800">
                {{ $editingId ? 'Edit Siswa' : 'Tambah Siswa Baru' }}
            </h2>

            {{-- Molecule: form-group (Label + Input + Error dalam 1 baris) --}}
            <x-molecules.form-group label="Nama Lengkap" name="nama" wire:model="nama" required />
            <x-molecules.form-group label="NIS" name="nis" wire:model="nis" required />

            <div class="flex items-center gap-2">
                {{-- Atom: button --}}
                <x-atoms.button variant="primary" type="submit">
                    <span wire:loading.remove wire:target="save">
                        {{ $editingId ? 'Perbarui' : 'Simpan' }}
                    </span>
                    <span wire:loading wire:target="save">
                        <x-shared.loading-spinner size="sm" class="inline mr-1" />
                        Menyimpan...
                    </span>
                </x-atoms.button>

                @if($editingId)
                    <x-atoms.button variant="ghost" wire:click="$set('editingId', null)">
                        Batal
                    </x-atoms.button>
                @endif
            </div>
        </form>

        {{-- Organism: Data Table --}}
        <x-organisms.data-table :headers="['Nama', 'NIS', 'Aksi']" empty="Belum ada data siswa.">
            @foreach ($siswaList as $siswa)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium">{{ $siswa->nama }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $siswa->nis }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <x-atoms.button variant="ghost" size="sm" wire:click="edit({{ $siswa->id }})">
                            Edit
                        </x-atoms.button>
                        <x-atoms.button variant="danger" size="sm"
                            wire:click="delete({{ $siswa->id }})"
                            wire:confirm="Yakin ingin menghapus siswa ini?">
                            Hapus
                        </x-atoms.button>
                    </td>
                </tr>
            @endforeach
        </x-organisms.data-table>

    </div>
</div>
```

---

## 10. Cara Berpikir Saat Membangun Halaman Baru

Setiap kali kamu akan membuat halaman baru, ikuti langkah mental ini:

### Langkah 1: Pecah desain jadi potongan-potongan

Lihat desain halaman → identifikasi **atoms**, **molecules**, **organisms** apa yang dibutuhkan.

### Langkah 2: Cek yang sudah ada

Sebelum membuat komponen baru, cek dulu apakah sudah ada di `components/atoms/`, `components/molecules/`, dll. **Jangan buat duplikat!**

### Langkah 3: Buat dari bawah ke atas

```
1. Buat Atoms baru (jika belum ada)
2. Buat Molecules baru (jika belum ada) — gabungkan atoms
3. Buat Organisms baru (jika belum ada) — gabungkan molecules + atoms
4. Buat Page (Volt) — gabungkan semua + panggil Service Layer
```

### Langkah 4: Tanyakan "Dumb atau Smart?"

```
Komponen ini perlu state / akses database?
├── YA  → Smart (Livewire). Taruh di app/Livewire/ atau livewire/partials/
└── TIDAK → Dumb (Blade). Taruh di components/{atoms|molecules|organisms|shared}/
            └── Perlu interaksi UI sederhana (buka/tutup)?
                ├── YA  → Tambahkan Alpine.js (x-data, x-show)
                └── TIDAK → Pure Blade + Tailwind saja
```

---

## 11. Aturan Wajib (JANGAN Dilanggar)

| #   | Aturan                                     | ❌ Salah                            | ✅ Benar                                            |
| --- | ------------------------------------------ | ----------------------------------- | --------------------------------------------------- |
| 1   | Atom tidak boleh hardcode `wire:`          | `<input wire:model="nama">` di atom | `{{ $attributes }}` di atom, `wire:model` di parent |
| 2   | Blade Component = Dumb                     | Taruh `User::all()` di blade        | Hanya `@props` dan `$slot`                          |
| 3   | Livewire = panggil Service, bukan Eloquent | `$this->users = User::all()`        | `$this->users = $service->getAll()`                 |
| 4   | Jangan buat duplikat                       | Buat tombol baru di setiap halaman  | Pakai `<x-atoms.button>` yang sudah ada             |
| 5   | Pesan feedback lewat session               | `echo "Berhasil"`                   | `session()->flash('success', '...')`                |
| 6   | Shared component untuk hal umum            | Copy-paste flash message HTML       | `<x-shared.flash-message />`                        |
