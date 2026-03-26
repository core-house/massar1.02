<?php

declare(strict_types=1);

namespace Modules\Gamification\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Gamification\Models\UserGamification;
use Modules\Gamification\Models\UserPointsHistory;

class GamificationService
{
    /**
     * Add points to a user for a specific action.
     */
    public function awardPoints(User $user, int $points, string $eventType, ?string $description = null, ?Model $source = null): void
    {
        DB::transaction(function () use ($user, $points, $eventType, $description, $source) {
            $userGamification = UserGamification::firstOrCreate(['user_id' => $user->id]);

            // Update streak if needed (Daily Login logic)
            if ($eventType === 'login') {
                $this->handleLoginStreak($userGamification);
            }

            // Award points
            $userGamification->points += $points;
            $userGamification->last_activity_at = now();

            // Calculate level
            $userGamification->level = $this->calculateLevel($userGamification->points);
            $userGamification->save();

            // Record history
            UserPointsHistory::create([
                'user_id' => $user->id,
                'points' => $points,
                'event_type' => $eventType,
                'description' => $description,
                'source_id' => $source?->getKey(),
                'source_type' => $source ? get_class($source) : null,
            ]);
        });
    }

    /**
     * Update Consecutive Days (Streak)
     */
    protected function handleLoginStreak(UserGamification $game): void
    {
        $lastActivity = $game->last_activity_at;

        if (!$lastActivity) {
            $game->streak = 1;
        } else {
            $diffInDays = $lastActivity->diffInDays(now());

            if ($diffInDays === 1) {
                // Consecutive day
                $game->streak += 1;
            } elseif ($diffInDays > 1) {
                // Streak broken
                $game->streak = 1;
            }
        }
    }

    /**
     * Level calculation formula (x / 1000)
     */
    protected function calculateLevel(int $points): int
    {
        return (int) floor($points / 100) + 1; // Example: 100 pts per level
    }

    /**
     * Get user statistics.
     */
    public function getUserStats(User $user): array
    {
        $game = UserGamification::firstOrNew(['user_id' => $user->id]);
        return [
            'points' => (float) ($game->points ?? 0),
            'level'  => (int)   ($game->level  ?? 1),
            'streak' => (int)   ($game->streak ?? 0),
            'rank'   => $this->getUserRank($user->id),
        ];
    }

    /**
     * Get rank by points.
     */
    public function getUserRank(int $userId): int
    {
        return UserGamification::where('points', '>', function ($query) use ($userId) {
            $query->select('points')->from('user_gamification')->where('user_id', $userId);
        })->count() + 1;
    }
}
