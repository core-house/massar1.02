<?php

namespace Modules\Inquiries\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\BranchScope);
    }

    // ========== Relationships ==========

    public function roles()
    {
        return $this->belongsToMany(
            ContactRole::class,
            'contact_role_assignments',
            'contact_id',
            'contact_role_id'      // <-- هنا
        )
            ->withPivot(['is_primary', 'assigned_date', 'notes'])
            ->withTimestamps();
    }

    // العلاقة الجديدة: المؤسسات اللي الشخص بيشتغل فيها
    public function organizations()
    {
        return $this->belongsToMany(Contact::class, 'contact_organization_relations', 'person_id', 'organization_id')
            ->withPivot(['job_title_in_org', 'is_primary', 'start_date', 'end_date'])
            ->withTimestamps();
    }

    // العلاقة الجديدة: الأشخاص اللي شغالين في المؤسسة
    public function employees()
    {
        return $this->belongsToMany(Contact::class, 'contact_organization_relations', 'organization_id', 'person_id')
            ->withPivot(['job_title_in_org', 'is_primary', 'start_date', 'end_date'])
            ->withTimestamps();
    }

    // المؤسسة الأساسية للشخص
    public function primaryOrganization()
    {
        return $this->organizations()->wherePivot('is_primary', true)->first();
    }

    public function inquiries()
    {
        return $this->belongsToMany(Inquiry::class, 'inquiry_contacts')
            ->withPivot(['role_id', 'is_primary', 'involvement_percentage', 'assigned_date', 'responsibilities'])
            ->withTimestamps();
    }

    // ========== Scopes ==========

    public function scopePersons($query)
    {
        return $query->where('type', 'person');
    }

    public function scopeOrganizations($query)
    {
        return $query->where('type', 'organization');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithRole($query, $roleSlug)
    {
        return $query->whereHas('roles', function ($q) use ($roleSlug) {
            $q->where('slug', $roleSlug);
        });
    }

    // ========== Helper Methods ==========

    public function isPerson()
    {
        return $this->type === 'person';
    }

    public function isOrganization()
    {
        return $this->type === 'organization';
    }

    public function hasRole($roleSlug)
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    public function getPrimaryRole()
    {
        return $this->roles()->wherePivot('is_primary', true)->first();
    }

    public function assignRole($roleId, $isPrimary = false, $notes = null)
    {
        // إذا كان primary، نلغي primary من باقي الأدوار
        if ($isPrimary) {
            $this->roles()->updateExistingPivot(
                $this->roles()->pluck('id')->toArray(),
                ['is_primary' => false]
            );
        }

        return $this->roles()->syncWithoutDetaching([
            $roleId => [
                'is_primary' => $isPrimary,
                'assigned_date' => now(),
                'notes' => $notes
            ]
        ]);
    }

    public function removeRole($roleId)
    {
        return $this->roles()->detach($roleId);
    }

    // ربط الشخص بمؤسسة
    public function attachToOrganization($organizationId, $jobTitle = null, $isPrimary = false)
    {
        if ($isPrimary) {
            $this->organizations()->updateExistingPivot(
                $this->organizations()->pluck('id')->toArray(),
                ['is_primary' => false]
            );
        }

        return $this->organizations()->syncWithoutDetaching([
            $organizationId => [
                'job_title_in_org' => $jobTitle,
                'is_primary' => $isPrimary,
                'start_date' => now()
            ]
        ]);
    }

    public function getFullDisplayNameAttribute()
    {
        if ($this->isPerson()) {
            $primaryOrg = $this->primaryOrganization();
            if ($primaryOrg) {
                return "{$this->name} ({$primaryOrg->name})";
            }
        }
        return $this->name;
    }
}
