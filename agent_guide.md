# Agent Guide — School Management API

## Project Overview

A Laravel 10 REST API for managing a school system: users, students, teachers, parents, academic courses, exams, enrollments, finances, library, and facilities.

**Status:** Intermediate development. Core auth and most CRUD controllers are implemented and wired. Library module: Author, Book, BookCopy, BookLoan and Fine are implemented and wired; BookAuthor remains a stub with empty method bodies. TeacherCourse is implemented and wired; StudentParent is implemented but not yet wired into routes.

---

## Tech Stack

| Component        | Technology                                      |
| ---------------- | ----------------------------------------------- |
| Framework        | Laravel 10.10+                                  |
| Language         | PHP 8.1+                                        |
| Database         | PostgreSQL 17 (Docker)                          |
| Authentication   | Laravel Sanctum (API token)                     |
| Image Uploads    | Cloudinary (`cloudinary-labs/cloudinary-laravel`) |
| Testing          | PHPUnit 10                                      |
| Containerization | Docker Compose (PostgreSQL only)                |

---

## Directory Structure

```
app/
├── Console/Kernel.php
├── Exceptions/Handler.php
├── Http/
│   ├── Controllers/          # 30 controllers (29 implemented, 1 stub)
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
├── api.php                   # API routes (22 resources registered)
├── web.php
├── channels.php
└── console.php

tests/
├── Feature/ExampleTest.php
├── Unit/ExampleTest.php
└── TestCase.php
```

---

## Database Schema

### Core Identity
| Table       | Key Columns                                                        |
| ----------- | ------------------------------------------------------------------ |
| roles       | id, name                                                           |
| users       | code (USR-YYYY-NNN), name, username, email, phone, gender, date_of_birth, image, role_id (FK) |
| students    | user_id (FK → users)                                               |
| teachers    | hire_date, user_id (FK → users)                                    |
| parents     | user_id (FK → users)                                               |

### Academic
| Table             | Key Columns                                              |
| ----------------- | -------------------------------------------------------- |
| subjects          | name, description                                        |
| courses           | unit_price, promotion, capacity, start_date, end_date, subject_id (FK) |
| student_parents   | relation, is_primary, student_id, parent_id (pivot)      |
| teacher_courses   | teacher_id, course_id (pivot)                            |
| schedules         | day_of_week, time_start, time_out, room_id, teacher_course_id |
| attendances       | date, time_in, time_out, status (late/persent/permission/absent), user_id, teacher_course_id (FK) — user_id + nullable time cols + teacher_course_id added via migrations |
| enrollments       | enrollment_date, status, student_id, course_id           |

### Financials
| Table    | Key Columns                                                        |
| -------- | ------------------------------------------------------------------ |
| invoices | total_amount, due_date, status, enrollment_id                     |
| payments | amount_paid, payment_method, payment_date, status, invoice_id      |
| payrolls | base_salary, bonus, deduction, net_salary, pay_date, pay_period_start, pay_period_end, teacher_id |

### Exams & Results
| Table   | Key Columns                            |
| ------- | -------------------------------------- |
| exams   | exam_date, exam_type, subject_id, teacher_id, course_id |
| results | score, grade, enrollment_id, exam_id   |

### System
| Table    | Key Columns                              |
| -------- | ---------------------------------------- |
| settings | key (unique), value                      |

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
| Table     | Key Columns                              |
| --------- | ---------------------------------------- |
| buildings | name, total_floors, address_location     |
| floors    | floor_number, building_id                |
| rooms     | room_number (unique), room_type, capacity, floor_id |

---

## Model Relationships

```
User belongsTo Role
User hasOne Student / Teacher / Parent
Student belongsTo User
Student hasMany Enrollments, BookLoans, StudentParents
Teacher belongsTo User
Teacher hasMany Payrolls, TeacherCourses, Exams
Course belongsTo Subject
Course hasMany Enrollments, Exams, TeacherCourses
Subject hasMany Courses, Exams
Building hasMany Floors → Rooms → Schedules
Book hasMany BookCopies, BookAuthors
BookLoan hasMany Fines
Enrollment hasMany Results, Invoices
Invoice hasMany Payments
```

---

## API Routes (Currently Registered)

All CRUD resources use `middleware('auth:sanctum')`. Admin-only resources use `middleware('role:1')`; a growing set of academic resources allow Admin+Teacher via `middleware('role:1,2')`.

| Method | URI              | Controller             | Auth       | Status    |
| ------ | ---------------- | ---------------------- | ---------- | --------- |
| POST   | /api/register    | AuthController         | Public     | Implemented |
| POST   | /api/login       | AuthController         | Public     | Implemented |
| POST   | /api/logout      | AuthController         | Sanctum    | Implemented |
| GET    | /api/me          | AuthController (closure) | Sanctum  | Implemented |
| PUT    | /api/profile     | AuthController         | Sanctum     | Implemented |
| PUT    | /api/profile/password | AuthController    | Sanctum     | Implemented |
| POST   | /api/upload      | UploadController       | Sanctum     | Implemented |
| *      | /api/user        | UserController         | **none** (bug, see Known Issues) | Implemented |
| *      | /api/role        | RoleController         | role:1     | Implemented |
| *      | /api/student     | StudentController      | role:1,2   | Implemented |
| *      | /api/teacher     | TeacherController      | role:1     | Implemented |
| *      | /api/subject     | SubjectController      | role:1,2   | Implemented |
| *      | /api/building    | BuildingController     | role:1     | Implemented |
| *      | /api/parent      | ParentController       | role:1,2   | Implemented |
| *      | /api/course      | CourseController       | role:1,2   | Implemented |
| *      | /api/payment     | PaymentController      | role:1     | Implemented |
| *      | /api/enrollment  | EnrollmentController   | role:1,2   | Implemented |
| *      | /api/exam        | ExamController         | role:1,2   | Implemented |
| *      | /api/invoice     | InvoiceController      | role:1     | Implemented |
| *      | /api/result      | ResultController       | role:1,2   | Implemented |
| *      | /api/floor       | FloorController        | role:1     | Implemented |
| *      | /api/payroll     | PayrollController      | role:1     | Implemented |
| *      | /api/room        | RoomController         | role:1,2   | Implemented |
| *      | /api/schedule    | ScheduleController     | role:1,2   | Implemented |
| *      | /api/attendance  | AttendanceController   | role:1,2   | Implemented |
| *      | /api/teacher-course | TeacherCourseController | role:1,2 | Implemented |
| *      | /api/author      | AuthorController       | role:1     | Implemented |
| *      | /api/book        | BookController         | role:1     | Implemented |
| *      | /api/book-copy   | BookCopyController     | role:1     | Implemented |
| *      | /api/book-loan   | BookLoanController     | role:1     | Implemented |
| *      | /api/fine        | FineController         | role:1     | Implemented |

| GET    | /api/dashboard/summary | DashboardController | role:1     | Implemented |
| GET/POST | /api/setting | SettingController | role:1 | Implemented |

`* = apiResource (GET /, GET /{id}, POST, PUT /{id}, DELETE /{id})`

**Not yet wired (implemented but not registered in routes):** StudentParent.
**Not yet wired (stub):** BookAuthor.

---

## Authentication & Authorization

- **Sanctum tokens** returned on login/register as Bearer tokens
- **CheckRole middleware** (alias `role`, registered in `app/Http/Kernel.php`) checks `role_id` against allowed IDs
- **Roles seeded:** Admin(1), Teacher(2), Library_staff(3), Student(4), Parent(5)
- **Route usage:** `middleware('role:1')` restricts to Admin; `middleware('role:1,2')` allows Admin and Teacher
- **Known gap:** the `/user` apiResource is registered outside the `auth:sanctum` group with no middleware (see Known Issues)

---

## Code Conventions & Patterns

1. **User code format:** `USR-{YEAR}-{sequential:3digits}` (e.g., `USR-2026-001`) generated via `User::lockForUpdate()->latest()` + `str_pad`
2. **DB transactions:** Multi-table operations wrapped in `DB::beginTransaction()` / `commit()` / `rollBack()`
3. **Cloudinary uploads:** Static `CloudinaryService::upload($file, $folder)` / `delete($imageUrl)`
4. **Response format:** `{ "message": "...", "data": ... }` with HTTP status codes (200/201/401/403/500)
5. **Model naming:** Plural — `Students`, `Teachers`, `Courses`, `Books`
6. **Form validation:** Via `StoreUserRequest` with `sometimes` and `Rule::unique()->ignore()`
7. **Eager loading:** `Students::with('user.role')`
8. **Route model binding:** Controllers use type-hinted models (e.g., `Students $student`) — but newer controllers (Author, Book, BookCopy, BookLoan) use `string $id` + `findOrFail`
9. **Comments:** Some Khmer language comments present in code
10. **Response eager loading:** Controllers `with()` the relations the frontend needs (e.g. `borrower`, `book_copy.book`, `staff`); relation keys serialize as snake_case in JSON

---

## Seed Data

- **Roles:** Admin(1), Teacher(2), Library_staff(3), Student(4), Parent(5)
- **Users:** superadmin (Admin), sinh (Teacher), staff (Library_staff), sinh (Student), si (Parent)
- **Seeded via API (dev data, not in DatabaseSeeder):**
  - Teachers 2/4/5/6, courses 1/2/5/6 (Mathematics, Khmer Literature, Biology, English), subjects 1/2/8/10
  - Enrollments (students 2/3), schedules (Mon/Wed/Tue, Mon-Fri blocks), rooms 101/201/CH-301/PH-302/301/READ-1/102
  - Exams (Math Final, Khmer Midterm, Biology Quiz, English Final), results, invoices, payments
  - Library: authors 2-7, books 1-4, book copies BC-001..007, book loans (borrowed/returned/overdue)
  - Facilities: buildings 1-4 (Main, Voluptatibus officii, Science, Library) with floors 1-16

**Note:** Live data is frequently deleted/re-added externally (course/enrollment/author ids have shifted). Verify current ids before referencing them in code or seeds.

---

## Known Issues

1. **Migration/model mismatches:**
   - `Books` model has a `book_loans()` relation using `book_id`, but `book_loans` table has no `book_id` column (loans reference `book_copies.book_id`, not books directly)
   - `BookLoans` model has misnamed `student()` and `book()`/`user()` methods (`student()` actually belongsTo `User` via `library_staff_id`); proper relations `borrower()`/`bookCopy()`/`staff()` exist and should be preferred
   - `Authors` model has self-referencing `authors()` method (bug); the working pivot relation is `book_authors()`
   - `Fine` verb vs `Fines` model naming — `BookLoans::fines()` references `fines::class` directly
   - `schedule`/`attendance` mismatches were fixed via migrations: `day_of_week` added to `schedules`, `user_id` added to `attendances`, and `attendances.time_in`/`time_out` made nullable
2. **Route security gap** — `/user` apiResource is registered **without** `auth:sanctum` / `role:1` middleware (only a comment says "only admin"); any unauthenticated request can CRUD users
3. **Library module stub** — BookAuthor controller still has empty method bodies (Author, Book, BookCopy, BookLoan, Fine are implemented)
4. **No application tests** written yet (only default example tests)
5. **phpunit.xml** — SQLite in-memory DB is commented out; tests need PostgreSQL
6. **`.env`** with real credentials (including Cloudinary URL) is in the repository

---

## Running the Project

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

## Git

- **Main branches:** `main`, `dev`
- **Active branch:** `dev`
- **Remote:** `origin` with `main`, `dev`, `develop`, `sinh-dev` branches

---

## When Making Changes

- Follow existing controller patterns (JSON response format, DB transactions for multi-table ops)
- Check model `$fillable` and `$casts` before adding fields (e.g. `BookLoans` was missing `status` until it was added)
- Register new routes in `routes/api.php` with appropriate `role:` middleware — remember to `use App\Http\Controllers\X;` at the top, otherwise the route silently fails with "Target class [XController] does not exist"
- Use existing `CloudinaryService` for image uploads
- Use `StoreUserRequest` for user-related validation
- Be aware of existing migration/model mismatches — validate against the migration, not just the model