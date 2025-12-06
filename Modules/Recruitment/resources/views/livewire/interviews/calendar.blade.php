<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use Modules\Recruitment\Models\Interview;
use Livewire\Attributes\Computed;

new class extends Component {
    public string $currentMonth;
    public string $currentYear;

    public function mount(): void
    {
        $this->currentMonth = date('Y-m');
        $this->currentYear = date('Y');
    }

    #[Computed]
    public function interviews(): \Illuminate\Database\Eloquent\Collection
    {
        $startDate = $this->currentMonth . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

        return Interview::with(['cv', 'jobPosting', 'interviewer'])
            ->whereBetween('scheduled_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ])
            ->orderBy('scheduled_at')
            ->get();
    }

    #[Computed]
    public function calendarDays(): array
    {
        $year = (int) substr($this->currentMonth, 0, 4);
        $month = (int) substr($this->currentMonth, 5, 2);
        
        $firstDay = mktime(0, 0, 0, $month, 1, $year);
        $daysInMonth = date('t', $firstDay);
        $dayOfWeek = date('w', $firstDay);
        
        $days = [];
        $day = 1;
        
        // Fill empty cells before first day
        for ($i = 0; $i < $dayOfWeek; $i++) {
            $days[] = null;
        }
        
        // Fill days of month
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $days[] = $date;
        }
        
        return $days;
    }

    public function getInterviewsForDate(string $date): \Illuminate\Database\Eloquent\Collection
    {
        return $this->interviews->filter(function ($interview) use ($date) {
            return $interview->scheduled_at && 
                   $interview->scheduled_at->format('Y-m-d') === $date;
        })->values();
    }

    public function previousMonth(): void
    {
        $date = date_create($this->currentMonth . '-01');
        date_modify($date, '-1 month');
        $this->currentMonth = $date->format('Y-m');
    }

    public function nextMonth(): void
    {
        $date = date_create($this->currentMonth . '-01');
        date_modify($date, '+1 month');
        $this->currentMonth = $date->format('Y-m');
    }

    public function goToToday(): void
    {
        $this->currentMonth = date('Y-m');
    }
}; ?>

<div>
    <!-- Calendar Header -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">{{ __('recruitment.interview_calendar') }}</h4>
                    <p class="text-muted mb-0">{{ date('F Y', strtotime($this->currentMonth . '-01')) }}</p>
                </div>
                <div class="d-flex gap-2">
                    <button wire:click="previousMonth" class="btn btn-outline-secondary">
                        <i class="mdi mdi-chevron-left"></i> {{ __('recruitment.previous_month') }}
                    </button>
                    <button wire:click="goToToday" class="btn btn-outline-primary">
                        {{ __('recruitment.today') }}
                    </button>
                    <button wire:click="nextMonth" class="btn btn-outline-secondary">
                        {{ __('recruitment.next_month') }} <i class="mdi mdi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th class="text-center">{{ __('recruitment.sunday') }}</th>
                            <th class="text-center">{{ __('recruitment.monday') }}</th>
                            <th class="text-center">{{ __('recruitment.tuesday') }}</th>
                            <th class="text-center">{{ __('recruitment.wednesday') }}</th>
                            <th class="text-center">{{ __('recruitment.thursday') }}</th>
                            <th class="text-center">{{ __('recruitment.friday') }}</th>
                            <th class="text-center">{{ __('recruitment.saturday') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(array_chunk($this->calendarDays, 7) as $week)
                            <tr>
                                @foreach($week as $date)
                                    <td class="align-top" style="height: 150px; vertical-align: top;">
                                        @if($date)
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <strong>{{ date('d', strtotime($date)) }}</strong>
                                                @if($date === date('Y-m-d'))
                                                    <span class="badge bg-primary">{{ __('recruitment.today') }}</span>
                                                @endif
                                            </div>
                                            <div class="small">
                                                @foreach($this->getInterviewsForDate($date) as $interview)
                                                    <div class="mb-1 p-1 rounded" 
                                                         style="background-color: {{ $interview->status === 'completed' ? '#d4edda' : ($interview->status === 'cancelled' ? '#f8d7da' : '#fff3cd') }}; font-size: 0.75rem;">
                                                        <div class="fw-bold">{{ $interview->cv?->name ?? __('recruitment.unknown') }}</div>
                                                        <div class="text-muted">{{ $interview->scheduled_at?->format('H:i') }}</div>
                                                        <div class="text-muted">{{ $interview->interview_type }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
