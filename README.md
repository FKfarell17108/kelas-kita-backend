# 📚 KelasKita Backend API

Sistem backend untuk platform pembelajaran **KelasKita**, yang menyediakan fitur:
- Kelas daring (membuat kelas, bergabung, tugas, pengumpulan, penilaian)
- Bootcamp (materi/video/soal, progress tracking)
- Forum komunitas (diskusi & komentar)
- Manajemen user berbasis role (Admin, Guru, Siswa)

Dibangun menggunakan **PHP Native + MySQL**, tanpa framework eksternal.

---

## 🔧 Teknologi
- PHP Native (tanpa framework)
- MySQL (phpMyAdmin via XAMPP)
- JSON Web Token (JWT) untuk autentikasi
- Postman untuk testing fungsionalitas
- Role-based access: `admin`, `teacher`, `student`

---

## 🚀 Setup

### 1. Clone repository
```bash
git clone https://github.com/username/kelas-kita-backend.git
cd kelaskita-backend
```

### 2. Buat database
- Import file SQL yang telah disediakan: `kelaskita.sql`
- Pastikan database MySQL lokal aktif (bisa menggunakan XAMPP atau sejenis)

### 3. Konfigurasi environment

Buat file `.env` di root folder, atau duplikat dari `.env.example`

```env
DB_HOST=localhost
DB_NAME=kelaskita
DB_USER=root
DB_PASS=
BASE_URL=http://localhost/kelaskita-backend
```

### 4. Jalankan server

Bisa dijalankan melalui local PHP built-in server:

```bash
php -S localhost:8000
```

Atau taruh di folder `htdocs` jika menggunakan XAMPP:

```bash
http://localhost/kelas-kita-backend
```

## 📁 Struktur Folder

```
kelas-kita-be/
├── app/
│   ├── config/                        # Koneksi database
│   ├── controllers/                   # Logic tiap fitur utama
│   ├── helpers/                       # JWT, role, json response
│   ├── middleware/                    # JWT middleware
│   └── routes/                        # Routing utama
├── public/                            # Entry point index.php
├── sql/                               # File init database
├── .htaccess                          # Redirect semua ke index.php
├── .gitignore
├── .env.example                       # Contoh konfigurasi environment
├── KelasKita.postman_collection.json  # Postman Collection
└── README.md
```

---

## 🚀 Cara Menjalankan (XAMPP)

1. Clone repo ini ke folder `htdocs`
2. Import file `sql/kelaskita.sql` ke phpMyAdmin
3. Jalankan Apache & MySQL
4. Akses dari browser:
   ```
   http://localhost/kelas-kita-be/public/
   ```
5. Gunakan Postman untuk tes endpoint:
   - Login & register akan mengembalikan `token`
   - Gunakan token itu untuk `Authorization: Bearer <token>` di endpoint lain

---

## 🔐 Autentikasi

| Endpoint      | Method | Deskripsi                |
|---------------|--------|--------------------------|
| /api/register | POST   | Daftar akun              |
| /api/login    | POST   | Login dan dapatkan token |
| /api/profile  | GET    | Lihat data pengguna dari token |

---

## 🏫 Fitur Kelas

| Endpoint                          | Method | Akses     |
|----------------------------------|--------|-----------|
| /api/classes                     | POST   | Guru      |
| /api/classes                     | GET    | Semua     |
| /api/classes/:id/join            | POST   | Siswa     |
| /api/classes/:id/members         | GET    | Guru      |
| /api/classes/:id/assignments     | POST   | Guru      |
| /api/classes/:id/assignments     | GET    | Semua     |
| /api/assignments/:id/submit      | POST   | Siswa     |
| /api/assignments/:id/submissions | GET    | Guru      |
| /api/submissions/:id/grade       | POST   | Guru      |

---

## 🎓 Bootcamp & Modul

| Endpoint                                         | Method | Akses     |
|--------------------------------------------------|--------|-----------|
| /api/bootcamps                                   | POST   | Guru      |
| /api/bootcamps                                   | GET    | Semua     |
| /api/bootcamps/:id/join                          | POST   | Siswa     |
| /api/bootcamps/:id/modules                       | POST   | Guru      |
| /api/bootcamps/:id/modules                       | GET    | Semua     |
| /api/bootcamps/:id/progress                      | GET    | Siswa     |
| /api/bootcamps/:id/progress/all                  | GET    | Guru      |
| /api/bootcamps/:id/progress/export               | GET    | Guru      |
| /api/modules/:id/questions                       | GET    | Siswa     |
| /api/questions/:id/answer                        | POST   | Siswa     |
| /api/modules/:id/results                         | GET    | Siswa     |

---

## 💬 Forum Komunitas

| Endpoint                           | Method | Akses     |
|------------------------------------|--------|-----------|
| /api/forum/posts                   | POST   | Semua     |
| /api/forum/posts                   | GET    | Semua     |
| /api/forum/posts/:id/comments      | POST   | Semua     |
| /api/forum/posts/:id/comments      | GET    | Semua     |

---

## ⚠️ Catatan untuk Frontend

- Selalu kirim header:
  ```
  Authorization: Bearer <token>
  Content-Type: application/json
  ```
- Format response selalu dalam `application/json`
- Gunakan endpoint `/api/profile` untuk ambil detail user & role
- Semua ID (kelas, modul, dll.) digunakan sebagai parameter di URL

---

## ✨ Kontributor
- Backend Developer: Farell Kurniawan
