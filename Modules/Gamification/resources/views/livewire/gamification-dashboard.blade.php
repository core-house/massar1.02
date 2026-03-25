<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use Modules\Gamification\Models\UserGamification;
use Modules\Gamification\Models\UserPointsHistory;
use Modules\Gamification\Services\GamificationService;
use App\Models\User;

new class extends Component {
    public $userStats;
    public $leaderboard;
    public $recentHistory;

    public function mount(GamificationService $service)
    {
        $user = auth()->user();
        $this->userStats = $service->getUserStats($user);
        
        $this->leaderboard = UserGamification::with('user')
            ->orderByDesc('points')
            ->limit(5)
            ->get();

        $this->recentHistory = UserPointsHistory::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();
    }

    public function getPointsToNextLevel()
    {
        return (($this->userStats['level']) * 100) - ($this->userStats['points']);
    }

    public function getProgress()
    {
        return (int) ($this->userStats['points'] % 100);
    }
}; ?>

<div class="gamification-dashboard">
    <div class="card border-0 shadow-sm" style="border-radius: 1rem; overflow: hidden;">
        <div class="card-body p-0">
            <div class="d-flex align-items-stretch flex-wrap">

                {{-- Stats Bar --}}
                <div class="gami-stats-bar d-flex align-items-center gap-3 px-3 py-2 flex-wrap"
                     style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: white; min-width: 280px; flex: 1;">
                    <i class="las la-trophy fs-4 opacity-75"></i>
                    <div>
                        <div class="x-small opacity-75">{{ __('gamification.my_stats') }}</div>
                        <div class="fw-bold">
                            <span class="fs-5">{{ number_format($userStats['points']) }}</span>
                            <span class="x-small opacity-75 ms-1">{{ __('gamification.points') }}</span>
                        </div>
                    </div>
                    <div class="vr bg-white opacity-25 mx-1" style="height: 30px;"></div>
                    <div class="text-center">
                        <div class="fw-bold">{{ __('gamification.level') }} {{ $userStats['level'] }}</div>
                        <div class="progress bg-white bg-opacity-20 mt-1" style="height: 5px; width: 80px; border-radius: 3px;">
                            <div class="progress-bar bg-white" style="width: {{ $this->getProgress() }}%; border-radius: 3px;"></div>
                        </div>
                    </div>
                    <div class="vr bg-white opacity-25 mx-1" style="height: 30px;"></div>
                    <div class="text-center">
                        <div class="fw-bold">#{{ $userStats['rank'] }}</div>
                        <div class="x-small opacity-75">{{ __('gamification.rank') }}</div>
                    </div>
                    <div class="vr bg-white opacity-25 mx-1" style="height: 30px;"></div>
                    <div class="text-center">
                        <div class="fw-bold">{{ $userStats['streak'] }}</div>
                        <div class="x-small opacity-75">{{ __('gamification.streak') }}</div>
                    </div>
                </div>

                {{-- Leaderboard --}}
                <div class="px-3 py-2 border-start" style="flex: 1; min-width: 220px;">
                    <div class="x-small fw-bold text-muted mb-2 text-uppercase">
                        <i class="las la-medal text-warning me-1"></i>{{ __('gamification.leaderboard') }}
                    </div>
                    <div class="d-flex flex-column gap-1">
                        @foreach($leaderboard as $index => $row)
                        <div class="d-flex align-items-center gap-2 {{ auth()->id() === $row->user_id ? 'fw-bold text-primary' : '' }}">
                            <span class="x-small text-muted" style="width: 16px;">{{ $index + 1 }}</span>
                            <span class="x-small flex-grow-1 text-truncate" style="max-width: 120px;">{{ $row->user->name }}</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary x-small">{{ number_format($row->points) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Recent Activity --}}
                <div class="px-3 py-2 border-start" style="flex: 1; min-width: 220px;">
                    <div class="x-small fw-bold text-muted mb-2 text-uppercase">
                        <i class="las la-history text-primary me-1"></i>{{ __('gamification.activity_history') }}
                    </div>
                    <div class="d-flex flex-column gap-1">
                        @forelse($recentHistory as $log)
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge {{ $log->event_type === 'login' ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary' }} x-small">
                                +{{ $log->points }}
                            </span>
                            <span class="x-small text-truncate flex-grow-1">{{ __('gamification.event_types.' . $log->event_type) }}</span>
                            <span class="x-small text-muted">{{ $log->created_at->diffForHumans(null, true) }}</span>
                        </div>
                        @empty
                        <span class="x-small text-muted">{{ __('gamification.descriptions.daily_login') }}</span>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .gamification-dashboard .x-small { font-size: 0.7rem; }
        .gamification-dashboard .progress-bar { transition: width 1s ease-in-out; }
    </style>
</div>
