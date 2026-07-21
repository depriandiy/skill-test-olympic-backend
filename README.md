# Skill Test Olympic Backend

Laravel REST API untuk test Full Stack Developer HR/recruitment. Frontend dibuat sebagai project terpisah.

## Stack

- PHP 8.4
- Laravel 13
- Laravel Sanctum bearer token
- PostgreSQL untuk runtime
- SQLite in-memory untuk automated tests
- Laravel Excel untuk import/export kandidat
- L5 Swagger + `docs/api/openapi.yaml` untuk dokumentasi API

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Default `.env` memakai PostgreSQL lokal:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=skill_test_olympic_backend
DB_USERNAME=depriandiyanto
DB_PASSWORD=
```

Buat database jika belum ada:

```bash
createdb skill_test_olympic_backend
```

Lalu jalankan migration dan seeder:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Seeder membuat akun demo untuk setiap role:

| Role | Nama | Email | Password |
| --- | --- | --- | --- |
| HR Admin | Aditya Rahman | `admin@example.com` | `password` |
| HR Staff | Nadia Putri | `staff.nadia@example.com` | `password` |
| Interviewer | Raka Pratama | `interviewer.raka@example.com` | `password` |
| Candidate | Alya Maharani | `candidate.alya@example.com` | `password` |

Seeder juga membuat data demo realistis:

- 18 kandidat dengan status `new`, `screening`, `interview`, `accepted`, dan `rejected`
- 8 lowongan lintas department Technology, Human Resources, Recruitment, Finance, Product, dan Operations
- 20 riwayat lamaran yang menghubungkan kandidat dengan lowongan

Akun Candidate terhubung ke biodata kandidat Alya Maharani, sehingga bisa dipakai untuk menguji batas akses "melihat data miliknya".

## Authentication

Login:

```http
POST /api/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}
```

Gunakan token dari response:

```http
Authorization: Bearer <access_token>
```

Logout:

```http
POST /api/logout
```

## API Endpoints

Authentication:

- `POST /api/login`
- `POST /api/logout`
- `GET /api/me`

Dashboard:

- `GET /api/dashboard`

Candidates:

- `GET /api/candidates?search=&status=&sort=&direction=&page=`
- `POST /api/candidates`
- `GET /api/candidates/{candidate}`
- `PUT /api/candidates/{candidate}`
- `DELETE /api/candidates/{candidate}`
- `GET /api/candidates/export`
- `POST /api/candidates/import`

Jobs:

- `GET /api/jobs`
- `POST /api/jobs`
- `GET /api/jobs/{job}`
- `PUT /api/jobs/{job}`
- `DELETE /api/jobs/{job}`

Applications:

- `GET /api/applications`
- `GET /api/application`
- `POST /api/applications`
- `GET /api/applications/{application}`
- `PUT /api/applications/{application}`

`GET /api/application` disediakan sebagai alias kompatibilitas karena PDF menulis endpoint singular.

## RBAC

- HR Admin: semua akses.
- HR Staff: Dashboard, CRUD Candidate, import/export Candidate, dan CRUD Job.
- Interviewer: read Candidate termasuk detail kandidat.
- Candidate: melihat lamaran miliknya dan membuat lamaran atas nama sendiri.

Catatan akses backend:

- HR Staff tidak mengakses Application management.
- Interviewer tidak mengakses Dashboard, Job, atau Application.
- Candidate dapat membaca Job aktif untuk kebutuhan dropdown apply, tetapi menu Job tidak ditampilkan di frontend.
- Update status Application otomatis menyinkronkan status Candidate.

Authorization dilakukan di backend. Frontend boleh menyembunyikan menu, tetapi bukan sumber keamanan.

## Security

- Password di-hash lewat Laravel Hash.
- Query memakai Eloquent/query builder sehingga parameter dibinding dan terlindungi dari SQL injection.
- Semua mutating endpoint memakai Form Request validation.
- Endpoint privat memakai `auth:sanctum`.
- Role dicek di backend sebelum operasi berjalan.
- Soft delete memakai `deleted_at`, bukan `is_delete` manual.
- CORS dikonfigurasi lewat `FRONTEND_URL`.
- Rate limiting aktif untuk login dan API.
- Upload foto kandidat divalidasi sebagai image maksimal 2 MB.

## Bonus

- Dokumentasi API: `docs/api/openapi.yaml`
- Swagger package: `darkaonline/l5-swagger`
- Dokumentasi pengguna/developer: README ini
- Upload Foto Kandidat: field `photo` di `POST/PUT /api/candidates`
- Export Excel: `GET /api/candidates/export`
- Import Excel: `POST /api/candidates/import`
- CI/CD: `.github/workflows/ci.yml`

Bonus frontend seperti infinite scroll, dark mode, dan desain sesuai KV akan dikerjakan di project frontend.

## Tests

```bash
php artisan test
```

Test memakai SQLite in-memory dari `phpunit.xml`, jadi tidak membutuhkan PostgreSQL lokal.
