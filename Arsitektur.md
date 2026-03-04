### 1. Arsitektur Backend (Layered Pattern)

Dalam arsitektur ini, tugas dibagi secara spesifik agar tidak ada class yang menanggung beban terlalu berat (menghindari *Fat Controller* atau *Fat Livewire Component*).

Alur jalannya data: **Route/Livewire Action $\rightarrow$ Validation $\rightarrow$ Service Layer $\rightarrow$ Repository Layer $\rightarrow$ Database**

* **A. Entry Point (Livewire Component / Controller):**
* Tugas: Menerima *request* atau aksi dari user (misal: klik tombol simpan).
* Di stack ini, class Livewire akan menggantikan fungsi Controller tradisional untuk fitur-fitur yang reaktif.


* **B. Validation Layer (Form Requests / Livewire Rules):**
* Tugas: Memastikan data yang masuk sesuai aturan sebelum menyentuh logika bisnis.
* Implementasi: Di Livewire, Anda bisa menggunakan properti `#[Validate]` (Laravel 11 / Livewire 3) atau mendefinisikan array `$rules`. Jika kompleks, Anda bisa memisahkannya ke dalam class khusus *Validator* atau Laravel `FormRequest` jika menggunakan Controller API/Biasa.


* **C. Logic / Service Layer (Business Logic):**
* Tugas: Tempat semua "otak" aplikasi Anda berada. Menghitung, memanggil API eksternal, dan merangkai data.
* Implementasi: Class PHP biasa (misal: `UserService`). Layer ini tidak peduli dari mana data berasal (Web atau API), ia hanya menerima data bersih dari *Validation*, memprosesnya, dan mengirimkan hasilnya.


* **D. Repository Layer (Data Access):**
* Tugas: Tempat sentralisasi query database. Layer atas (Service) tidak boleh tahu bahwa Anda menggunakan Eloquent atau query builder murni.
* Implementasi: Interface (misal: `UserRepositoryInterface`) dan implementasinya (misal: `EloquentUserRepository`). Service hanya memanggil method seperti `$this->userRepo->findByEmail($email)`.


* **E. Models (Eloquent):**
* Tugas: Representasi tabel database dan relasinya. Hindari menaruh logika bisnis yang rumit di sini.



---

### 2. Arsitektur Frontend (TALL Stack)

Karena Anda menggunakan Laravel Breeze dengan Livewire, Anda otomatis masuk ke dalam ekosistem **TALL Stack** (Tailwind, Alpine, Laravel, Livewire).

* **A. Livewire Components (The UI Logic):**
* Penghubung antara Backend dan Frontend. Class PHP tempat Anda menyimpan state (variabel publik) dan *action* (fungsi yang dipanggil dari tombol). State ini akan tersinkronisasi otomatis dengan HTML (Blade).


* **B. Blade Templates (The View):**
* File `.blade.php` standar Laravel. Menampilkan data dan menambahkan *directive* Livewire seperti `wire:model` untuk input form atau `wire:click` untuk tombol.


* **C. Alpine.js (Lightweight Frontend Interactivity):**
* Bawaan dari Breeze. Digunakan untuk manipulasi UI sederhana di sisi klien tanpa perlu bolak-balik ke server. Contoh: Membuka/menutup *Dropdown*, *Modal*, *Tabs*, atau menyembunyikan notifikasi setelah beberapa detik.


* **D. Tailwind CSS (Styling):**
* Utility-first CSS framework bawaan Breeze. Semua styling (warna, layout, grid) dilakukan langsung di dalam class HTML di Blade template.



---

### 3. Gambaran Struktur Folder Rekomendasi

Agar arsitektur ini rapi, Anda bisa membuat struktur folder (namespace) kustom di dalam folder `app/`:

```text
app/
├── Http/
│   └── Livewire/          <-- Class UI Logic (Entry Point)
├── Models/                <-- Eloquent Models
├── Repositories/          <-- Repository Layer (Data Access)
│   ├── Contracts/         <-- Interfaces (misal: UserRepositoryInterface.php)
│   └── Eloquent/          <-- Implementasi Eloquent (misal: UserRepository.php)
├── Services/              <-- Service Layer (Business Logic)
│   └── User/              <-- Misal: UserService.php
└── Providers/
    └── RepositoryServiceProvider.php <-- Untuk binding Interface ke Implementasi (Dependency Injection)

```
---

Sebagai contoh, kita akan membuat fitur **"Create Post" (Membuat Artikel Baru)**. Kita akan menggunakan sintaks Livewire 3 (bawaan Laravel 11/Breeze terbaru) yang sangat elegan.

Berikut adalah implementasi kodenya langkah demi langkah:

---

### 1. Repository Layer (Data Access)

Pertama, kita pisahkan logika database (Eloquent) agar tidak bercampur dengan logika bisnis.

**A. Buat Interface (`app/Repositories/Contracts/PostRepositoryInterface.php`)**
Ini adalah "kontrak" yang menjamin metode apa saja yang harus ada.

```php
<?php

namespace App\Repositories\Contracts;

interface PostRepositoryInterface
{
    public function create(array $data);
}

```

**B. Buat Implementasi Eloquent (`app/Repositories/Eloquent/PostRepository.php`)**
Di sinilah query database murni (Eloquent) dijalankan.

```php
<?php

namespace App\Repositories\Eloquent;

use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;

class PostRepository implements PostRepositoryInterface
{
    public function create(array $data)
    {
        return Post::create($data);
    }
}

```

*Catatan: Jangan lupa daftarkan binding interface ke implementasinya di `app/Providers/AppServiceProvider.php` di dalam method `register()`:*

```php
$this->app->bind(
    \App\Repositories\Contracts\PostRepositoryInterface::class,
    \App\Repositories\Eloquent\PostRepository::class
);

```

---

### 2. Service Layer (Business Logic)

Layer ini bertugas memproses data sebelum masuk ke database. Ia menerima dependensi dari Repository Interface.

**Buat Service (`app/Services/PostService.php`)**

```php
<?php

namespace App\Services;

use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Support\Str;

class PostService
{
    // Dependency Injection: Service memanggil Repository
    public function __construct(
        protected PostRepositoryInterface $postRepository
    ) {}

    public function createNewPost(array $data)
    {
        // Contoh Logika Bisnis: 
        // 1. Buat slug otomatis dari judul
        $data['slug'] = Str::slug($data['title']);
        
        // 2. Mungkin Anda ingin menambahkan user_id yang sedang login
        $data['user_id'] = auth()->id();

        // Setelah diproses, lempar ke Repository untuk disimpan ke database
        return $this->postRepository->create($data);
    }
}

```

---

### 3. Entry Point & Validation (Livewire Component)

Di sini kita menangani interaksi UI dan validasi data menggunakan fitur `#[Validate]` dari Livewire 3.

**Buat Component (`app/Livewire/CreatePost.php`)**

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Services\PostService;

class CreatePost extends Component
{
    // Layer Validasi langsung di properti
    #[Validate('required|min:5|max:255')]
    public $title = '';

    #[Validate('required|min:10')]
    public $content = '';

    // Action saat form disubmit
    // Dependency Injection: Inject PostService secara otomatis
    public function save(PostService $postService)
    {
        // 1. Eksekusi Validasi
        $this->validate();

        // 2. Kirim data yang sudah divalidasi ke Service Layer
        $postService->createNewPost([
            'title' => $this->title,
            'content' => $this->content,
        ]);

        // 3. Reset form dan berikan feedback ke UI
        $this->reset();
        session()->flash('status', 'Artikel berhasil dibuat!');
    }

    public function render()
    {
        return view('livewire.create-post');
    }
}

```

---

### 4. Frontend / View (Blade + Tailwind)

Terakhir, kita buat tampilan formnya. Karena menggunakan Breeze, kita asumsikan Tailwind CSS sudah siap.

**Buat View (`resources/views/livewire/create-post.blade.php`)**

```html
<div>
    @if (session('status'))
        <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Judul</label>
            <input type="text" wire:model="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Konten</label>
            <textarea wire:model="content" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
            @error('content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
            <span wire:loading.remove wire:target="save">Simpan Artikel</span>
            <span wire:loading wire:target="save">Menyimpan...</span>
        </button>
    </form>
</div>

```

---

### Ringkasan Alur:

1. User mengetik di form (otomatis tersinkronisasi dengan variabel `$title` dan `$content` via `wire:model`).
2. User klik "Simpan" $\rightarrow$ memanggil fungsi `save()` di Livewire.
3. Fungsi `save()` menjalankan validasi (`$this->validate()`).
4. Jika valid, data dikirim ke `PostService->createNewPost()`.
5. Service memanipulasi data (membuat slug otomatis).
6. Service mengirim data matang ke `PostRepository->create()`.
7. Repository menyimpannya ke database via Eloquent Model `Post`.

---

**Kelebihan Arsitektur Ini:**
Jika suatu saat Anda ingin membuat Mobile App dan membutuhkan API, logika aplikasi Anda (Service & Repository) tetap utuh dan bisa langsung digunakan ulang oleh API Controller tanpa perlu menulis ulang atau mengubah kode Livewire.