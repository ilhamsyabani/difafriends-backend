# DifaFriends — Dokumentasi Project Lengkap
> Platform Edukasi Inklusif untuk Anak Difabel
> Versi Dokumentasi: 1.3 | April 2026

---

## PETUNJUK PENGGUNAAN DOKUMEN INI

Dokumen ini dibuat untuk membantu AI assistant memahami konteks, arsitektur, dan status project DifaFriends secara menyeluruh. Paste seluruh dokumen ini di awal conversation dengan AI sebelum mengajukan pertanyaan teknis.

---

## 1. RINGKASAN PROJECT

### Apa itu DifaFriends?
DifaFriends adalah platform LMS (Learning Management System) marketplace berbasis web untuk pendidikan inklusif anak-anak difabel di Indonesia. Platform ini menyediakan dua produk utama:

- **Produk A — Kelas Online**: Kursus video yang dibuat oleh instruktur terverifikasi, mencakup terapi wicara, terapi sensori, akademik, motorik, dan sosial-emosional.
- **Produk B — Guru Pendamping (Companion)**: Booking sesi tatap muka atau online dengan guru pendamping profesional.

### Tech Stack
```
Backend      : Laravel 12 (PHP 8.4)
Frontend     : Vue 3 + Inertia.js v2 (SPA via Inertia)
Styling      : Tailwind CSS v4
Database     : SQLite (dev) / PostgreSQL (prod) — database-agnostic
Auth         : Laravel Fortify (dengan 2FA/TOTP)
Payment      : Midtrans Snap + Scalev Webhook
Typed Routes : Laravel Wayfinder v0
Queue        : Laravel Queue (database driver, production: Redis)
Storage      : Laravel Storage (public disk, production: Cloudflare R2/S3)
Testing      : Pest PHP v4 (123 tests)
Code Style   : Laravel Pint + Prettier + ESLint
Monitoring   : Laravel Pulse v1
Activity Log : Spatie Activity Log v5
```

### Struktur Project
```
Project adalah MONOREPO — satu folder Laravel dengan Vue terintegrasi via Inertia.js
Tidak ada pemisahan frontend/backend repository.

/backend (root project)
├── app/
│   ├── Actions/Fortify/     ← Auth actions (CreateNewUser, UpdateUserProfileInformation)
│   ├── Concerns/            ← Traits (PasswordValidationRules, ProfileValidationRules)
│   ├── Enums/               ← PHP 8.1 Enums (10 enums)
│   ├── Http/Controllers/    ← Controllers per domain
│   │   ├── Admin/           ← Admin panel controllers
│   │   ├── Instructor/      ← Instructor panel controllers
│   │   ├── Companion/       ← Companion panel controllers
│   │   └── (root)           ← Public & user controllers
│   ├── Models/              ← Eloquent models (15+ models)
│   ├── Notifications/       ← Laravel Notifications
│   ├── Providers/           ← AppServiceProvider, FortifyServiceProvider
│   └── Services/            ← MidtransService, OrderService
├── database/
│   ├── migrations/          ← 19 migrations
│   ├── seeders/             ← UserSeeder, CategorySeeder, CourseSeeder
│   └── factories/           ← UserFactory, CourseFactory
├── resources/js/
│   ├── components/          ← Reusable Vue components
│   │   ├── AppLogo.vue
│   │   ├── AppSidebar.vue   ← Dynamic sidebar per role
│   │   ├── NotificationBell.vue
│   │   └── ui/              ← ShadCN-like UI components
│   ├── layouts/
│   │   ├── AppLayout.vue    ← Authenticated layout (dengan sidebar)
│   │   ├── GuestLayout.vue  ← Public layout (navbar + footer)
│   │   └── AuthLayout.vue   ← Auth pages layout (login, register)
│   └── pages/
│       ├── admin/           ← Admin panel pages
│       ├── instructor/      ← Instructor panel pages
│       ├── companion/       ← Companion panel pages
│       ├── user/            ← User pages (orders, enrollments)
│       ├── companions/      ← Public companion list & profile
│       ├── courses/         ← Public course catalog & detail
│       ├── learn/           ← Learning page (fullscreen)
│       ├── bookings/        ← Booking detail page
│       └── auth/            ← Login, register pages
└── routes/web.php           ← Semua routes
```

---

## 2. DATABASE SCHEMA

### Enum Classes (app/Enums/)

```php
Roles::class            → user, instructor, companion, admin
OrderStatus::class      → pending, paid, expired, cancelled, refunded
PaymentStatus::class    → pending, settlement, capture, expired, expire, cancel, fraud, deny
                          (capture & expire ditambahkan agar cocok dengan status raw Midtrans)
BookingStatus::class    → pending, confirmed, completed, cancelled
CourseStatus::class     → draft, review, published, archived
EnrollmentStatus::class → active, completed, expired
SessionType::class      → online, offline
ResourceType::class     → pdf, video, link, file
LectureType::class      → video, text, quiz
DayOfWeek::class        → 0-6 (int, Minggu=0, Sabtu=6)
```

### Tabel Database (24 Migrations)

#### users
```
id, first_name, last_name, email, password
role (enum: user|instructor|companion|admin)
phone, city, bio, photo, gender
is_active (boolean, default: true)
email_verified_at, remember_token
created_at, updated_at, deleted_at (softDeletes)
```

#### categories
```
id, name, slug
parent_id (FK → categories.id, nullable — null = kategori utama)
description, is_active (boolean)
sort_order (integer)
created_at, updated_at
```

#### courses
```
id, instructor_id (FK → users.id)
category_id (FK → categories.id)
title, slug, description
thumbnail (path), preview_video (url)
price, discount_price (nullable)
duration_minutes, has_certificate (boolean)
prerequisites (text, nullable)
is_featured (boolean)
status (enum: CourseStatus)
created_at, updated_at, deleted_at (softDeletes)
```

#### course_sections
```
id, course_id (FK → courses.id)
title, sort_order
created_at, updated_at
```

#### course_lectures
```
id, course_id (FK → courses.id)
section_id (FK → course_sections.id)
title, type (enum: LectureType)
url (nullable), content (text, nullable)
video_duration (integer, seconds)
is_free_preview (boolean)
sort_order
created_at, updated_at
```

#### course_resources
```
id, course_id, lecture_id (nullable)
title, type (enum: ResourceType)
url, file_size (nullable)
created_at, updated_at
```

#### course_goals
```
id, course_id (FK → courses.id)
goal_name (string)
created_at, updated_at
```

#### certificates
```
id, user_id, course_id
certificate_number (unique)
issued_at (timestamp)
created_at, updated_at
```

#### orders (polymorphic)
```
id, user_id (FK → users.id)
orderable_type (Course::class atau Booking::class)
orderable_id (FK ke course atau booking)
item_name (string)
invoice_number (unique)
original_price, discount_amount, final_amount
status (enum: OrderStatus)
snap_token (nullable — Midtrans token)
expired_at (timestamp, nullable)
created_at, updated_at
```

#### payments
```
id, order_id (FK → orders.id)
midtrans_transaction_id (unique)
payment_type (nullable — gopay, bank_transfer, dll)
amount
status (enum: PaymentStatus)
midtrans_response (json)
paid_at (timestamp, nullable)
created_at, updated_at
```

#### tutor_schedules
```
id, tutor_id (FK → users.id — companion)
day_of_week (integer 0-6)
start_time, end_time (time)
session_duration (integer, menit)
break_time (integer, menit)
price (decimal)
max_participants (integer, default: 1)
is_active (boolean)
created_at, updated_at
```

#### bookings
```
id
student_id (FK → users.id)
tutor_id (FK → users.id)
tutor_schedule_id (FK → tutor_schedules.id)
start_at, end_at (timestamp)
type (enum: SessionType — online/offline)
status (enum: BookingStatus)
notes (text, nullable)
meeting_link (string, nullable — diisi admin setelah booking confirmed)
confirmed_at (timestamp, nullable)
completed_at (timestamp, nullable)
cancelled_at (timestamp, nullable)
created_at, updated_at
```

#### enrollments
```
id, user_id (FK → users.id)
course_id (FK → courses.id)
order_id (FK → orders.id, nullable)
status (enum: EnrollmentStatus)
enrolled_at (timestamp)
completed_at (timestamp, nullable)
created_at, updated_at
```

#### course_progress
```
id, user_id (FK → users.id)
course_id (FK → courses.id)
lecture_id (FK → course_lectures.id)
watch_seconds (integer)
is_completed (boolean)
completed_at (timestamp, nullable)
created_at, updated_at
```

#### reviews (polymorphic)
```
id, user_id (FK → users.id)
reviewable_type (Course::class atau Booking::class)
reviewable_id
rating (integer 1-5)
comment (text)
is_published (boolean)
created_at, updated_at
```

#### notifications (Laravel built-in)
```
id (uuid), type, notifiable_type, notifiable_id
data (json), read_at (timestamp, nullable)
created_at, updated_at
```

#### quizzes *(IMPLEMENTED)*
```
id, course_id (FK → courses.id)
section_id (FK → course_sections.id, nullable)
title, description (text, nullable)
is_required (boolean, default: false)
passing_score (integer, 0-100)
created_at, updated_at
```

#### quiz_questions
```
id, quiz_id (FK → quizzes.id)
question (text)
type (enum: multiple_choice|essay)
points (integer)
sort_order (integer)
created_at, updated_at
```

#### quiz_options *(hanya untuk soal multiple_choice)*
```
id, question_id (FK → quiz_questions.id)
option_text (string)
is_correct (boolean)
created_at, updated_at
```

#### quiz_attempts
```
id, user_id (FK → users.id)
quiz_id (FK → quizzes.id)
score (integer, nullable — null jika belum dinilai)
status (enum: pending|graded)
started_at (timestamp)
submitted_at (timestamp, nullable)
created_at, updated_at
```

#### quiz_answers
```
id, attempt_id (FK → quiz_attempts.id)
question_id (FK → quiz_questions.id)
selected_option_id (FK → quiz_options.id, nullable — untuk PG)
essay_answer (text, nullable — untuk esai)
points_earned (integer, nullable)
instructor_note (text, nullable — feedback instruktur/admin)
created_at, updated_at
```

#### articles
```
id, author_id (FK → users.id)
title, slug (unique)
thumbnail (path, nullable)
content (longtext)
status (enum: draft|review|published|archived)
created_at, updated_at
```

### Relasi Eloquent Penting

```php
// User
user->courses()           → HasMany Course (sebagai instructor)
user->enrollments()       → HasMany Enrollment
user->orders()            → HasMany Order
user->bookingsAsStudent() → HasMany Booking (student_id)
user->bookingsAsTutor()   → HasMany Booking (tutor_id)
user->schedules()         → HasMany TutorSchedule
user->reviews()           → MorphMany Review
user->notifications()     → Laravel built-in

// Course
course->instructor()      → BelongsTo User
course->category()        → BelongsTo Category
course->sections()        → HasMany CourseSection
course->lectures()        → HasMany CourseLecture (direct)
course->goals()           → HasMany CourseGoal
course->enrollments()     → HasMany Enrollment
course->reviews()         → MorphMany Review
course->orders()          → MorphMany Order

// Order (polymorphic)
order->user()             → BelongsTo User
order->orderable()        → MorphTo (Course atau Booking)
order->payments()         → HasMany Payment

// Booking
booking->student()        → BelongsTo User (student_id)
booking->tutor()          → BelongsTo User (tutor_id)
booking->schedule()       → BelongsTo TutorSchedule
booking->reviews()        → MorphMany Review
booking->orders()         → MorphMany Order

// Category (self-referencing)
category->parent()        → BelongsTo Category
category->children()      → HasMany Category
category->courses()       → HasMany Course
```

---

## 3. ROLES DAN AKSES

### Role System
```
Role disimpan sebagai string di kolom users.role
Cast ke Enum App\Enums\Roles di model User
Middleware: RoleMiddleware (app/Http/Middleware/RoleMiddleware.php)
Alias: 'role' di bootstrap/app.php
```

### Akses Per Role

#### Admin (role: admin)
- `/admin/dashboard` — statistik platform (users, courses, orders, revenue)
- `/admin/users` — CRUD semua user, assign role
- `/admin/categories` — CRUD kategori (parent + children)
- `/admin/courses` — list semua kelas, approve/reject
- `/admin/orders` — monitor semua transaksi

#### Instructor (role: instructor)
- `/instructor/dashboard` — stats kelas sendiri (total siswa, revenue)
- `/instructor/courses` — CRUD kelas milik sendiri
- `/instructor/courses/{id}/manage` — kelola sections & lectures
- Route section: `/instructor/courses/{course}/sections`
- Route lecture: `/instructor/courses/{course}/sections/{section}/lectures`

#### Companion (role: companion)
- `/companion/dashboard` — stats booking (total, upcoming, revenue)
- `/companion/schedules` — CRUD jadwal tersedia
- `/companion/bookings` — list booking masuk

#### User biasa (role: user)
- `/dashboard` — dashboard utama
- `/user/enrollments` — kelas yang diikuti
- `/user/orders` — riwayat transaksi

### Public Routes (tanpa login)
- `/` — landing page
- `/courses` — katalog kelas (dengan filter)
- `/courses/{slug}` — detail kelas
- `/companions` — list guru pendamping
- `/companions/{id}` — profil guru pendamping

---

## 4. FLOW BISNIS UTAMA

### Flow A: Beli Kelas Online

```
1. User browse katalog → /courses
2. User klik kelas → /courses/{slug}
3. User klik "Beli Sekarang"
   - Cek login → redirect /login jika belum
   - POST /orders {course_id}
   - OrderController@store:
     a. Cek sudah enroll → return 422
     b. Buat Order (status: pending, expired: +1 jam)
     c. Request snap_token dari Midtrans
     d. Return snap_token ke frontend
4. Frontend tampilkan Midtrans Snap popup
5. User bayar via GoPay / VA / CC / dll
6. Midtrans kirim webhook → POST /webhook/midtrans
7. OrderController@webhook:
   a. Verifikasi signature SHA512
   b. Update Payment record
   c. Jika settlement/capture → handleSuccess()
      - Update Order status → paid
      - Buat Enrollment (status: active)
      - Kirim OrderPaidNotification ke user
8. User akses kelas → /learn/{slug}
```

### Flow B: Booking Guru Pendamping

```
1. User browse → /companions
2. User pilih companion → /companions/{id}
3. User klik "Booking" pada jadwal tertentu
   - Cek login → redirect /login jika belum
   - Tampilkan modal: pilih tanggal + catatan
4. User submit → POST /bookings
   - BookingController@store:
     a. Validasi schedule_id, start_at, notes
     b. Cek ketersediaan slot
     c. Hitung end_at (start + session_duration)
     d. Buat Booking (status: pending)
     e. Buat Order polymorphic ke Booking
     f. Request snap_token dari Midtrans
5. Frontend tampilkan Midtrans Snap popup
6. User bayar
7. Webhook Midtrans → konfirmasi booking
8. User dan Companion mendapat notifikasi
```

### Flow C: Proses Kelas (Learning)

```
1. User akses /learn/{slug}
   - LearnController@show:
     a. Cek enrollment aktif → redirect jika tidak ada
     b. Load course + sections + lectures
     c. Load CourseProgress user
     d. Tentukan activeLecture (pertama yang belum selesai)
     e. Hitung progressPercent
2. User tonton video (iframe YouTube embed)
3. User klik "Tandai Selesai"
   - POST /learn/{slug}/progress
   - Update/create CourseProgress
   - Hitung ulang progress %
   - Auto-navigate ke lecture berikutnya
4. Jika 100% → trigger GenerateCertificateJob
   - Buat Certificate record
   - TODO: Generate PDF via DomPDF
```

### Flow D: Approval Kelas

```
Instructor buat kelas:
1. /instructor/courses/create → status: draft
2. Instructor tambah sections & lectures
3. Instructor ubah status → review
4. Admin lihat di /admin/courses (pending review count)
5. Admin klik Approve:
   - Course status → published
   - Kirim CourseApprovedNotification ke instructor
6. Kelas muncul di katalog publik
```

---

## 5. SERVICES DAN HELPERS

### MidtransService (app/Services/MidtransService.php)
```php
// Konfigurasi dari config/midtrans.php
// Method: createSnapToken(Order $order): string
// Membuat Snap token untuk Midtrans popup
// Item details, customer details, expiry otomatis dari order
```

### OrderService (app/Services/OrderService.php)
```php
// Method: createForCourse(User $user, Course $course): Order
// Cek existing pending order → return jika ada
// Buat order baru dengan invoice number unik (INV-YYYYMMDD-XXXXXX)
// Handle kasus kelas gratis
```

### FortifyServiceProvider — Custom Redirect
```php
// Login redirect berdasarkan role:
// admin      → /admin/dashboard
// instructor → /instructor/dashboard
// companion  → /companion/dashboard
// user       → /dashboard
// Implemented via LoginResponse & RegisterResponse override di register()
```

---

## 6. NOTIFICATION SYSTEM

### Cara Kerja
```
Channel: mail + database (sesuai jenis notifikasi)
Queue: semua Notification implements ShouldQueue → dikirim async
Auto-polling: setiap 30 detik via setInterval di NotificationBell.vue
Badge: unread count di bell icon
Dropdown: list 10 notifikasi terbaru
Mark read: klik notifikasi individual atau "Tandai semua dibaca"
```

### Notification Classes
```php
OrderPaidNotification           → mail + database → user setelah bayar kelas
BookingConfirmedNotification    → mail + database → student setelah booking confirmed
BookingMeetingLinkNotification  → database only  → student + tutor saat admin set meeting link
CourseApprovedNotification      → mail + database → instructor setelah kelas di-approve
ScalevWelcomeNotification       → mail only      → user baru via Scalev webhook (berisi password)
```

### SMTP Configuration
```env
MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password   ← gunakan Google App Password, bukan password biasa
MAIL_FROM_ADDRESS="noreply@difafriends.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Email Template
Custom branding DifaFriends dipublish ke `resources/views/vendor/mail/`:
- Warna utama: `#7B2D8B` (purple)
- Header: logo DifaFriends
- Footer: copyright + link difafriends.com + pengaturan akun

### API Routes Notifikasi
```
GET  /notifications           → list + unread count
POST /notifications/{id}/read → tandai 1 notif dibaca
POST /notifications/read-all  → tandai semua dibaca
```

### Data Struktur Notifikasi (database channel)
```json
{
  "type": "order_paid",
  "title": "Pembayaran Berhasil!",
  "message": "Kelas 'X' berhasil dibeli.",
  "url": "/user/orders",
  "icon": "check-circle"
}
```

---

## 7. FRONTEND CONVENTIONS

### Layout Rules
```
AppLayout.vue   → Halaman yang butuh LOGIN (dashboard, profile, learn)
                → Punya sidebar dinamis per role
                → Jika dipakai tanpa login → CRASH (NavUser error)

GuestLayout.vue → Halaman PUBLIC (catalog, detail course, companions)
                → Punya navbar dengan logo + menu + auth buttons
                → Aman diakses tanpa login

AuthLayout.vue  → Halaman auth (login, register, forgot password)
                → Layout minimalis centered
```

### Vue Component Conventions
```typescript
// Props: selalu define TypeScript interface
// Inertia data: akses via defineProps<{...}>()
// Auth user: gunakan usePage() dari @inertiajs/vue3
// Navigate: gunakan router.get/post dari @inertiajs/vue3
// HTTP calls: gunakan axios untuk AJAX (order, booking, progress)
// Forms: gunakan useForm() dari Inertia untuk form dengan error handling
```

### AppSidebar — Dynamic Menu
```typescript
// Menu berubah berdasarkan user.role
// Roles dambil dari usePage().props.auth.user.role
// adminNav, instructorNav, companionNav, userNav
// Definisi di resources/js/components/AppSidebar.vue
```

### Inertia Props Sharing (HandleInertiaRequests)
```php
// Semua page otomatis dapat:
'auth' => ['user' => $request->user()]
'flash' => ['success' => ..., 'error' => ...]
'notifications' => ['unread_count' => ..., 'latest' => [...]]
```

---

## 8. PAYMENT INTEGRATION

### Midtrans Configuration
```env
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxx
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

### Webhook Security
```php
// Midtrans — verifikasi signature:
// SHA512(order_id + status_code + gross_amount + server_key)
// Exempt dari CSRF: bootstrap/app.php → validateCsrfTokens(except: ['webhook/midtrans', 'webhook/scalev'])
```

### Scalev Webhook
```
POST /webhook/scalev
Header: X-Scalev-Secret: {SCALEV_WEBHOOK_SECRET}

Payload:
{
  "payment_status": "paid",
  "product_id": "SCALEV-PROD-xxx",    ← cocokkan ke courses.scalev_product_id
  "customer": { "name": "...", "email": "..." }
}

Flow:
1. Verifikasi X-Scalev-Secret header
2. Cari Course via scalev_product_id
3. Jika user belum ada → buat user baru dengan password random → kirim ScalevWelcomeNotification
4. Jika user sudah ada → skip (tidak kirim email)
5. Buat Enrollment jika belum ada (idempoten)
```

### Sandbox Testing
```
Simulator VA: https://simulator.sandbox.midtrans.com/bca/va/index
Snap JS URL:  https://app.sandbox.midtrans.com/snap/snap.js
```

### Order Status Flow
```
pending → (bayar) → paid
pending → (timeout) → expired
pending → (cancel) → cancelled
paid    → (refund) → refunded
```

---

## 9. SEMUA ROUTES

### Public Routes
```
GET  /                          → Welcome (landing page)
GET  /articles                  → ArticleController@index
GET  /articles/{article:slug}    → ArticleController@show 
GET  /courses                   → CourseController@index (catalog)
GET  /courses/{course:slug}     → CourseController@show (detail)
GET  /companions                → CompanionController@index
GET  /companions/{user}         → CompanionController@show
```

### Auth Routes (Laravel Fortify)
```
GET|POST /login
GET|POST /register
POST     /logout
GET|POST /forgot-password
GET|POST /reset-password/{token}
GET      /email/verify
GET|POST /settings/profile
PUT      /settings/password
GET      /settings/appearance
GET      /settings/security
```

### Authenticated User Routes
```
GET  /dashboard              → user dashboard
GET  /user/enrollments       → UserOrderController@enrollments
GET  /user/orders            → UserOrderController@index
POST /orders                 → OrderController@store
GET  /orders                 → OrderController@index
GET  /learn/{course:slug}    → LearnController@show
POST /learn/{slug}/progress  → LearnController@updateProgress
POST /bookings               → BookingController@store
GET  /bookings/{booking}     → BookingController@show
GET  /notifications          → notifikasi list
POST /notifications/{id}/read
POST /notifications/read-all
```

### Admin Routes (prefix: /admin, middleware: role:admin)
```
GET         /admin/dashboard
GET|POST    /admin/users                                         → admin.users.*
GET|PUT|DEL /admin/users/{user}
GET|POST    /admin/categories                                    → admin.categories.*
GET|PUT|DEL /admin/categories/{category}
GET         /admin/courses                                       → admin.courses.index
POST        /admin/courses                                       → admin.courses.store
GET         /admin/courses/create                                → admin.courses.create
GET|PUT|DEL /admin/courses/{course}                              → admin.courses.edit/update/destroy
GET         /admin/courses/{course}/manage                       → admin.courses.manage
PATCH       /admin/courses/{course}/approve                      → admin.courses.approve
PATCH       /admin/courses/{course}/reject                       → admin.courses.reject
GET         /admin/courses/{course}/quizzes/{quiz}/grade         → admin.courses.quiz.grade
POST        /admin/quiz-answers/{answer}/grade                   → admin.quiz.answer.grade
GET|POST    /admin/schedules                                     → admin.schedules.*
GET|PUT|DEL /admin/schedules/{schedule}
GET         /admin/orders                                        → admin.orders.index
GET         /admin/orders/export                                 → admin.orders.export
GET         /admin/orders/{order}                                → admin.orders.show
GET         /admin/companions                                    → admin.companions.index
GET         /admin/companions/{user}/schedules                   → admin.companions.schedules
GET         /admin/bookings                                      → admin.bookings.index
POST        /admin/bookings/{booking}/meeting                    → admin.bookings.meeting (generateMeet)
GET         /admin/reports                                       → admin.reports.index
GET         /admin/reports/export                                → admin.reports.export
GET|POST    /admin/articles                                      → admin.articles.*
GET|PUT|DEL /admin/articles/{article}
```

### Instructor Routes (prefix: /instructor, middleware: role:instructor)
```
GET         /instructor/dashboard                                → instructor.dashboard
GET|POST    /instructor/courses                                  → instructor.courses.*
GET|PUT|DEL /instructor/courses/{course}
GET         /instructor/courses/{course}/manage                  → instructor.courses.manage
POST        /instructor/courses/{course}/sections                → instructor.courses.sections.store
PUT|DEL     /instructor/courses/{course}/sections/{section}      → instructor.courses.sections.update/destroy
POST        /instructor/courses/{course}/sections/{section}/lectures → instructor.courses.sections.lectures.store
PUT|DEL     /instructor/courses/{course}/sections/{section}/lectures/{lecture}
POST|PUT    /instructor/courses/{course}/sections/{section}/quiz → instructor.courses.sections.quiz.*
DEL         /instructor/courses/{course}/sections/{section}/quiz/{quiz}
POST|PUT|DEL /instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions/{question}
GET         /instructor/courses/{course}/quizzes/{quiz}/grade    → instructor.quiz.grade
POST        /instructor/quiz-answers/{answer}/grade              → instructor.quiz.answer.grade
```

### Companion Routes (prefix: /companion, middleware: role:companion)
```
GET         /companion/dashboard                                 → companion.dashboard
GET|POST    /companion/schedules                                 → companion.schedules.*
GET|PUT|DEL /companion/schedules/{schedule}
PUT         /companion/schedules/{schedule}/toggle-status        → companion.schedules.toggle-status
GET         /companion/bookings                                  → companion.bookings.index
```

### Quiz Routes (auth user)
```
GET  /learn/{slug}/quiz/{quiz}         → quiz.show    — tampilkan kuis
POST /learn/{slug}/quiz/{quiz}/start   → quiz.start   — buat attempt baru
POST /learn/{slug}/quiz/{quiz}/submit  → quiz.submit  — submit jawaban
GET  /learn/{slug}/quiz/{quiz}/result  → quiz.result  — lihat hasil & review
```

### Webhook
```
POST /webhook/midtrans    → OrderController@webhook          (no CSRF, no auth)
POST /webhook/scalev      → ScalevWebhookController@handle   (no CSRF, no auth)
```

---

## 10. SEEDER DAN TEST DATA

### Test Accounts
```
Admin      : admin@difafriends.test     / password
Instructor : instructor@difafriends.test / password
Companion  : companion@difafriends.test  / password
User       : user@difafriends.test       / password
```

### Data Seed
```
Users:       4 fixed + 30 factory (total ~34)
Categories:  4 parent + 12 children (total 16)
             Parent: Terapi, Akademik, Sosial & Emosional, Motorik
Courses:     10 published + 3 featured + 2 draft (total 15)
             Masing-masing punya sections & lectures
```

### Cara Reset & Seed Ulang
```bash
php artisan migrate:fresh --seed
```

---

## 11. FITUR YANG SUDAH SELESAI (STATUS APRIL 2026)

```
✅ Authentication         — register, login, redirect per role, 2FA/TOTP via Fortify
✅ Settings Profile       — edit first_name, last_name, photo, bio, phone, city
✅ Settings Security      — ganti password, toggle 2FA
✅ Landing Page           — hero, features, categories dari DB, kelas featured
✅ Katalog Kelas          — filter kategori, search, sort, pagination
✅ Detail Kelas           — tabs deskripsi/kurikulum/review, sidebar beli
✅ Checkout               — Midtrans Snap popup, order creation
✅ Webhook Midtrans       — verifikasi signature SHA512, aktivasi enrollment, konfirmasi booking
✅ Webhook Scalev         — buat user baru + enrollment, kirim welcome email + password
✅ Halaman Belajar        — video player YouTube, sidebar kurikulum, progress tracking
✅ Companion List         — avatar, kota, harga, search
✅ Companion Profile      — jadwal, ulasan, booking modal
✅ Booking Flow           — pilih jadwal → pilih tanggal → Midtrans popup
✅ User Orders            — riwayat transaksi semua order
✅ User Enrollments       — kelas yang diikuti, tombol lanjut belajar
✅ Review System          — ulasan polymorphic untuk kursus & booking
✅ Certificate            — generate otomatis (GenerateCertificateJob) + unduh PDF
✅ Verifikasi Sertifikat  — halaman publik /verify/{number}
✅ Quiz System            — kuis per section, pilihan ganda (auto-grade) + esai (manual)
✅ Quiz Grading           — instruktur nilai esai, admin bisa nilai semua kuis
✅ Admin Dashboard        — stats cards, alert kelas pending, tabel transaksi terbaru
✅ Admin User CRUD        — create/edit/delete user, assign role
✅ Admin Category CRUD    — create/edit/delete kategori dengan parent
✅ Admin Course           — list + approve/reject kelas + manage konten + kirim notif ke instruktur
✅ Admin Order            — list, detail, export CSV
✅ Admin Schedule         — CRUD jadwal companion
✅ Admin Companions       — daftar companion + jadwal
✅ Admin Bookings         — list booking + set meeting link (generateMeet) + notif ke student & tutor
✅ Admin Articles         — CRUD artikel/blog
✅ Admin Reports          — laporan revenue, enrollment, user growth per bulan (cached)
✅ Admin Quiz Grading     — penilaian esai untuk semua kursus (tanpa batasan instruktur)
✅ Admin Log Aktivitas    — riwayat perubahan data via Spatie Activity Log v5
✅ Instructor Dashboard   — stats kelas, total siswa, revenue
✅ Instructor Course CRUD — create/edit/delete kelas milik sendiri + filter rating/harga
✅ Instructor Manage      — kelola sections, lectures, kuis via halaman manage
✅ Instructor Quiz        — buat/edit kuis per section + tambah soal PG & esai
✅ Instructor Grading     — nilai jawaban esai milik kursus sendiri
✅ Companion Dashboard    — stats booking, upcoming, revenue
✅ Companion Schedule     — CRUD jadwal tersedia + toggle aktif/nonaktif
✅ Companion Bookings     — list booking masuk sebagai tutor
✅ Notification Bell      — badge unread, dropdown, mark read, auto-polling 30s
✅ Email Notifications    — SMTP Gmail configured, custom template branding DifaFriends (#7B2D8B)
✅ Async Notifications    — semua Notification implements ShouldQueue
✅ Laravel Pulse          — monitoring /pulse (hanya admin), snapshot per menit via schedule
✅ Spatie Activity Log v5 — log perubahan User, Course, Booking di admin panel
✅ Wayfinder              — typed route functions dihasilkan otomatis (resources/js/actions/)
✅ Database-Agnostic      — semua query refactor ke Eloquent/Query Builder (no raw SQL)
✅ CI/CD                  — GitHub Actions: lint, tests, auto-deploy via SSH ke VPS
```

---

## 12. FITUR YANG BELUM SELESAI / TODO

```
⏳ Webhook Lokal       — butuh Ngrok/expose untuk test webhook dari Midtrans & Scalev di lokal
⏳ Search Global       — search lintas kelas dan companion
⏳ Admin Export Excel  — maatwebsite/excel sudah terinstall, belum diimplementasikan
⏳ PDF Certificate     — GenerateCertificateJob sudah ada, generate PDF via DomPDF belum
```

---

## 13. SISTEM KUIS (IMPLEMENTED)

### Spesifikasi
- Kuis bersifat **opsional** per section (tidak wajib ada)
- Tipe soal: **Pilihan Ganda (PG)** dan **Esai**
- PG: nilai dinilai otomatis saat submit
- Esai: jawaban direview manual oleh instruktur atau admin
- Nilai akhir = persentase dari total poin yang diperoleh

### Flow Kuis

```
1. Instruktur buat kuis di section:
   POST /instructor/courses/{course}/sections/{section}/quiz
   → instructor/quiz/Form.vue

2. Instruktur tambah soal (PG atau esai):
   POST /instructor/courses/{course}/sections/{section}/quiz/{quiz}/questions

3. Siswa buka halaman kuis:
   GET /learn/{slug}/quiz/{quiz} → user/Quiz.vue

4. Siswa mulai attempt:
   POST /learn/{slug}/quiz/{quiz}/start
   → QuizAttempt dibuat (status: pending)

5. Siswa submit jawaban:
   POST /learn/{slug}/quiz/{quiz}/submit
   → PG: auto-scored, points_earned diset otomatis
   → Jika ada esai → status attempt tetap 'pending'
   → Jika semua PG → status attempt jadi 'graded', score dihitung

6. Instruktur/Admin nilai esai:
   GET  /instructor/courses/{course}/quizzes/{quiz}/grade → instructor/quiz/Grade.vue
   GET  /admin/courses/{course}/quizzes/{quiz}/grade      → admin/quiz/Grade.vue
   POST /instructor/quiz-answers/{answer}/grade
   POST /admin/quiz-answers/{answer}/grade
   → Setelah semua esai dinilai → status = 'graded', score dihitung

7. Siswa lihat hasil:
   GET /learn/{slug}/quiz/{quiz}/result → user/QuizResult.vue
   → Review semua jawaban + feedback instruktur
```

### Perbedaan Grading Instruktur vs Admin

| Aspek | Instruktur | Admin |
|---|---|---|
| Scope | Hanya kursus milik sendiri | Semua kursus di platform |
| Cek kepemilikan | `abort_if($course->instructor_id !== $user->id, 403)` | Tidak ada cek |
| Route | `instructor.quiz.grade` | `admin.courses.quiz.grade` |
| Halaman | `instructor/quiz/Grade.vue` | `admin/quiz/Grade.vue` |

### Wayfinder Actions untuk Kuis

```typescript
// Admin grading
import { index, grade } from '@/actions/App/Http/Controllers/Admin/QuizGradeController'

grade.url(answerId)           // "/admin/quiz-answers/1/grade"
index({ course: 1, quiz: 2 }) // "/admin/courses/1/quizzes/2/grade"
```

---

## 14. LAPORAN & EXPORT

### Admin\ReportController

Semua data laporan di-cache per tahun:
- **Historical** (tahun lalu): 24 jam
- **Tahun berjalan**: 1 jam

| Data | Cara Kalkulasi |
|---|---|
| Revenue per bulan | `Order::whereYear()->get()` → dikelompokkan via PHP Collection |
| Enrollment per bulan | `Enrollment::whereYear()->get()` → grouped by bulan |
| User baru per bulan | `User::whereYear()->get()` → grouped by bulan |
| Top 10 kursus | `Order::selectRaw('item_name, COUNT(*), SUM(final_amount)')` |
| Ringkasan tahunan | `sum()`, `count()` via Eloquent |

> **Catatan**: Semua kalkulasi menggunakan PHP Collection atau Query Builder standard — **database-agnostic**, kompatibel dengan SQLite, MySQL, dan PostgreSQL.

### Export CSV

`GET /admin/reports/export?year=2026`
- Format CSV dengan BOM UTF-8 (kompatibel Excel)
- Kolom: Invoice, Nama User, Email, Item, Total (Rp), Status, Tanggal
- Dilindungi dari CSV Formula Injection (`=`, `+`, `-`, `@`)

---

## 15. KONVENSI KODING

### PHP / Laravel
```php
// Enum: cast di model, bisa pakai langsung (tidak perlu ->value)
// Model sudah cast: 'status' => CourseStatus::class
$course->update(['status' => CourseStatus::Published]);  // ✅

// Controller: dependency injection via constructor
public function __construct(
    private MidtransService $midtransService,
    private OrderService $orderService,
) {}

// Otorisasi ownership: abort_if
abort_if($course->instructor_id !== $request->user()->id, 403);

// Redirect dengan flash message:
return redirect()->route('admin.users.index')
    ->with('success', 'User berhasil dibuat.');

// Raw SQL: HINDARI — gunakan Query Builder atau Eloquent
// ❌ whereRaw('(SELECT AVG(rating) FROM reviews WHERE course_id = ?)', [$id])
// ✅ where(fn($q) => $q->selectRaw('AVG(rating)')->from('reviews')->whereColumn(...))

// Pint: jalankan setelah edit PHP file
vendor/bin/pint --dirty --format agent
```

### Vue / TypeScript
```typescript
// Props: selalu typed
defineProps<{ course: CourseType }>()

// Auth user via usePage()
const page = usePage();
const user = (page.props.auth as any).user;

// Form: gunakan useForm() dari Inertia
const form = useForm({ title: '', ... });
form.post('/route');   // atau form.put(), form.delete()

// Navigation: gunakan router dari Inertia
import { router } from '@inertiajs/vue3';
router.visit('/path');

// Route URL: gunakan Wayfinder (bukan hardcode)
import { grade } from '@/actions/App/Http/Controllers/Admin/QuizGradeController'
form.post(grade.url(answerId));   // ✅
form.post(`/admin/quiz-answers/${answerId}/grade`);  // ❌ hindari hardcode
```

### Wayfinder — Typed Routes
```bash
# Jalankan setelah perubahan routes
php artisan wayfinder:generate --no-interaction

# Import: named import (tree-shaking)
import { index, grade } from '@/actions/App/Http/Controllers/Admin/QuizGradeController'

# Metode tersedia
grade(answerId)        // { url: "...", method: "post" }
grade.url(answerId)    // "/admin/quiz-answers/1/grade"
grade.post(answerId)   // { url: "...", method: "post" }
```

---

## 16. TESTING

### Menjalankan Tests

```bash
# Semua test
php artisan test --compact

# Test spesifik file
php artisan test --compact tests/Feature/QuizTest.php

# Filter by nama test
php artisan test --compact --filter="admin dapat membuka"
```

### Test Suite (123 tests)

| File | Cakupan |
|---|---|
| `tests/Feature/QuizTest.php` | Alur kuis dari sisi siswa (9 test) |
| `tests/Feature/Admin/QuizGradeTest.php` | Penilaian kuis admin (9 test) |
| `tests/Feature/Admin/ReportTest.php` | Laporan revenue & grafik bulanan (12 test) |
| `tests/Feature/Admin/BookingMeetingLinkTest.php` | Set meeting link via admin (5 test) |
| `tests/Feature/WebhookMidtransTest.php` | Webhook Midtrans: settlement, capture, expire, booking (6 test) |
| `tests/Feature/WebhookScalevTest.php` | Webhook Scalev: user baru, idempoten, validasi secret (6 test) |
| `tests/Feature/NotificationTest.php` | CourseApproved, BookingConfirmed, MeetingLink channels (5 test) |
| `tests/Feature/ActivityLogTest.php` | Spatie Activity Log v5 untuk User, Course, Booking |
| `tests/Feature/PulseMonitoringTest.php` | Laravel Pulse access control per role |
| `tests/Feature/Companion/BookingTest.php` | Companion booking list & filter |

### Konvensi Test

```php
// Gunakan test() bukan it() (sesuai konvensi project)
test('admin dapat melihat halaman grade', function () {
    $admin = User::factory()->create(['role' => Roles::Admin->value]);
    ...
    $this->actingAs($admin)->get(route('admin.courses.quiz.grade', [$course, $quiz]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('admin/quiz/Grade'));
});

// Gunakan assertSuccessful() bukan assertStatus(200)
// Gunakan assertForbidden() bukan assertStatus(403)
// Gunakan assertModelExists($model) bukan assertDatabaseHas()
```

> **Penting**: Jalankan `npm run build` sebelum test jika ada halaman Vue baru — test Inertia membutuhkan Vite manifest.

---

## 17. ENVIRONMENT SETUP

### Prerequisites
```
PHP 8.3+
Node.js 20+
Composer 2+
SQLite (development)
```

### Installation
```bash
# Clone dan install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate:fresh --seed

# Storage link
php artisan storage:link

# Run development server
composer run dev
# Menjalankan: php artisan serve + npm run dev + php artisan queue:listen
```

### Environment Variables Penting
```env
APP_URL=http://localhost:8000
DB_CONNECTION=sqlite

MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxx
MIDTRANS_IS_PRODUCTION=false

SCALEV_WEBHOOK_SECRET=your-secret-key

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password

QUEUE_CONNECTION=database  # production: redis
```

---

## 18. CATATAN PENTING UNTUK AI

### Hal yang TIDAK Perlu Dibuat Ulang
Semua fitur di bagian "Sudah Selesai" sudah ada dan berjalan. Jangan suggest membuat ulang dari scratch.

### Pola yang Sudah Disepakati
1. **Tidak ada cart** — langsung checkout satu item
2. **Instruktur tidak bisa daftar sendiri** — admin yang daftarkan
3. **Webhook** butuh server online (gunakan Ngrok untuk development)
4. **Role system** tidak pakai Spatie Permission, tapi PHP Enum custom
5. **Inertia.js** — bukan full SPA, bukan full SSR. Middleware semua via server-side
6. **Database-agnostic** — tidak boleh ada raw SQL yang spesifik satu engine (no `strftime`, no `DATE_FORMAT`, no `GROUP_CONCAT` MySQL/SQLite specific)
7. **Meeting link** — method bernama `generateMeet` (bukan `setZoom`), karena tidak harus pakai Zoom
8. **Spatie Activity Log v5** — perubahan ada di `attribute_changes` (bukan `properties`). Gunakan `$log->attribute_changes->get('attributes')` untuk melihat perubahan
9. **Notifications** — semua implements `ShouldQueue`, jangan kirim synchronous

### Saat Menambah Fitur Baru
1. Selalu cek role middleware yang dibutuhkan
2. Gunakan layout yang tepat (AppLayout vs GuestLayout)
3. Tambah route di kelompok yang sesuai (admin/instructor/companion/user/public)
4. Jalankan `php artisan wayfinder:generate` setelah tambah route
5. Update sidebar AppSidebar.vue jika perlu menu baru
6. Tambah flash message untuk feedback user
7. Tulis test (Pest) untuk setiap fitur baru
8. Jalankan `vendor/bin/pint --dirty --format agent` setelah edit PHP

### Konvensi Penamaan
```
Controller : Admin\UserController, Instructor\CourseController, Companion\ScheduleController
Route name : admin.users.index, instructor.courses.manage, companion.schedules.index
Vue pages  : admin/users/Index.vue, instructor/courses/Form.vue, admin/quiz/Grade.vue
Services   : MidtransService, OrderService (di app/Services/)
Wayfinder  : import dari @/actions/App/Http/Controllers/{Role}/{Name}Controller
```

### Reviews Polymorphic
```
reviews.reviewable_type = Course::class atau Booking::class
reviews.reviewable_id   = id dari Course atau Booking
BUKAN: reviews.course_id (kolom ini tidak ada)
```

---

## 19. MONITORING & LOGGING

### Laravel Pulse (`/pulse`)
```
Akses: hanya admin (Gate::define('viewPulse', ...))
Non-admin / guest: 403 Forbidden (bukan redirect)
Schedule: pulse:snapshot setiap menit (routes/console.php)
Worker: supervisorctl → difafriends-pulse (php artisan pulse:work)
```

### Spatie Activity Log v5
```php
// Model yang di-log: User, Course, Booking
// Breaking change v5: perubahan di attribute_changes, bukan properties
// Cara akses:
$log->attribute_changes->get('attributes')   // nilai baru (created/updated)
$log->attribute_changes->get('old')          // nilai lama (updated/deleted)

// Halaman admin: /admin/logs (Admin\LogActivityController@index)
```

---

## 20. DEPLOYMENT

### Branch Strategy
```
main    → production (auto-deploy via GitHub Actions saat push)
develop → staging / feature development
```

### CI/CD Pipeline (GitHub Actions)
```yaml
# .github/workflows/tests.yml  → jalankan Pest setiap push ke main/develop
# .github/workflows/lint.yml   → Pint + Prettier + ESLint
# .github/workflows/deploy.yml → SSH ke VPS dan jalankan deploy.sh (hanya main)
```

### GitHub Secrets yang Diperlukan
```
SSH_HOST         → IP/domain server VPS
SSH_USERNAME     → user SSH (ubuntu/root)
SSH_PRIVATE_KEY  → private key SSH
SSH_PORT         → (opsional, default 22)
```

### Production Server (VPS)
```
OS: Ubuntu 22.04/24.04
Web: Nginx + PHP 8.4-FPM
DB:  PostgreSQL 16
Cache/Queue: Redis
Supervisor processes:
  - difafriends-worker   → queue:work redis (2 procs)
  - difafriends-pulse    → pulse:work (1 proc)
  - difafriends-schedule → schedule:work (1 proc)
SSL: Certbot (Let's Encrypt)
```

### Deploy Manual
```bash
bash /var/www/difafriends/deploy/deploy.sh
```

Script ini melakukan: git pull → composer install → npm build → migrate → storage:link → optimize → restart supervisor.

---

*Dokumentasi ini terakhir diperbarui April 2026 (v1.3).*
*Update dokumentasi setiap kali ada perubahan signifikan pada arsitektur atau fitur.*
