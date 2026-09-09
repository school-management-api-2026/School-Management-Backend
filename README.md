# School Management API

A Laravel 10 REST API for managing a complete school system: users, students, teachers, parents, academic courses, exams, enrollments, finances, library, and facilities.

**Status:** Intermediate development. Core auth and most CRUD controllers are implemented and wired. The library module's `Author`, `Book`, `BookCopy`, and `BookLoan` are implemented and wired; `BookAuthor` and `Fine` remain stubs with empty method bodies. `TeacherCourse` is implemented and wired, while `StudentParent` is implemented but not yet registered in routes.

---

## Tech Stack

| Component        | Technology                                        |
| ---------------- | ------------------------------------------------- |
| Framework        | Laravel 10.10+                                    |
| Language         | PHP 8.1+                                          |
| Database         | PostgreSQL 17 (Docker)                            |
| Authentication   | Laravel Sanctum (API token)                       |
| Image Uploads    | Cloudinary (`cloudinary-labs/cloudinary-laravel`) |
| Testing          | PHPUnit 10                                        |
| Containerization | Docker Compose (PostgreSQL only)                  |

---

## Features

- **Authentication & Authorization** — Register / login / logout / profile via Sanctum Bearer tokens, with role-based access control through the `role` middleware.
- **Core Identity** — Management of users, students, teachers, and parents, with auto-generated user codes (`USR-YYYY-NNN`).
- **Academic** — Subjects, courses, schedules, attendances, enrollments, and the teacher-course pivot.
- **Exams & Results** — Exams and result grading linked to enrollments.
- **Financials** — Invoices, payments, and teacher payrolls.
- **Library** — Books, book copies, authors, book loans, and fines (partial).
- **Facilities** — Buildings, floors, and rooms.
- **System** — Settings key/value store and a dashboard summary endpoint.

---

## Directory Structure

```
app/
├── Console/Kernel.php
├── Exceptions/Handler.php
├── Http/
│   ├── Controllers/          # 31 controllers (library stubs: BookAuthor, Fine)
│   ├── Kernel.php            # Middleware alias `role` → CheckRole
│   ├── Middleware/CheckRole.php  # Custom RBAC middleware
│   └── Requests/StoreUserRequest.php  # Form validation
├── Models/                   # 26 Eloquent models
├── Providers/
└── Services/CloudinaryService.php  # Image upload/delete helper

database/
├── migrations/               # 30 migration files
├── factories/UserFactory.php
└── seeders/DatabaseSeeder.php  # Roles + admin seed data

routes/
└── api.php                   # API routes

tests/
├── Feature/ExampleTest.php
├── Unit/ExampleTest.php
└── TestCase.php
```

---

## Database Schema

### Core Identity
| Table    | Key Columns                                                        |
| -------- | ------------------------------------------------------------------ |
| roles    | id, name                                                           |
| users    | code (USR-YYYY-NNN), name, username, email, phone, gender, date_of_birth, image, role_id (FK) |
| students | user_id (FK → users)                                               |
| teachers | hire_date, user_id (FK → users)                                    |
| parents  | user_id (FK → users)                                               |

### Academic
| Table            | Key Columns                                                    |
| ---------------- | -------------------------------------------------------------- |
| subjects         | name, description                                              |
| courses          | unit_price, promotion, capacity, start_date, end_date, subject_id (FK) |
| student_parents  | relation, is_primary, student_id, parent_id (pivot)            |
| teacher_courses  | teacher_id, course_id (pivot)                                  |
| schedules        | day_of_week, time_start, time_out, room_id, teacher_course_id  |
| attendances      | date, time_in, time_out, status (late/persent/permission/absent), user_id, teacher_course_id (FK) |
| enrollments      | enrollment_date, status, student_id, course_id                 |

### Financials
| Table    | Key Columns                                                    |
| -------- | -------------------------------------------------------------- |
| invoices | total_amount, due_date, status, enrollment_id                  |
| payments | amount_paid, payment_method, payment_date, status, invoice_id  |
| payrolls | base_salary, bonus, deduction, net_salary, pay_date, pay_period_start, pay_period_end, teacher_id |

### Exams & Results
| Table   | Key Columns                              |
| ------- | ---------------------------------------- |
| exams   | exam_date, exam_type, subject_id, teacher_id, course_id |
| results | score, grade, enrollment_id, exam_id     |

### System
| Table    | Key Columns          |
| -------- | -------------------- |
| settings | key (unique), value  |

### Library
| Table        | Key Columns                                              |
| ------------ | -------------------------------------------------------- |
| books        | title, isbn (unique), category                           |
| book_copies  | barcode (unique), status (available/borrowed/lost), book_id |
| authors      | name, gender, date_of_birth, nation                      |
| book_authors | book_id, author_id (pivot)                               |
| book_loans   | loan_date, due_date, return_date, status, book_copy_id, user_id, library_staff_id |
| fines        | amount, paid_status, book_loan_id                        |

### Facilities
| Table     | Key Columns                                |
| --------- | ------------------------------------------ |
| buildings | name, total_floors, address_location       |
| floors    | floor_number, building_id                  |
| rooms     | room_number (unique), room_type, capacity, floor_id |

---

## Authentication & Authorization

- **Sanctum tokens** returned on login/register as Bearer tokens.
- **CheckRole middleware** (alias `role`, registered in `app/Http/Kernel.php`) checks `role_id` against allowed IDs.
- **Roles seeded:** Admin (1), Teacher (2), Library_staff (3), Student (4), Parent (5).
- **Route usage:** `middleware('role:1')` restricts to Admin.

---

## API Routes

All CRUD resources use `auth:sanctum`. Admin-only resources additionally use `role:1`.

`sanctum` (authenticated): `/register`, `/login`, `/logout`, `/me`, `/profile`, `/profile/password`, `/upload`.

`role:1` (admin) resources:

- `/user`, `/role`, `/student`, `/teacher`, `/subject`, `/building`, `/parent`, `/course`, `/payment`, `/enrollment`, `/exam`, `/invoice`, `/result`, `/floor`, `/payroll`, `/room`, `/schedule`, `/attendance`, `/teacher-course`, `/author`, `/book`, `/book-copy`, `/book-loan`

Other admin endpoints: `/dashboard/summary`, `/setting`.

**Not yet wired:** `StudentParent` (implemented); `BookAuthor`, `Fine` (stubs).

---

## Installation & Setup

Requires PHP 8.1+, Composer, and Docker.

```bash
# Start PostgreSQL
docker compose up -d

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Run migrations and seed
php artisan migrate:fresh --seed

# Start dev server
php artisan serve
```

---

## Code Conventions

1. **User code format:** `USR-{YEAR}-{sequential:3digits}` (e.g., `USR-2026-001`) generated via `User::lockForUpdate()->latest()` + `str_pad`.
2. **DB transactions:** Multi-table operations wrapped in `DB::beginTransaction()` / `commit()` / `rollBack()`.
3. **Cloudinary uploads:** Static `CloudinaryService::upload($file, $folder)` / `delete($imageUrl)`.
4. **Response format:** `{ "message": "...", "data": ... }` with HTTP status codes.
5. **Model naming:** Plural — `Students`, `Teachers`, `Courses`, `Books`.
6. **Form validation:** Via `StoreUserRequest` with `sometimes` and `Rule::unique()->ignore()`.
7. **Eager loading:** `Students::with('user.role')`.
8. **Route model binding:** Controllers use type-hinted models, though newer controllers use `string $id` + `findOrFail`.

---

## Known Issues

1. **Migration/model mismatches** — e.g., `Books::book_loans()` references a non-existent `book_id` column; misnamed relations on `BookLoans`; self-referencing `Authors::authors()` method; `Fine` vs `Fines` naming.
2. **Route security gap** — The `/user` apiResource is registered **without** `auth:sanctum` / `role:1` middleware; any unauthenticated request can CRUD users.
3. **Library module stubs** — `BookAuthor` and `Fine` controllers still have empty method bodies.
4. **No application tests** written yet (only default example tests).
5. **phpunit.xml** — SQLite in-memory DB is commented out; tests need PostgreSQL.
6. **`.env`** with real credentials (including Cloudinary URL) is in the repository.

---

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
