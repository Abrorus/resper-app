# RESPER (Restaurant Helper)

Aplikasi manajemen operasional restoran untuk mata kuliah Perancangan dan Pengembangan Perangkat Lunak (PPPL), Kelompok 6, Sistem Informasi ITS.

RESPER mengintegrasikan manajemen akun, pemesanan (offline dan pihak ketiga), inventaris, staf, dan keuangan dalam satu sistem. Acuan desain dan use case adalah dokumen **Critical Design Review (CDR) RESPER v1.0**.

---

## Daftar Isi

1. [Tim dan Pembagian Package](#1-tim-dan-pembagian-package)
2. [Teknologi yang Disepakati](#2-teknologi-yang-disepakati)
3. [Menyiapkan Laptop (Sekali Saja)](#3-menyiapkan-laptop-sekali-saja)
4. [Menjalankan Proyek Pertama Kali](#4-menjalankan-proyek-pertama-kali)
5. [Menjalankan Proyek Sehari-hari](#5-menjalankan-proyek-sehari-hari)
6. [Struktur Folder dan Konsep MVC](#6-struktur-folder-dan-konsep-mvc)
7. [Aturan Kode dan Tampilan](#7-aturan-kode-dan-tampilan)
8. [Alur Kerja Git](#8-alur-kerja-git)
9. [Troubleshooting](#9-troubleshooting)
10. [Keputusan yang Belum Final](#10-keputusan-yang-belum-final)

---

## 1. Tim dan Pembagian Package

| Anggota | NRP | Package | Branch |
|---|---|---|---|
| Krisna Agung Prabowo | 5026231144 | Keuangan | `feature/keuangan` |
| Abrorus Shobah | 5026231145 | Inventaris | `feature/inventaris` |
| Audrey Sophia M.L. | 5026241014 | Staff | `feature/staff` |
| Fathur Ramadhani N.R. | 5026241091 | Akun | `feature/akun` |
| Tessalonika Samosir | 5026241137 | Pemesanan | `feature/pemesanan` |

**Role pengguna** (sesuai CDR):

- **Manajer Restoran**: otoritas tertinggi, mengelola inventaris, staf, dan keuangan.
- **Kasir**: mengeksekusi transaksi harian dan mencatat transaksi penjualan/pengeluaran.
- **Staff**: akses terbatas pada jadwal kerja dan notifikasi pesanan untuk dapur.

**Ketergantungan antarpackage** yang perlu diingat semua anggota: semua halaman membutuhkan login dan pembatasan role dari package **Akun**. Package **Pemesanan** mengurangi stok di **Inventaris** dan mencatat penjualan di **Keuangan**. Karena itu, perubahan struktur tabel yang dipakai bersama harus didiskusikan dulu di grup.

---

## 2. Teknologi yang Disepakati

| Komponen | Pilihan | Keterangan |
|---|---|---|
| Bahasa | PHP 8.5 | Minimal 8.4.1, karena `composer.lock` memakai komponen Symfony 8 (dicek otomatis oleh `vendor/composer/platform_check.php`) |
| Framework | Laravel 13 (terkunci di 13.39) | Pola MVC, template Blade |
| Database | MySQL / MariaDB | Struktur tabel hanya lewat migration |
| CSS | Tailwind CSS 4 (terkunci di 4.3) | Satu framework CSS untuk seluruh aplikasi |
| Build tool frontend | Vite 8 (terkunci di 8.3) | Mengolah CSS, JS, dan font |
| Dependensi | Composer (PHP), npm (Node.js) | **Node.js minimal 22.13**, karena `concurrently` dan `@laravel/multiplex` (dipakai `composer run dev`) mensyaratkannya |
| Version control | Git + GitHub | Repo `Abrorus/resper-app` |
| Pengelola database | Bebas (DBeaver, phpMyAdmin, TablePlus) | Hanya untuk melihat data, bukan mengubah struktur |

> **Penting:** XAMPP versi terakhir hanya membawa PHP 8.2.12, sehingga **tidak bisa menjalankan proyek ini**. XAMPP boleh tetap dipakai untuk MySQL/MariaDB-nya saja, tetapi PHP harus dari sumber lain (lihat bagian 3).

---

## 3. Menyiapkan Laptop (Sekali Saja)

Cek dulu versi yang sudah terpasang:

```bash
php -v
composer -V
node -v
npm -v
```

`php -v` harus menunjukkan 8.4.1 ke atas (tim memakai 8.5). `composer -V` juga menampilkan versi PHP yang dipakai Composer, pastikan sama. `node -v` harus 22.13 ke atas. Kalau Node.js masih versi lama, perbarui dulu (misalnya lewat nvm, atau installer LTS dari nodejs.org).

### Windows

Pilih salah satu:

- **Laragon** (paling mirip XAMPP, sudah termasuk MySQL). Unduh PHP 8.5 (Thread Safe, x64) dari windows.php.net, ekstrak ke `laragon\bin\php`, lalu pilih versinya lewat menu Laragon > PHP > Version.
- **Laravel Herd** (paling mudah untuk PHP, sudah termasuk Composer). Versi gratisnya tidak menyertakan MySQL, jadi MySQL bisa tetap dari XAMPP atau Laragon.

Pastikan ekstensi berikut aktif di `php.ini`: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `zip`, `intl`, `curl`.

Pasang juga **Node.js 22 LTS** dari nodejs.org dan **Git** dari git-scm.com.

### Ubuntu

Ubuntu 26.04 (Resolute) tidak lagi didukung PPA `ondrej/php` (`apt update` akan error 404 "does not have a Release file"). Gunakan repositori `packages.sury.org`. Kalau sebelumnya sudah sempat menambahkan PPA, hapus dulu file sumbernya:

```bash
sudo rm -f /etc/apt/sources.list.d/ondrej-ubuntu-php-resolute.sources
```

Lalu tambahkan repositori Sury dan pasang PHP:

```bash
sudo apt install -y curl ca-certificates
curl -sSLo /tmp/debsuryorg-archive-keyring.deb https://packages.sury.org/debsuryorg-archive-keyring.deb
sudo dpkg -i /tmp/debsuryorg-archive-keyring.deb

cat <<EOF | sudo tee /etc/apt/sources.list.d/sury-php.sources
Types: deb
URIs: https://packages.sury.org/php/
Suites: $(lsb_release -cs)
Components: main
Signed-By: /usr/share/keyrings/debsuryorg-archive-keyring.gpg
EOF

sudo apt update
sudo apt install php8.5 php8.5-{cli,common,mbstring,xml,curl,zip,bcmath,mysql,sqlite3,intl}
sudo update-alternatives --set php /usr/bin/php8.5
```

Untuk Ubuntu 24.04 ke bawah, cara lama dengan `sudo add-apt-repository ppa:ondrej/php` masih bisa dipakai. Kalau ada beberapa versi PHP terpasang (misalnya 8.2 dan 8.5), pilih versi aktif dengan `sudo update-alternatives --config php`.

Pasang Composer lewat installer resmi, **bukan** `apt install composer` (versi apt ikut menarik PHP bawaan Ubuntu):

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
```

Pasang MariaDB, Git, dan unzip:

```bash
sudo apt install -y mariadb-server git unzip
sudo systemctl enable --now mariadb
```

### Akses GitHub

Minta pemilik repo menambahkan akun GitHub-mu sebagai **collaborator**. Untuk clone dan push, gunakan SSH:

```bash
ssh-keygen -t ed25519 -C "email-github-mu"
cat ~/.ssh/id_ed25519.pub
```

Salin outputnya ke GitHub > Settings > SSH and GPG keys > New SSH key. Atur juga identitas Git, pastikan email **sama dengan email akun GitHub** supaya commit tercatat sebagai kontribusimu:

```bash
git config --global user.name "Nama Kamu"
git config --global user.email "email-github-mu"
```

---

## 4. Menjalankan Proyek Pertama Kali

### a. Clone dan pasang dependensi

```bash
git clone git@github.com:Abrorus/resper-app.git
cd resper-app
composer install
npm install
```

Gunakan `composer install`, **bukan** `composer update`. `install` mengikuti versi yang dikunci di `composer.lock`, sehingga semua anggota memakai versi package yang sama persis.

### b. Siapkan file `.env`

File `.env` tidak ikut di-clone (sengaja dikecualikan dari Git karena berisi password). Buat dari contoh:

```bash
cp .env.example .env
php artisan key:generate
```

### c. Buat database dan user

**Ubuntu (MariaDB dari apt):** user `root` hanya bisa login lewat `sudo mysql` di terminal, tidak bisa dari DBeaver atau Laravel. Buat user khusus untuk proyek:

```bash
sudo mysql
```

```sql
CREATE DATABASE IF NOT EXISTS resper;
CREATE USER IF NOT EXISTS 'laravel'@'localhost' IDENTIFIED BY 'ganti_password_ini';
GRANT ALL PRIVILEGES ON resper.* TO 'laravel'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

**Windows (XAMPP/Laragon):** buat database `resper` lewat phpMyAdmin, HeidiSQL, atau DBeaver. User bawaannya `root` tanpa password.

### d. Atur koneksi database di `.env`

Ubah bagian database menjadi seperti ini. **Hapus tanda `#` di depan setiap baris**, karena baris yang diawali `#` dianggap komentar dan diabaikan Laravel.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=resper
DB_USERNAME=laravel
DB_PASSWORD=ganti_password_ini
```

Pengguna XAMPP/Laragon: `DB_USERNAME=root` dan `DB_PASSWORD=` dikosongkan. Kalau password mengandung spasi atau `#`, apit dengan tanda kutip, misalnya `DB_PASSWORD="abc#123"`.

### e. Buat tabel dan data contoh

```bash
php artisan config:clear
php artisan migrate --seed
```

Seeder membuat akun uji: email `test@example.com`, password `password`.

### f. Jalankan aplikasi

Cara paling praktis, cukup **satu terminal**:

```bash
composer run dev
```

Perintah ini menjalankan `php artisan dev`, yang menyalakan sekaligus web server (`php artisan serve`), Vite (`npm run dev`), queue worker, dan penampil log (`pail`, khusus Linux/macOS). Hentikan semuanya dengan `Ctrl+C`.

Alternatif kalau `composer run dev` error (biasanya karena Node.js di bawah 22.13), buka **dua terminal**:

```bash
# Terminal 1
npm run dev
```

```bash
# Terminal 2
php artisan serve
```

Buka `http://127.0.0.1:8000`. Halaman login ada di `http://127.0.0.1:8000/login`.

> **Tentang `composer run setup`:** repo juga punya skrip `setup` (install dependensi, membuat `.env`, `key:generate`, `migrate`, lalu `npm run build`). Skrip ini **belum disarankan** untuk anggota baru, karena `.env.example` masih default SQLite sehingga langkah `migrate` di dalamnya akan gagal. Gunakan langkah manual a sampai e di atas sampai `.env.example` diubah ke MySQL (lihat bagian 10).

---

## 5. Menjalankan Proyek Sehari-hari

Setiap kali mulai bekerja, tarik perubahan terbaru dari anggota lain:

```bash
git switch main
git pull
composer install
npm install
php artisan migrate
```

`composer install`, `npm install`, dan `migrate` perlu dijalankan ulang karena bisa saja ada package atau tabel baru dari anggota lain. Setelah itu kembali ke branch-mu dan gabungkan `main` terbaru:

```bash
git switch feature/nama-package
git merge main
```

Lalu jalankan `composer run dev` (atau `npm run dev` dan `php artisan serve` di dua terminal) seperti biasa.

---

## 6. Struktur Folder dan Konsep MVC

### Folder yang sering disentuh

| Folder / File | Isi |
|---|---|
| `routes/web.php` | Daftar URL aplikasi dan controller yang menanganinya |
| `app/Http/Controllers/` | Controller (logika yang menghubungkan request, data, dan tampilan) |
| `app/Models/` | Model Eloquent (kelas untuk membaca/menulis data tabel) |
| `resources/views/` | Tampilan Blade (`.blade.php`) |
| `resources/css/app.css` | CSS utama (Tailwind) |
| `database/migrations/` | Definisi struktur tabel |
| `database/seeders/` | Data contoh |
| `bootstrap/app.php` | Konfigurasi aplikasi, termasuk pendaftaran middleware |

### Folder yang jangan diubah manual

`vendor/` (hasil `composer install`), `node_modules/` (hasil `npm install`), `public/build/` dan `public/hot` (hasil Vite), `storage/` (log dan cache).

### Alur satu request (MVC)

```
Browser → routes/web.php → (middleware) → Controller → Model → Database
                                              ↓
Browser ←───────────── HTML ←──────────── View (Blade)
```

Contoh: Manajer membuka `/inventaris`. Route mengarahkan ke `InventoryController@index`, controller mengambil data lewat model `Inventory`, lalu mengirim data ke view `inventaris/index.blade.php` untuk ditampilkan.

**Migration vs model:** migration mendefinisikan struktur tabel (kolom dan tipenya) dan dijalankan dengan `php artisan migrate`. Model adalah kelas PHP untuk membaca dan menulis data ke tabel itu dari kode. Menambah kolom berarti membuat migration baru, lalu mendaftarkan kolomnya di model.

**Catatan Laravel 13:** konfigurasi model memakai *attribute* PHP, misalnya `#[Fillable(['name', 'email'])]`. Tutorial lama memakai `protected $fillable = [...]`, fungsinya sama. Middleware tidak lagi didaftarkan di `app/Http/Kernel.php` (file itu sudah tidak ada), melainkan di `bootstrap/app.php` bagian `withMiddleware`.

### Perintah artisan yang berguna

```bash
php artisan make:controller Inventaris/InventoryController
php artisan make:model Inventory -m      # -m sekaligus membuat migration
php artisan make:migration add_role_to_users_table
php artisan make:middleware EnsureUserHasRole
php artisan route:list                   # lihat semua route
php artisan migrate:status               # cek migration yang sudah jalan
```

---

## 7. Aturan Kode dan Tampilan

### Database

- **Struktur tabel hanya boleh diubah lewat file migration.** Jangan membuat atau mengubah tabel langsung lewat DBeaver/phpMyAdmin, karena perubahannya tidak ikut ke laptop anggota lain.
- Jangan mengedit migration yang sudah di-merge ke `main`. Kalau perlu mengubah tabel, buat migration baru.
- Tabel `users` dikelola package Akun. Kolom tambahan untuk `users` dikoordinasikan dengan penanggung jawab Akun.
- Jangan saling kirim file `.sql`. Cukup jalankan `php artisan migrate --seed`.

### MVC

- Query database hanya di controller atau model, **tidak pernah di view**.
- View tidak berisi logika bisnis (menghitung stok, memeriksa role, dan sejenisnya).

### Penamaan

- View dikelompokkan per package: `resources/views/auth/`, `resources/views/inventaris/`, `resources/views/pemesanan/`, `resources/views/staff/`, `resources/views/keuangan/`.
- Controller juga per package: `app/Http/Controllers/Auth/`, `app/Http/Controllers/Inventaris/`, dan seterusnya.
- Nama view memetakan langsung ke path: `view('auth.login')` berarti `resources/views/auth/login.blade.php`.
- Beri nama pada setiap route, misalnya `->name('inventaris.index')`, lalu panggil dengan `route('inventaris.index')` daripada menulis URL manual.

### Tampilan (UI)

- Acuan desain: **storyboard di dokumen CDR**.
- CSS: **Tailwind saja**. Jangan mencampur dengan Bootstrap atau CSS framework lain.
- Font: **Poppins**.
- Warna utama: hijau **`#27AE60`** (hover `#229954`).
- Tombol utama berbentuk pil (`rounded-full`), input bersudut `rounded-lg` dengan border abu-abu.
- Elemen yang dipakai berulang (tombol, input, modal) sebaiknya dibuat sebagai **Blade component** di `resources/views/components/`, lalu dipakai dengan `<x-button>`, supaya tampilan seluruh halaman seragam.
- Gambar disimpan di `public/images/` dan dipanggil dengan `{{ asset('images/nama-file.jpg') }}`. Gunakan gambar berlisensi bebas (misalnya Unsplash).

---

## 8. Alur Kerja Git

### Aturan utama

1. **Jangan pernah push langsung ke `main`.** `main` harus selalu berisi kode yang bisa dijalankan.
2. Setiap anggota bekerja di **branch package-nya** (lihat tabel di bagian 1). Untuk pekerjaan bersama, gunakan branch terpisah, misalnya `feature/database-schema`.
3. Semua perubahan masuk ke `main` lewat **Pull Request (PR)** dan direview minimal satu anggota lain.
4. **Jangan commit `.env`.** File ini sudah dikecualikan di `.gitignore`.
5. **Commit `composer.lock` dan `package-lock.json`** setiap kali berubah, agar versi package semua anggota sama.
6. Sebaiknya **hanya satu orang** yang menambah atau memperbarui dependensi (`composer require`, `npm install nama-package`), lalu memberi tahu grup.

### Langkah membuat dan mengirim perubahan

```bash
# 1. Mulai dari main terbaru
git switch main
git pull

# 2. Buat branch (sekali saja per package)
git switch -c feature/nama-package

# 3. Cek file yang berubah, pastikan tidak ada .env
git status

# 4. Tambahkan file yang relevan, cek, lalu commit
git add path/ke/file
git diff --staged --stat
git commit -m "Tambah halaman daftar inventaris"

# 5. Push branch
git push -u origin feature/nama-package
```

Setelah push, buka tautan yang muncul di terminal (atau tab **Pull requests** di GitHub) untuk membuat PR dari branch-mu ke `main`.

### Pull Request

- **Judul** singkat dan jelas, misalnya "Halaman login sesuai storyboard CDR".
- **Deskripsi** berisi apa yang sudah dikerjakan dan apa yang belum.
- Minta minimal satu anggota mereview. Reviewer mengecek apakah kode bisa jalan dan sesuai aturan di README ini.
- Kalau ada permintaan perbaikan, cukup commit dan push lagi ke branch yang sama. PR akan ter-update otomatis.
- **Jangan merge PR sendiri** sebelum disetujui.
- Setelah PR di-merge, semua anggota menjalankan langkah di bagian 5.

### Pesan commit

Gunakan bahasa Indonesia, kalimat perintah, dan spesifik:

- Baik: `Tambah validasi email pada form login`
- Kurang baik: `update`, `fix`, `coba lagi`

---

## 9. Troubleshooting

| Gejala | Penyebab | Solusi |
|---|---|---|
| `composer install` gagal dengan error versi PHP | PHP di bawah 8.4 | Pasang PHP 8.5 (bagian 3) |
| `500` + "No application encryption key has been specified" | File `.env` belum ada atau `APP_KEY` kosong | `cp .env.example .env` lalu `php artisan key:generate` |
| "Database file at path [...database.sqlite] does not exist" | `.env` masih `DB_CONNECTION=sqlite` atau baris DB masih diawali `#` | Perbaiki `.env` (bagian 4d), simpan file, lalu `php artisan config:clear` dan restart `php artisan serve` |
| DBeaver: "Connection refused" | Server MySQL/MariaDB belum berjalan | Ubuntu: `sudo systemctl enable --now mariadb`. Windows: start MySQL di XAMPP/Laragon |
| DBeaver: "Access denied for user 'root'@'localhost'" (Ubuntu) | `root` MariaDB di Ubuntu hanya bisa lewat `sudo mysql` | Buat user khusus (bagian 4c) |
| "Table 'resper.sessions' doesn't exist" | Koneksi MySQL sudah benar, tetapi tabel belum dibuat | `php artisan migrate` |
| "View [x.y] not found" | Nama atau lokasi file view tidak sesuai | `view('auth.login')` harus berada di `resources/views/auth/login.blade.php` |
| "Vite manifest not found at .../public/build/manifest.json" | Vite tidak sedang berjalan | Jalankan `composer run dev`, atau `npm run dev` di terminal terpisah |
| `composer run dev` gagal, menyebut `@laravel/multiplex` atau `concurrently` | Node.js di bawah 22.13 | Perbarui Node.js, atau pakai cara dua terminal |
| Perubahan `.env` tidak berpengaruh | File belum disimpan atau konfigurasi ter-cache | Simpan file (`Ctrl+S`), `php artisan config:clear`, restart server |
| Tabel di DBeaver tidak muncul setelah migrate | Tampilan DBeaver belum diperbarui | Klik kanan database > Refresh |

Untuk error lain, cek file log di `storage/logs/laravel.log`, terutama baris paling bawah.

### Mengganti password user database

```bash
sudo mysql
```

```sql
ALTER USER 'laravel'@'localhost' IDENTIFIED BY 'password_baru';
FLUSH PRIVILEGES;
EXIT;
```

Lalu perbarui password di DBeaver dan di `DB_PASSWORD` pada `.env`, kemudian jalankan `php artisan config:clear`.

---

## 10. Keputusan yang Belum Final

Hal-hal berikut perlu disepakati di grup. Setelah diputuskan, perbarui bagian ini.

### Konfigurasi repo

- [ ] Ubah `.env.example` agar default ke MySQL (`DB_CONNECTION=mysql`, `DB_DATABASE=resper`), supaya anggota baru tidak perlu menebak konfigurasi.
- [ ] Tambahkan pengunci versi PHP di `composer.json` bagian `config`, supaya Composer hanya memilih package yang juga berjalan di PHP 8.4:
  ```json
  "platform": {
      "php": "8.4.1"
  }
  ```
- [ ] Jadikan Poppins font global. Saat ini font bawaan proyek (Instrument Sans) dimuat lewat bagian `fonts` di `vite.config.js` dan dipakai di `@theme` pada `resources/css/app.css`, sedangkan Poppins baru dimuat manual di halaman login. Ganti keduanya ke Poppins supaya semua halaman otomatis memakainya.
- [ ] Buat migration bersama untuk entitas inti dari Domain Model CDR (users + role, inventories, orders, order_items, staff, schedules, transactions) di branch `feature/database-schema`, lalu merge ke `main` sebelum fitur lain berkembang jauh.

### Celah logika di spesifikasi package Akun (CDR)

- [ ] **Siapa yang boleh registrasi dan bagaimana role ditentukan?** Use case Register tidak menyebut penentuan role. Usulan: registrasi publik hanya untuk Manajer, akun Kasir dan Staff dibuat Manajer lewat package Staff.
- [ ] **OTP dikirim ke email atau nomor telepon?** Basic flow menyebut email, alternate flow "Kirim Ulang" menyebut nomor telepon. Usulan: email (gratis dan didukung bawaan Laravel). Selama pengembangan, `MAIL_MAILER=log` membuat isi email tercatat di `storage/logs/laravel.log`.
- [ ] **Akun terblokir dipulihkan bagaimana?** Spesifikasi menyebut blokir "sementara" setelah 5 kali gagal, tetapi tidak ada use case pemulihan. Usulan: blokir otomatis terbuka setelah 15 menit.
- [ ] Data registrasi disimpan sementara sampai OTP terverifikasi (sesuai CDR), sehingga butuh tabel atau session penampung.

### Lain-lain

- [ ] Footer storyboard tertulis "Rasper by kelompok 4". Di aplikasi dipakai "RESPER by Kelompok 6".
- [ ] File logo RESPER asli (SVG/PNG) disimpan di `public/images/` untuk menggantikan logo sementara.

---

## Progres

| Package | Halaman | Status |
|---|---|---|
| Akun | Login (`/login`) | Tampilan selesai, validasi email/password berjalan. Belum: blokir 5x gagal, redirect sesuai role |
| Inventaris | | |
| Staff | | |
| Pemesanan | | |
| Keuangan | | |
