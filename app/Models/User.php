<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Modules\Branches\Models\Branch;
use Modules\Inquiries\Models\UserInquiryPreference;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Authorizable, HasFactory, HasPermissions, HasRoles, Notifiable;
    // use LogsActivity; // معطل مؤقتاً للـ central database

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'is_active'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "تم {$eventName} المستخدم");
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * التحقق من أن المستخدم هو admin user
     */

    public function isAdmin(): bool
    {
        // إذا كنا في السنترال، تحقق بالإيميل
        if (!app()->bound(\Stancl\Tenancy\Tenancy::class) || !tenancy()->initialized) {
            return $this->email === 'admin@admin.com';
        }

        // إذا كنا في تينانت، استخدم الصلاحيات العادية
        return $this->hasRole('admin');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn(string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function employee()
    {
        return $this->hasOne(\Modules\HR\Models\Employee::class, 'user_id');
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_user')->withTimestamps();
    }

    public function assignedInquiries()
    {
        return $this->belongsToMany(
            \Modules\Inquiries\Models\Inquiry::class,
            'inquiry_assigned_engineers',
            'user_id',
            'inquiry_id'
        )
            ->withPivot('assigned_at', 'notes')
            ->withTimestamps();
    }

    public function projects()
    {
        return $this->belongsToMany(\Modules\Progress\Models\ProjectProgress::class, 'project_user', 'user_id', 'project_id')->withTimestamps();
    }

    public function getEmployeeIdAttribute()
    {
        return $this->employee?->id;
    }

    public function getFingerPrintIdAttribute()
    {
        return $this->employee?->finger_print_id ?: $this->id;
    }

    public function getFingerPrintNameAttribute()
    {
        return $this->employee?->finger_print_name ?: $this->name;
    }

    public function receivesBroadcastNotificationsOn()
    {
        return 'App.Models.User.' . $this->id;
    }

    public function inquiryPreferences()
    {
        return $this->hasOne(UserInquiryPreference::class);
    }

    public function loginSessions()
    {
        return $this->hasMany(LoginSession::class);
    }

    public function activeSessions()
    {
        return $this->hasMany(LoginSession::class)->whereNull('logout_at');
    }

    public function hasRole($roles, $guard = null): bool
    {
        // إذا لم نكن داخل تينانت (أي نحن في السنترال)، لا تحاول البحث في الداتا بيز
        if (!app()->bound(\Stancl\Tenancy\Tenancy::class) || !tenancy()->initialized) {
            // هنا يمكنك وضع منطق بديل للسنترال
            return $this->email === 'admin@admin.com';
        }

        // إذا كنا داخل تينانت، استدعي الوظيفة الأصلية لـ Spatie
        return $this->parentHasRole($roles, $guard);
    }

    // أضف هذا الـ Alias للوصول للدالة الأصلية من Trait Spatie
    use HasRoles {
        hasRole as parentHasRole;
    }
}
