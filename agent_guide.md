# Agent Guide — School Management API

## Project Overview

A Laravel 10 REST API for managing a school system: users, students, teachers, parents, academic courses, exams, enrollments, finances, library, and facilities.

**Status:** Intermediate development. Core auth and most CRUD controllers are implemented and wired. Library module controllers (Author, Book, BookCopy, BookLoan, BookAuthor, Fine) remain stubs with empty method bodies.

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
│   ├── Controllers/          # 28 controllers (22 implemented, 6 stubs)
│   ├── Middleware/CheckRole.php  # Custom RBAC middleware
│   └── Requests/StoreUserRequest.php  # Form validation
├── Models/                   # 26 Eloquent models
├── Providers/
└── Services/CloudinaryService.php  # Image upload/delete helper

database/
├── migrations/               # 29 migration files
├── factories/UserFactory.php
└── seeders/DatabaseSeeder.php  # Roles + admin seed data

routes/
├── api.php                   # API routes (19 resources registered)
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
| users       | code (USR-YYYY-NNN), name, username, email, phone, gender, date_of_birth, image, role_id (FK), password |
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
| schedules         | time_start, time_out, room_id, teacher_course_id         |
| attendances       | attendance_date, status (late/persent/permission/absent), time_in, time_out |
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
| floors    | floor_nummber (note: typo), building_id  |
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
BookLoan belongsTo BookCopy, User, Book
BookLoan hasMany Fines
Enrollment hasMany Results, Invoices
Invoice hasMany Payments
```

---

## API Routes (Currently Registered)

All routes use `middleware('auth:sanctum')`. Admin-only resources use `middleware('role:1')`.

| Method | URI              | Controller             | Auth       | Status    |
| ------ | ---------------- | ---------------------- | ---------- | --------- |
| POST   | /api/register    | AuthController         | Public     | Implemented |
| POST   | /api/login       | AuthController         | Public     | Implemented |
| POST   | /api/logout      | AuthController         | Sanctum    | Implemented |
| *      | /api/user        | UserController         | role:1     | Implemented |
| *      | /api/role        | RoleController         | role:1     | Implemented |
| *      | /api/student     | StudentController      | role:1     | Implemented |
| *      | /api/teacher     | TeacherController      | role:1     | Implemented |
| *      | /api/subject     | SubjectController      | role:1     | Implemented |
| *      | /api/parent      | ParentController       | role:1     | Implemented |
| *      | /api/course      | CourseController       | role:1     | Implemented |
| *      | /api/enrollment  | EnrollmentController   | role:1     | Implemented |
| *      | /api/attendance  | AttendanceController   | role:1     | Implemented |
| *      | /api/exam        | ExamController         | role:1     | Implemented |
| *      | /api/result      | ResultController       | role:1     | Implemented |
| *      | /api/invoice     | InvoiceController      | role:1     | Implemented |
| *      | /api/payment     | PaymentController      | role:1     | Implemented |
| *      | /api/payroll     | PayrollController      | role:1     | Implemented |
| *      | /api/building    | BuildingController     | role:1     | Implemented |
| *      | /api/floor       | FloorController        | role:1     | Implemented |
| *      | /api/room        | RoomController         | role:1     | Implemented |
| *      | /api/schedule    | ScheduleController     | role:1     | Implemented |

`* = apiResource (GET /, GET /{id}, POST, PUT /{id}, DELETE /{id})`

**Not yet wired:** Author, Book, BookCopy, BookLoan, BookAuthor, Fine, TeacherCourse, StudentParent controllers (implemented but not registered in routes).

---

## Authentication & Authorization

- **Sanctum tokens** returned on login/register as Bearer tokens
- **CheckRole middleware** (alias `role`) checks `role_id` against allowed IDs
- **Roles seeded:** Admin(1), Teacher(2), Library_staff(3), Student(4)
- **Route usage:** `middleware('role:1')` restricts to Admin

---

## Code Conventions & Patterns

1. **User code format:** `USR-{YEAR}-{sequential:3digits}` (e.g., `USR-2026-001`) generated via `User::lockForUpdate()->latest()` + `str_pad`
2. **DB transactions:** Multi-table operations wrapped in `DB::beginTransaction()` / `commit()` / `rollBack()`
3. **Cloudinary uploads:** Static `CloudinaryService::upload($file, $folder)` / `delete($imageUrl, $folder)`
4. **Response format:** `{ "message": "...", "data": ... }` with HTTP status codes (200/201/401/403/500)
5. **Model naming:** Plural — `Students`, `Teachers`, `Courses`, `Books`
6. **Form validation:** Via `StoreUserRequest` with `sometimes` and `Rule::unique()->ignore()`
7. **Eager loading:** `Students::with('user.role')`
8. **Route model binding:** Controllers use type-hinted models (e.g., `Students $student`)
9. **Comments:** Some Khmer language comments present in code

---

## Seed Data

- **Roles:** Admin(1), Teacher(2), Library_staff(3), Student(4)
- **Users:** superadmin (Admin), sinh (Teacher), staff (Library_staff), sinh (Student)

---

## Known Issues

1. **Migration/model mismatches:**
   - `floors` table: column `floor_nummber` (typo)
   - `attendances` model has `user_id` in fillable but migration lacks `user_id` column
   - `schedules` model uses `time_end` but migration uses `time_out`; model has `day_of_week` but migration doesn't
   - `book_loans` model references `student_id`, `book_id` but migration uses `book_copy_id`, `user_id`, `library_staff_id`
   - `Authors` model has self-referencing `authors()` method (bug)
2. **Library module stubs** — Author, Book, BookCopy, BookLoan, BookAuthor, Fine controllers have empty method bodies
3. **No application tests** written yet (only default example tests)
4. **phpunit.xml** — SQLite in-memory DB is commented out; tests need PostgreSQL
5. **`.env`** with real credentials (including Cloudinary URL) is in the repository

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
- **Active branch:** `sinh-dev`
- **Remote:** `origin` with `develop` branch

---

## When Making Changes

- Follow existing controller patterns (JSON response format, DB transactions for multi-table ops)
- Check model `$fillable` and `$casts` before adding fields
- Register new routes in `routes/api.php` with appropriate `role:` middleware
- Use existing `CloudinaryService` for image uploads
- Use `StoreUserRequest` for user-related validation
- Be aware of existing migration/model mismatches — validate against the migration, not just the model
