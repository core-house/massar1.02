<?php
// Modules/Inquiries/Models/UserInquiryPreference.php

namespace Modules\Inquiries\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserInquiryPreference extends Model
{
    protected $fillable = [
        'user_id',
        'visible_columns',
        'filters',
        'sort_column',
        'sort_direction',
        'per_page'
    ];

    protected $casts = [
        'visible_columns' => 'array',
        'filters' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // الأعمدة المتاحة للعرض
    public static function getAvailableColumns(): array
    {
        return [
            'id'                 => __('inquiries::inquiries.inquiry_id'),
            'project'            => __('inquiries::inquiries.project'),
            'client'             => __('inquiries::inquiries.client'),
            'main_contractor'    => __('inquiries::inquiries.main_contractor'),
            'consultant'         => __('inquiries::inquiries.consultant'),
            'owner'              => __('inquiries::inquiries.owner'),
            'inquiry_date'       => __('inquiries::inquiries.inquiry_received_date'),
            'req_submittal_date' => __('inquiries::inquiries.required_submission_date'),
            'project_start_date' => __('inquiries::inquiries.project_start_date'),
            'status'             => __('inquiries::inquiries.status'),
            'status_for_kon'     => __('inquiries::inquiries.inquiry_status_for_kon'),
            'kon_title'          => __('inquiries::inquiries.kon_position'),
            'quotation_state'    => __('inquiries::inquiries.quotation_state'),
            'work_type'          => __('inquiries::inquiries.work_type'),
            'inquiry_source'     => __('inquiries::inquiries.inquiry_source'),
            'city'               => __('inquiries::inquiries.city'),
            'town'               => __('inquiries::inquiries.town'),
            'total_project_value'=> __('inquiries::inquiries.total_value'),
            'client_priority'    => __('inquiries::inquiries.client_priority'),
            'kon_priority'       => __('inquiries::inquiries.kon_priority'),
            'tender_number'      => __('inquiries::inquiries.tender_number'),
            'project_difficulty' => __('inquiries::inquiries.difficulty_level'),
            'assigned_engineer'  => __('inquiries::inquiries.assigned_engineer'),
        ];
    }

    // الأعمدة الافتراضية
    public static function getDefaultColumns()
    {
        return ['id', 'project', 'client', 'inquiry_date', 'status', 'quotation_state'];
    }
}
