<?php

namespace Modules\Inquiries\Models;

use App\Models\{City, Town, Client, Project};
use App\Enums\ClientType;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Modules\Inquiries\Enums\{KonTitle, StatusForKon, InquiryStatus, QuotationStateEnum};

class Inquiry extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => InquiryStatus::class,
        'status_for_kon' => StatusForKon::class,
        'kon_title' => KonTitle::class,
        'quotation_state' => QuotationStateEnum::class, // أضف هذا السطر
        'inquiry_date' => 'date',
        'req_submittal_date' => 'date',
        'project_start_date' => 'date',
        'estimation_start_date' => 'date',
        'estimation_finished_date' => 'date',
        'submitting_date' => 'date',
        'project_documents' => 'array',
        'submittal_checklist' => 'array',
        'working_conditions' => 'array',
    ];

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'inquiry_contacts')
            ->withPivot(['role_id', 'is_primary', 'involvement_percentage', 'assigned_date', 'responsibilities'])
            ->withTimestamps();
    }

    // ========== Helper Methods للوصول السريع ==========

    // جلب الـ Contacts حسب الدور
    public function getContactsByRole($roleSlug)
    {
        return $this->contacts()->whereHas('roles', function ($q) use ($roleSlug) {
            $q->where('slug', $roleSlug);
        })->get();
    }

    // جلب الـ Primary Contact لدور معين
    public function getPrimaryContactForRole($roleSlug)
    {
        return $this->contacts()
            ->wherePivot('is_primary', true)
            ->whereHas('roles', function ($q) use ($roleSlug) {
                $q->where('slug', $roleSlug);
            })
            ->first();
    }

    // الـ Client الرئيسي
    public function getPrimaryClientAttribute()
    {
        return $this->getPrimaryContactForRole('client');
    }

    // المقاول الرئيسي
    public function getPrimaryMainContractorAttribute()
    {
        return $this->getPrimaryContactForRole('main_contractor');
    }

    // الاستشاري الرئيسي
    public function getPrimaryConsultantAttribute()
    {
        return $this->getPrimaryContactForRole('consultant');
    }

    // المالك الرئيسي
    public function getPrimaryOwnerAttribute()
    {
        return $this->getPrimaryContactForRole('owner');
    }

    // المهندس المعين
    public function getAssignedEngineerAttribute()
    {
        return $this->getPrimaryContactForRole('engineer');
    }

    // كل الـ Clients
    public function clients()
    {
        return $this->getContactsByRole('client');
    }

    // كل المقاولين
    public function mainContractors()
    {
        return $this->getContactsByRole('main_contractor');
    }

    // كل الاستشاريين
    public function consultants()
    {
        return $this->getContactsByRole('consultant');
    }

    // كل الملاك
    public function owners()
    {
        return $this->getContactsByRole('owner');
    }

    // كل المهندسين
    public function engineers()
    {
        return $this->getContactsByRole('engineer');
    }

    // ========== إضافة Contact للـ Inquiry ==========

    public function assignContact($contactId, $roleSlug, $isPrimary = false, $additionalData = [])
    {
        $role = ContactRole::where('slug', $roleSlug)->firstOrFail();

        // لو primary، نشيل الـ primary من باقي الـ contacts في نفس الدور
        if ($isPrimary) {
            $this->contacts()
                ->wherePivot('role_id', $role->id)
                ->update(['inquiry_contacts.is_primary' => false]);
        }

        return $this->contacts()->attach($contactId, array_merge([
            'role_id' => $role->id,
            'is_primary' => $isPrimary,
            'assigned_date' => now(),
        ], $additionalData));
    }

    // إزالة Contact من الـ Inquiry
    public function removeContact($contactId, $roleId = null)
    {
        if ($roleId) {
            return $this->contacts()->wherePivot('role_id', $roleId)->detach($contactId);
        }
        return $this->contacts()->detach($contactId);
    }

    // ========== Backward Compatibility (للكود القديم) ==========
    // لو عندك كود قديم بيستخدم العلاقات القديمة

    // public function client()
    // {
    //     return $this->belongsToMany(Contact::class, 'inquiry_contacts')
    //         ->wherePivot('role_id', ContactRole::where('slug', 'client')->value('id'))
    //         ->wherePivot('is_primary', true);
    // }

    // public function mainContractor()
    // {
    //     return $this->belongsToMany(Contact::class, 'inquiry_contacts')
    //         ->wherePivot('role_id', ContactRole::where('slug', 'main_contractor')->value('id'))
    //         ->wherePivot('is_primary', true);
    // }

    // public function consultant()
    // {
    //     return $this->belongsTo(Client::class, 'consultant_id');
    // }

    // public function owner()
    // {
    //     return $this->belongsTo(Client::class, 'owner_id');
    // }

    // public function assignedEngineer()
    // {
    //     return $this->belongsTo(Client::class, 'assigned_engineer_id');
    // }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function town()
    {
        return $this->belongsTo(Town::class);
    }

    public function submittalChecklists()
    {
        return $this->belongsToMany(SubmittalChecklist::class, 'inquiry_submittal_checklist');
    }

    public function workConditions()
    {
        return $this->belongsToMany(WorkCondition::class, 'inquiry_work_condition');
    }

    public function workType()
    {
        return $this->belongsTo(WorkType::class);
    }

    public function workTypes()
    {
        return $this->belongsToMany(
            WorkType::class,
            'inquiry_work_type',
            'inquiry_id',
            'work_type_id'
        )
            ->withPivot(['hierarchy_path', 'description', 'order'])
            ->orderBy('inquiry_work_type.order')
            ->withTimestamps();
    }

    public function inquirySource()
    {
        return $this->belongsTo(InquirySource::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function projectSize()
    {
        return $this->belongsTo(ProjectSize::class, 'project_size_id');
    }


    public function comments()
    {
        return $this->hasMany(InquiryComment::class)->with('user')->latest();
    }

    public function quotationUnits()
    {
        return $this->belongsToMany(QuotationUnit::class, 'inquiry_quotation_info', 'inquiry_id', 'quotation_unit_id')
            ->withPivot('quotation_type_id')
            ->withTimestamps();
    }

    public function projectDocuments()
    {
        return $this->belongsToMany(
            InquiryDocument::class,
            'inquiry_project_document',
            'inquiry_id',
            'project_document_id'
        )->withTimestamps();
    }

    public static function getStatusOptions()
    {
        return InquiryStatus::cases();
    }

    public static function getStatusForKonOptions()
    {
        return StatusForKon::cases();
    }

    public static function getKonTitleOptions()
    {
        return KonTitle::cases();
    }

    public static function getQuotationStateOptions()
    {
        return QuotationStateEnum::cases();
    }

    public function getTotalConditionsScoreAttribute()
    {
        $score = 0;
        if (is_array($this->working_conditions)) {
            foreach ($this->working_conditions as $condition) {
                if ($condition['checked'] ?? false) {
                    $score += (int) ($condition['value'] ?? 0);
                }
            }
        }
        return $score;
    }

    public function getTotalSubmittalScoreAttribute()
    {
        $score = 0;
        if (is_array($this->submittal_checklist)) {
            foreach ($this->submittal_checklist as $item) {
                if ($item['checked'] ?? false) {
                    $score += (int) ($item['value'] ?? 0);
                }
            }
        }
        return $score;
    }

    public function getProjectDifficultyAttribute()
    {
        $score = $this->total_conditions_score;
        if ($score < 6) return 1;
        if ($score <= 10) return 2;
        if ($score <= 15) return 3;
        return 4;
    }

    public static function safeFrom(string $value): ?self
    {
        return self::tryFrom(strtolower($value));
    }
}
