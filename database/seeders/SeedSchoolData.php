<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

use App\Models\Roles;
use App\Models\User;
use App\Models\Subjects;
use App\Models\Courses;
use App\Models\Teachers;
use App\Models\Students;
use App\Models\Parents;
use App\Models\Buildings;
use App\Models\Floors;
use App\Models\Rooms;
use App\Models\TeacherCourses;
use App\Models\Enrollments;
use App\Models\Exams;
use App\Models\Results;
use App\Models\Invoices;
use App\Models\Payments;
use App\Models\Payrolls;
use App\Models\Authors;
use App\Models\Books;
use App\Models\BookCopies;
use App\Models\BookLoans;
use App\Models\Fines;
use App\Models\Attendances;

class SeedSchoolData extends Seeder
{
    public function run(): void
    {
        $this->seedRolesAndUsers();
        $this->seedSubjectsAndCourses();
        $this->seedBuildingsFloorsRooms();
        $this->seedTeachers();
        $this->seedStudents();
        $this->seedParents();
        $this->seedTeacherCourses();
        $this->seedEnrollments();
        $this->seedExamsAndResults();
        $this->seedInvoicesAndPayments();
        $this->seedPayrolls();
        $this->seedLibraryData();
        $this->seedAttendance();

        $this->command->info('School data seeded successfully!');
    }

    private function seedRolesAndUsers(): void
    {
        $roles = ['Admin', 'Teacher', 'Library_staff', 'Student', 'Parent'];
        foreach ($roles as $i => $name) {
            Roles::firstOrCreate(['name' => $name]);
        }

        User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            ['name' => 'superadmin', 'username' => 'superadmin', 'password' => Hash::make('superadmin'), 'phone' => '1234556667', 'role_id' => 1]
        );
        User::firstOrCreate(
            ['email' => 'sinhadmin@gmail.com'],
            ['name' => 'sinh', 'username' => 'sinhadmin', 'password' => Hash::make('superadmin'), 'phone' => '1234556668', 'role_id' => 2]
        );
        User::firstOrCreate(
            ['email' => 'staff@gmail.com'],
            ['name' => 'staff', 'username' => 'staff', 'password' => Hash::make('staff'), 'phone' => '1234556669', 'role_id' => 3]
        );
        User::firstOrCreate(
            ['email' => 'sinhnaadmin@gmail.com'],
            ['name' => 'sinh', 'username' => 'sinhstudent', 'password' => Hash::make('superadmin'), 'phone' => '1234556670', 'role_id' => 4]
        );
        User::firstOrCreate(
            ['email' => 'siadmin@gmail.com'],
            ['name' => 'si', 'username' => 'siadmin', 'password' => Hash::make('superadmin'), 'phone' => '123455', 'role_id' => 5]
        );
    }

    private function seedSubjectsAndCourses(): void
    {
        $subjects = [
            ['Mathematics', 'Algebra, geometry and calculus'],
            ['Physics', 'Mechanics, electricity and modern physics'],
            ['Chemistry', 'Organic, inorganic and physical chemistry'],
            ['English Literature', 'Classic and contemporary literature'],
            ['Khmer Studies', 'Khmer language, literature and culture'],
            ['Computer Science', 'Programming, databases and algorithms'],
            ['Biology', 'Cell biology, genetics and ecology'],
            ['History', 'World and Cambodian history'],
        ];

        $created = [];
        foreach ($subjects as $s) {
            $created[] = Subjects::firstOrCreate(['name' => $s[0]], ['description' => $s[1]]);
        }

        $start = Carbon::parse('2026-09-01');
        $end = Carbon::parse('2026-12-31');
        foreach ($created as $i => $subject) {
            Courses::firstOrCreate(
                ['subject_id' => $subject->id, 'start_date' => $start->toDateString()],
                [
                    'unit_price' => 120.00 + $i * 5,
                    'promotion' => $i % 3 === 0 ? 5.00 : 0.00,
                    'capacity' => $i < 5 ? 40 : 30,
                    'end_date' => $end->toDateString(),
                ]
            );
        }
    }

    private function seedBuildingsFloorsRooms(): void
    {
        $buildingSpecs = [
            ['Building A', 3, 'Active', 'Main academic building'],
            ['Building B', 2, 'Active', 'Science building'],
            ['Building C', 2, 'Active', 'Library and computer labs'],
        ];

        foreach ($buildingSpecs as $b) {
            $building = Buildings::firstOrCreate(['name' => $b[0]], [
                'total_floors' => $b[1],
                'status' => $b[2],
                'description' => $b[3],
            ]);

            for ($f = 1; $f <= $b[1]; $f++) {
                $floor = Floors::firstOrCreate(
                    ['building_id' => $building->id, 'floor_number' => $f]
                );

                $roomTypes = ['Classroom', 'Computer Lab', 'Science Lab', 'Library'];
                foreach ($roomTypes as $rt) {
                    $roomNumber = sprintf('%d%02d', $f, array_search($rt, $roomTypes) + 1);
                    Rooms::firstOrCreate(
                        ['room_number' => "{$roomNumber}-{$building->id}"],
                        ['room_type' => $rt, 'capacity' => $rt === 'Library' ? 50 : 40, 'floor_id' => $floor->id]
                    );
                }
            }
        }
    }

    private function seedTeachers(): void
    {
        $teachers = [
            ['Sokly Meas', 'soklymeas', 'sokly@school.edu', '012111222', 'Male', '1982-04-15', '2015-01-10'],
            ['Dara Chen', 'darachen', 'dara@school.edu', '012333444', 'Male', '1978-11-02', '2010-08-20'],
            ['Sreyneang Sok', 'sreyneang', 'sreyneang@school.edu', '012555666', 'Female', '1985-07-30', '2016-03-05'],
            ['Linda Tan', 'lindatan', 'linda@school.edu', '012777888', 'Female', '1988-01-25', '2018-11-15'],
            ['Visal Phan', 'visalph', 'visal@school.edu', '012999000', 'Male', '1980-09-12', '2012-05-01'],
            ['Borey Long', 'boreylong', 'borey@school.edu', '012000111', 'Male', '1992-03-18', '2020-02-01'],
        ];

        foreach ($teachers as $t) {
            $user = User::firstOrCreate(
                ['email' => $t[2]],
                [
                    'name' => $t[0], 'username' => $t[1], 'password' => Hash::make('password123'),
                    'phone' => $t[3], 'gender' => $t[4], 'date_of_birth' => $t[5], 'role_id' => 2,
                ]
            );
            Teachers::firstOrCreate(['user_id' => $user->id], ['hire_date' => $t[6]]);
        }
    }

    private function seedStudents(): void
    {
        $students = [
            ['Kosal Sam', 'kosalsam', 'kosal@stu.edu', '016111222', 'Male', '2008-02-14'],
            ['Thida Keo', 'thidakeo', 'thida@stu.edu', '016333444', 'Female', '2008-06-22'],
            ['Rattana Som', 'rattanasom', 'rattana@stu.edu', '016555666', 'Male', '2007-11-05'],
            ['Chanveasna Ly', 'chanveasna', 'chanveasna@stu.edu', '016777888', 'Male', '2008-09-30'],
            ['Bopha Mak', 'bophamak', 'bopha@stu.edu', '016999000', 'Female', '2009-01-18'],
            ['Sokunthea Hout', 'sokunthea', 'sokunthea@stu.edu', '016000111', 'Female', '2008-04-25'],
            ['Pich Samnang', 'pichsamnang', 'pich@stu.edu', '016111333', 'Male', '2007-08-09'],
            ['Kalyan Nhem', 'kalyan', 'kalyan@stu.edu', '016222444', 'Female', '2008-12-01'],
        ];

        foreach ($students as $s) {
            $user = User::firstOrCreate(
                ['email' => $s[2]],
                [
                    'name' => $s[0], 'username' => $s[1], 'password' => Hash::make('password123'),
                    'phone' => $s[3], 'gender' => $s[4], 'date_of_birth' => $s[5], 'role_id' => 4,
                ]
            );
            Students::firstOrCreate(['user_id' => $user->id]);
        }
    }

    private function seedParents(): void
    {
        $parents = [
            ['Sovann Sam', 'sovannsam', 'sovann@parent.edu', '017111222', 'Male', '1975-03-10'],
            ['Veasna Keo', 'veasnakeo', 'veasna@parent.edu', '017333444', 'Male', '1978-07-20'],
            ['Sophea Mak', 'sopheamak', 'sophea@parent.edu', '017555666', 'Female', '1980-01-15'],
            ['Dany Hout', 'danyhout', 'dany@parent.edu', '017777888', 'Female', '1979-09-05'],
            ['Mony Rath', 'monyrath', 'mony@parent.edu', '017999000', 'Male', '1976-11-25'],
        ];

        foreach ($parents as $p) {
            $user = User::firstOrCreate(
                ['email' => $p[2]],
                [
                    'name' => $p[0], 'username' => $p[1], 'password' => Hash::make('password123'),
                    'phone' => $p[3], 'gender' => $p[4], 'date_of_birth' => $p[5], 'role_id' => 5,
                ]
            );
            Parents::firstOrCreate(['user_id' => $user->id]);
        }
    }

    private function seedTeacherCourses(): void
    {
        $teachers = Teachers::with('user')->get();
        $courses = Courses::all();

        foreach ($teachers as $i => $teacher) {
            foreach ($courses as $j => $course) {
                if (($i + $j) % 2 === 0) {
                    TeacherCourses::firstOrCreate([
                        'teacher_id' => $teacher->id,
                        'course_id' => $course->id,
                    ]);
                }
            }
        }
    }

    private function seedEnrollments(): void
    {
        $students = Students::all();
        $courses = Courses::all();

        foreach ($students as $i => $student) {
            for ($j = 0; $j < 2; $j++) {
                $course = $courses[($i * 2 + $j) % $courses->count()];
                Enrollments::firstOrCreate(
                    ['student_id' => $student->id, 'course_id' => $course->id],
                    ['enrollment_date' => '2026-09-01', 'status' => 'Enrolled']
                );
            }
        }
    }

    private function seedExamsAndResults(): void
    {
        $teachers = Teachers::all();
        $courses = Courses::all();
        $subjects = Subjects::all();

        foreach ($courses as $i => $course) {
            $teacher = $teachers[$i % $teachers->count()];
            $subject = $subjects[$i % $subjects->count()];
            Exams::firstOrCreate(
                ['course_id' => $course->id, 'exam_type' => 'Midterm', 'exam_date' => '2026-11-15'],
                ['teacher_id' => $teacher->id, 'subject_id' => $subject->id]
            );
        }

        $scores = [95, 88, 76, 92, 65, 81, 73, 58, 90, 84, 67, 72, 96, 79, 85, 62];
        $enrollments = Enrollments::all();
        $exams = Exams::all();

        foreach ($enrollments as $i => $enrollment) {
            if ($i >= count($scores)) break;
            $score = $scores[$i];
            Results::firstOrCreate(
                ['enrollment_id' => $enrollment->id, 'exam_id' => $exams[$i % $exams->count()]->id],
                ['score' => $score, 'grade' => $this->calculateGrade($score)]
            );
        }
    }

    private function calculateGrade(float $score): string
    {
        if ($score >= 95) return 'A+';
        if ($score >= 90) return 'A';
        if ($score >= 85) return 'A-';
        if ($score >= 80) return 'B+';
        if ($score >= 75) return 'B';
        if ($score >= 70) return 'B-';
        if ($score >= 65) return 'C+';
        if ($score >= 60) return 'C';
        if ($score >= 55) return 'D';
        return 'F';
    }

    private function seedInvoicesAndPayments(): void
    {
        $enrollments = Enrollments::all();

        foreach ($enrollments->take(8) as $i => $enrollment) {
            $invoice = Invoices::firstOrCreate(
                ['enrollment_id' => $enrollment->id],
                [
                    'total_amount' => 120.00 + ($i % 4) * 5,
                    'due_date' => '2026-10-01',
                    'status' => $i % 3 !== 0 ? 'Paid' : 'Unpaid',
                ]
            );

            if ($i % 3 !== 0) {
                Payments::firstOrCreate(
                    ['invoice_id' => $invoice->id],
                    [
                        'amount_paid' => 120.00,
                        'payment_date' => '2026-09-15',
                        'payment_method' => $i % 2 === 0 ? 'Cash' : 'Bank Transfer',
                        'status' => 'Completed',
                    ]
                );
            }
        }
    }

    private function seedPayrolls(): void
    {
        $teachers = Teachers::all();

        foreach ($teachers as $i => $teacher) {
            $base = 1200.00 + $i * 100;
            $bonus = $i % 2 === 0 ? 50.00 : 0.00;
            Payrolls::firstOrCreate(
                ['teacher_id' => $teacher->id, 'pay_date' => '2026-09-30'],
                [
                    'base_salary' => $base,
                    'bonus' => $bonus,
                    'deduction' => 20.00,
                    'net_salary' => $base + $bonus - 20.00,
                    'pay_period_start' => '2026-09-01',
                    'pay_period_end' => '2026-09-30',
                ]
            );
        }
    }

    private function seedLibraryData(): void
    {
        $authors = [
            ['J.K. Rowling', 'Female', 'British', '1965-07-31'],
            ['George Orwell', 'Male', 'British', '1903-06-25'],
            ['Harper Lee', 'Female', 'American', '1926-04-28'],
            ['F. Scott Fitzgerald', 'Male', 'American', '1896-09-24'],
            ['Gabriel Garcia Marquez', 'Male', 'Colombian', '1927-03-06'],
        ];

        $createdAuthors = [];
        foreach ($authors as $a) {
            $createdAuthors[] = Authors::firstOrCreate(['name' => $a[0]], [
                'gender' => $a[1],
                'nation' => $a[2],
                'date_of_birth' => $a[3],
            ]);
        }

        $bookTitles = [
            "Harry Potter and the Philosopher's Stone", '978-0747532743', 'Fantasy',
            '1984', '978-0451524934', 'Fiction',
            'To Kill a Mockingbird', '978-0061120084', 'Fiction',
            'The Great Gatsby', '978-0743273565', 'Classics',
            'One Hundred Years of Solitude', '978-0060883287', 'Fiction',
            'Introduction to Algorithms', '978-0262033848', 'Computer Science',
        ];

        $books = [];
        for ($i = 0; $i < count($bookTitles); $i += 3) {
            $books[] = Books::firstOrCreate(['isbn' => $bookTitles[$i + 1]], [
                'title' => $bookTitles[$i],
                'category' => $bookTitles[$i + 2],
            ]);
        }

        foreach ($books as $i => $book) {
            $author = $createdAuthors[$i % count($createdAuthors)];
            \App\Models\BookAuthors::firstOrCreate([
                'book_id' => $book->id,
                'author_id' => $author->id,
            ]);

            for ($c = 1; $c <= 3; $c++) {
                BookCopies::firstOrCreate(
                    ['barcode' => sprintf('BC-%04d-%d', $book->id, $c)],
                    ['status' => 'available', 'book_id' => $book->id]
                );
            }
        }

        $students = Students::with('user')->get();
        $copies = BookCopies::all();
        $libraryStaff = User::where('role_id', 3)->first();
        $staffId = $libraryStaff ? $libraryStaff->id : User::where('role_id', 1)->value('id');

        foreach ($students->take(5) as $i => $student) {
            $copy = $copies[$i % $copies->count()];
            $returned = $i % 2 === 0;
            $loan = BookLoans::firstOrCreate(
                ['user_id' => $student->user_id, 'book_copy_id' => $copy->id],
                [
                    'library_staff_id' => $staffId,
                    'loan_date' => '2026-09-01',
                    'due_date' => '2026-09-20',
                    'status' => $returned ? 'returned' : 'borrowed',
                    'return_date' => $returned ? '2026-09-18' : null,
                ]
            );

            if ($i < 3) {
                Fines::firstOrCreate(
                    ['book_loan_id' => $loan->id],
                    ['amount' => 2.50 + $i, 'paid_status' => $i % 2 === 0 ? 'paid' : 'unpaid']
                );
            }
        }
    }

    private function seedAttendance(): void
    {
        $students = Students::with('user')->get();
        $teacherCourses = TeacherCourses::all();
        $statuses = ['persent', 'persent', 'late', 'absent', 'permission'];

        foreach ($students as $i => $student) {
            Attendances::firstOrCreate(
                ['user_id' => $student->user_id, 'date' => '2026-09-09'],
                [
                    'time_in' => '08:00:00',
                    'time_out' => '16:00:00',
                    'status' => $statuses[$i % count($statuses)],
                    'teacher_course_id' => $teacherCourses->isNotEmpty() ? $teacherCourses[$i % $teacherCourses->count()]->id : null,
                ]
            );
        }
    }
}