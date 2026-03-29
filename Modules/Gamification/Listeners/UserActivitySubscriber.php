<?php

declare(strict_types=1);

namespace Modules\Gamification\Listeners;

use App\Models\User;
use App\Models\OperHead;
use Illuminate\Auth\Events\Login;
use Illuminate\Events\Dispatcher;
use Modules\Gamification\Services\GamificationService;
use Spatie\Activitylog\Models\Activity;

class UserActivitySubscriber
{
    private GamificationService $service;

    public function __construct(GamificationService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle user login.
     */
    public function handleUserLogin(Login $event): void
    {
        $user = $event->user;

        if (!$user instanceof User) {
            return;
        }

        // Give points for login (once per day)
        $todayLogin = \Modules\Gamification\Models\UserPointsHistory::where('user_id', $user->id)
            ->where('event_type', 'login')
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        if (!$todayLogin) {
            $this->service->awardPoints($user, 10, 'login', 'تسجيل الدخول اليومي');
        }
    }

    /**
     * Handle generic activity log entry.
     */
    public function handleActivityCreated(Activity $activity): void
    {
        $user = $activity->causer;

        if ($user instanceof User) {
            $points = 5;
            $eventType = 'activity';
            $description = $activity->description;

            if (str_contains($description, 'created')) {
                $points = 10;
                $eventType = 'create';
            } elseif (str_contains($description, 'updated')) {
                $points = 5;
                $eventType = 'update';
            }

            $this->service->awardPoints($user, $points, $eventType, $description, $activity->subject ?? null);
        }
    }

    /**
     * Handle OperHead creation (Core business operations).
     */
    public function handleOperHeadCreated(OperHead $operHead): void
    {
        $user = User::find($operHead->getAttributes()['user'] ?? null); // OperHead user column contains the ID

        if ($user) {
            $this->service->awardPoints(
                $user,
                15,
                'operation',
                'إجراء عملية: ' . $operHead->getOperationTypeText(),
                $operHead
            );
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Dispatcher  $events
     * @return void
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            Login::class,
            [static::class, 'handleUserLogin']
        );

        if (class_exists(Activity::class)) {
            $events->listen(
                'eloquent.created: Spatie\Activitylog\Models\Activity',
                [static::class, 'handleActivityCreated']
            );
        }

        // Listen for core operations
        $events->listen(
            'eloquent.created: App\Models\OperHead',
            [static::class, 'handleOperHeadCreated']
        );
    }
}
<?php

declare(strict_types=1);

namespace Modules\Gamification\Listeners;

use App\Models\User;
use App\Models\OperHead;
use Illuminate\Auth\Events\Login;
use Illuminate\Events\Dispatcher;
use Modules\Gamification\Services\GamificationService;
use Spatie\Activitylog\Models\Activity;

class UserActivitySubscriber
{
    private GamificationService $service;

    public function __construct(GamificationService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle user login.
     */
    public function handleUserLogin(Login $event): void
    {
        $user = $event->user;

        if (!$user instanceof User) {
            return;
        }

        // Give points for login (once per day)
        $todayLogin = \Modules\Gamification\Models\UserPointsHistory::where('user_id', $user->id)
            ->where('event_type', 'login')
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        if (!$todayLogin) {
            $this->service->awardPoints($user, 10, 'login', 'تسجيل الدخول اليومي');
        }
    }

    /**
     * Handle generic activity log entry.
     */
    public function handleActivityCreated(Activity $activity): void
    {
        $user = $activity->causer;

        if ($user instanceof User) {
            $points = 5;
            $eventType = 'activity';
            $description = $activity->description;

            if (str_contains($description, 'created')) {
                $points = 10;
                $eventType = 'create';
            } elseif (str_contains($description, 'updated')) {
                $points = 5;
                $eventType = 'update';
            }

            $this->service->awardPoints($user, $points, $eventType, $description, $activity->subject ?? null);
        }
    }

    /**
     * Handle OperHead creation (Core business operations).
     */
    public function handleOperHeadCreated(OperHead $operHead): void
    {
        $user = User::find($operHead->getAttributes()['user'] ?? null); // OperHead user column contains the ID

        if ($user) {
            $this->service->awardPoints(
                $user,
                15,
                'operation',
                'إجراء عملية: ' . $operHead->getOperationTypeText(),
                $operHead
            );
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Dispatcher  $events
     * @return void
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            Login::class,
            [static::class, 'handleUserLogin']
        );

        if (class_exists(Activity::class)) {
            $events->listen(
                'eloquent.created: Spatie\Activitylog\Models\Activity',
                [static::class, 'handleActivityCreated']
            );
        }

        // Listen for core operations
        $events->listen(
            'eloquent.created: App\Models\OperHead',
            [static::class, 'handleOperHeadCreated']
        );
    }
}
