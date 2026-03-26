<?php

declare(strict_types=1);

namespace Modules\Recruitment\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Recruitment\Models\Cv;
use Modules\Recruitment\Models\Contract;
use Modules\Recruitment\Models\Interview;
use Modules\Recruitment\Models\JobPosting;
use Modules\Recruitment\Models\Termination;
use Modules\Recruitment\Models\Onboarding;
use Illuminate\Support\Facades\DB;

class RecruitmentDashboardController extends Controller
{
    public function index()
    {
        // Basic Statistics
        $stats = [
            // Job Postings
            'total_job_postings' => JobPosting::count(),
            'active_job_postings' => JobPosting::where('status', 'active')->count(),
            'closed_job_postings' => JobPosting::where('status', 'closed')->count(),
            'expired_job_postings' => JobPosting::where('status', 'expired')->count(),
            
            // CVs
            'total_cvs' => Cv::count(),
            'cvs_this_month' => Cv::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'cvs_last_month' => Cv::whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->count(),
            
            // Interviews
            'total_interviews' => Interview::count(),
            'scheduled_interviews' => Interview::where('status', 'scheduled')->count(),
            'completed_interviews' => Interview::where('status', 'completed')->count(),
            'cancelled_interviews' => Interview::where('status', 'cancelled')->count(),
            'accepted_interviews' => Interview::where('result', 'accepted')->count(),
            'rejected_interviews' => Interview::where('result', 'rejected')->count(),
            'pending_interviews' => Interview::where('result', 'pending')->orWhereNull('result')->count(),
            
            // Contracts
            'total_contracts' => Contract::count(),
            'recent_contracts' => Contract::whereDate('created_at', '>=', now()->subDays(30))->count(),
            'contracts_this_month' => Contract::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            
            // Onboardings
            'total_onboardings' => Onboarding::count(),
            'pending_onboardings' => Onboarding::where('status', 'pending')->count(),
            'in_progress_onboardings' => Onboarding::where('status', 'in_progress')->count(),
            'completed_onboardings' => Onboarding::where('status', 'completed')->count(),
            'cancelled_onboardings' => Onboarding::where('status', 'cancelled')->count(),
            'employees_created_from_onboarding' => Onboarding::whereNotNull('employee_id')->count(),
            
            // Terminations
            'total_terminations' => Termination::count(),
            'pending_terminations' => Termination::whereNull('approved_by')->count(),
            'terminations_this_month' => Termination::whereMonth('termination_date', now()->month)
                ->whereYear('termination_date', now()->year)
                ->count(),
            
            // Conversion Rates
            'cv_to_interview_rate' => $this->calculateConversionRate(Cv::count(), Interview::count()),
            'interview_to_contract_rate' => $this->calculateConversionRate(
                Interview::where('result', 'accepted')->count(),
                Contract::count()
            ),
            'onboarding_to_employee_rate' => $this->calculateConversionRate(
                Onboarding::where('status', 'completed')->count(),
                Onboarding::whereNotNull('employee_id')->count()
            ),
        ];

        // Monthly Statistics (Last 6 Months)
        $monthlyStats = $this->getMonthlyStatistics();

        // Status Distribution
        $statusDistribution = [
            'job_postings' => [
                'active' => JobPosting::where('status', 'active')->count(),
                'closed' => JobPosting::where('status', 'closed')->count(),
                'expired' => JobPosting::where('status', 'expired')->count(),
            ],
            'interviews' => [
                'scheduled' => Interview::where('status', 'scheduled')->count(),
                'completed' => Interview::where('status', 'completed')->count(),
                'cancelled' => Interview::where('status', 'cancelled')->count(),
                'rescheduled' => Interview::where('status', 'rescheduled')->count(),
            ],
            'onboardings' => [
                'pending' => Onboarding::where('status', 'pending')->count(),
                'in_progress' => Onboarding::where('status', 'in_progress')->count(),
                'completed' => Onboarding::where('status', 'completed')->count(),
                'cancelled' => Onboarding::where('status', 'cancelled')->count(),
            ],
        ];

        // Interview Results Distribution
        $interviewResults = [
            'accepted' => Interview::where('result', 'accepted')->count(),
            'rejected' => Interview::where('result', 'rejected')->count(),
            'pending' => Interview::where('result', 'pending')->orWhereNull('result')->count(),
            'on_hold' => Interview::where('result', 'on_hold')->count(),
        ];

        // Termination Types Distribution
        $terminationTypes = [
            'resignation' => Termination::where('termination_type', 'resignation')->count(),
            'dismissal' => Termination::where('termination_type', 'dismissal')->count(),
            'death' => Termination::where('termination_type', 'death')->count(),
            'retirement' => Termination::where('termination_type', 'retirement')->count(),
        ];

        // Recent Data
        $upcomingInterviews = Interview::where('status', 'scheduled')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit(5)
            ->with(['cv', 'jobPosting', 'interviewer'])
            ->get();

        $recentJobPostings = JobPosting::where('status', 'active')
            ->orderByDesc('created_at')
            ->limit(5)
            ->with('job')
            ->get();

        $recentOnboardings = Onboarding::orderByDesc('created_at')
            ->limit(5)
            ->with(['cv', 'contract', 'employee'])
            ->get();

        $recentContracts = Contract::orderByDesc('created_at')
            ->limit(5)
            ->with(['employee', 'contractType'])
            ->get();

        return view('recruitment::dashboard.index', compact(
            'stats',
            'monthlyStats',
            'statusDistribution',
            'interviewResults',
            'terminationTypes',
            'upcomingInterviews',
            'recentJobPostings',
            'recentOnboardings',
            'recentContracts'
        ));
    }

    /**
     * Calculate conversion rate percentage
     */
    private function calculateConversionRate(int $total, int $converted): float
    {
        if ($total === 0) {
            return 0.0;
        }

        return round(($converted / $total) * 100, 2);
    }

    /**
     * Get monthly statistics for the last 6 months
     */
    private function getMonthlyStatistics(): array
    {
        $months = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('Y-m');
            
            $data[] = [
                'month' => $date->format('M Y'),
                'cvs' => Cv::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'interviews' => Interview::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'contracts' => Contract::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'onboardings' => Onboarding::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }

        return $data;
    }
}

