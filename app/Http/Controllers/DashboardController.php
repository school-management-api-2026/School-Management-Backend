<?php

namespace App\Http\Controllers;

use App\Models\Attendances;
use App\Models\BookCopies;
use App\Models\Courses;
use App\Models\Enrollments;
use App\Models\Exams;
use App\Models\Invoices;
use App\Models\Parents;
use App\Models\Payments;
use App\Models\Students;
use App\Models\Teachers;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function summary()
    {
        $today = Carbon::today();
        $months = collect(range(5, 0))->map(fn ($i) => $today->copy()->subMonths($i));
        $days = collect(range(6, 0))->map(fn ($i) => $today->copy()->subDays($i));

        $enrollmentTrend = $months->map(fn (Carbon $m) => [
            'month' => $m->translatedFormat('M'),
            'count' => Enrollments::whereYear('enrollment_date', $m->year)->whereMonth('enrollment_date', $m->month)->count(),
        ]);

        $attendanceOverview = $days->map(fn (Carbon $d) => [
            'day' => $d->format('D'),
            'present' => Attendances::whereDate('date', $d)->where('status', 'persent')->count(),
            'absent' => Attendances::whereDate('date', $d)->where('status', 'absent')->count(),
            'late' => Attendances::whereDate('date', $d)->where('status', 'late')->count(),
        ]);

        $revenueOverview = $months->map(fn (Carbon $m) => [
            'month' => $m->translatedFormat('M'),
            'revenue' => (float) Payments::whereYear('payment_date', $m->year)->whereMonth('payment_date', $m->month)->sum('amount_paid'),
        ]);

        $libraryStats = collect([
            ['name' => 'Available', 'value' => BookCopies::where('status', 'available')->count()],
            ['name' => 'Borrowed', 'value' => BookCopies::where('status', 'borrowed')->count()],
            ['name' => 'Lost', 'value' => BookCopies::where('status', 'lost')->count()],
        ])->filter(fn ($s) => $s['value'] > 0)->values();

        $attendanceToday = [
            'present' => Attendances::whereDate('date', $today)->where('status', 'persent')->count(),
            'absent' => Attendances::whereDate('date', $today)->where('status', 'absent')->count(),
            'late' => Attendances::whereDate('date', $today)->where('status', 'late')->count(),
            'excused' => Attendances::whereDate('date', $today)->where('status', 'permission')->count(),
        ];

        $totalRevenue = (float) Payments::sum('amount_paid');

        $outstanding = 0;
        foreach (Invoices::with('payments')->get() as $invoice) {
            $paid = $invoice->payments->sum('amount_paid');
            $remaining = (float) $invoice->total_amount - $paid;
            if ($remaining > 0) {
                $outstanding += $remaining;
            }
        }

        $recentStudents = Students::with('user')->latest()->take(5)->get()->map(fn ($s) => [
            'id' => $s->id,
            'name' => $s->user->name,
            'email' => $s->user->email,
            'status' => 'active',
        ]);

        $recentEnrollments = Enrollments::with('student.user', 'course.subject')->latest()->take(5)->get()->map(fn ($e) => [
            'id' => $e->id,
            'studentName' => $e->student->user->name,
            'courseName' => $e->course->subject?->name ?? $e->course->name,
            'status' => $e->status,
            'enrollmentDate' => $e->enrollment_date,
        ]);

        $todayAttendance = Attendances::with('user')->whereDate('date', $today)->latest()->take(5)->get()->map(fn ($a) => [
            'id' => $a->id,
            'userName' => $a->user->name,
            'timeIn' => $a->time_in,
            'timeOut' => $a->time_out,
            'status' => $a->status,
        ]);

        $upcomingExams = Exams::with('course.subject', 'teacher.user')->whereDate('exam_date', '>=', $today)->orderBy('exam_date')->take(5)->get()->map(fn ($ex) => [
            'id' => $ex->id,
            'courseName' => $ex->course->subject?->name ?? $ex->course->name,
            'teacherName' => $ex->teacher?->user?->name,
            'examType' => $ex->exam_type,
            'examDate' => $ex->exam_date,
        ]);

        $recentPayments = Payments::with('invoice')->latest()->take(5)->get()->map(fn ($p) => [
            'id' => $p->id,
            'invoiceRef' => 'INV-' . str_pad($p->invoice_id, 3, '0', STR_PAD_LEFT),
            'amount' => (float) $p->amount_paid,
            'paymentDate' => $p->payment_date,
            'paymentMethod' => $p->payment_method,
            'status' => $p->status,
        ]);

        $dashboardStats = [
            'totalStudents' => Students::count(),
            'totalTeachers' => Teachers::count(),
            'totalParents' => Parents::count(),
            'totalCourses' => Courses::count(),
            'totalEnrollments' => Enrollments::count(),
            'attendanceToday' => $attendanceToday,
            'totalRevenue' => $totalRevenue,
            'outstandingPayments' => (float) round($outstanding, 2),
        ];

        return response()->json([
            'message' => 'Get dashboard summary successfully',
            'data' => [
                'dashboardStats' => $dashboardStats,
                'tables' => [
                    'recentStudents' => $recentStudents,
                    'recentEnrollments' => $recentEnrollments,
                    'todayAttendance' => $todayAttendance,
                    'upcomingExams' => $upcomingExams,
                    'recentPayments' => $recentPayments,
                ],
                'charts' => [
                    'enrollmentTrend' => $enrollmentTrend,
                    'attendanceOverview' => $attendanceOverview,
                    'revenueOverview' => $revenueOverview,
                    'libraryStats' => $libraryStats,
                ],
            ],
        ], 200);
    }
}