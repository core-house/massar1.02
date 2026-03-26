# Recruitment Module - وحدة إدارة التوظيف

وحدة شاملة لإدارة دورة حياة التوظيف الكاملة من الإعلان الوظيفي حتى نهاية الخدمة.

## الوحدات المكونة

### 1. Job Postings - الإعلانات الوظيفية
- إدارة الإعلانات الوظيفية
- ربط الإعلانات بالوظائف (EmployeesJob)
- تتبع عدد المتقدمين
- حالة الإعلان (نشط/مغلق/منتهي)

### 2. CVs - السير الذاتية
- إدارة السير الذاتية للمرشحين
- رفع وتحميل ملفات CV
- البحث والفلترة
- ربط السير الذاتية بالإعلانات الوظيفية

### 3. Interviews - المقابلات
- جدولة المقابلات
- تتبع نتائج المقابلات
- تقويم المقابلات
- ربط المقابلات بالسير الذاتية والإعلانات

### 4. Contracts - العقود
- إدارة عقود التوظيف
- نقاط العقد ونقاط الراتب
- ربط العقود بالموظفين والمقابلات

### 5. Onboardings - إجراءات الانضمام والتوظيف
- إدارة إجراءات الانضمام والتوظيف
- قائمة مهام (Checklist) للإجراءات
- إدارة الملفات والمستندات (Spatie Media Library)
- ربطها بالموظفين والعقود والمقابلات والسير الذاتية
- تتبع حالة الإجراءات (pending, in_progress, completed, cancelled)

### 6. Terminations - إجراءات نهاية الخدمة
- إدارة حالات نهاية الخدمة (فصل/استقالة/وفاة/تقاعد)
- تتبع المستندات والإجراءات القانونية
- ربطها بالموظفين والعقود

## العلاقات

```
Job Posting → CVs → Interviews → Contract → Onboarding → Employee
                                                              ↓
                                                    Termination
```

## Routes

- `/recruitment/dashboard` - لوحة التحكم
- `/recruitment/job-postings` - الإعلانات الوظيفية
- `/recruitment/cvs` - السير الذاتية
- `/recruitment/interviews` - المقابلات
- `/recruitment/interviews/calendar` - تقويم المقابلات
- `/recruitment/contracts` - العقود
- `/recruitment/onboardings` - إجراءات الانضمام والتوظيف
- `/recruitment/terminations` - إجراءات نهاية الخدمة

## Models

- `Modules\Recruitment\Models\JobPosting`
- `Modules\Recruitment\Models\Cv`
- `Modules\Recruitment\Models\Interview`
- `Modules\Recruitment\Models\InterviewSchedule`
- `Modules\Recruitment\Models\Contract`
- `Modules\Recruitment\Models\ContractPoint`
- `Modules\Recruitment\Models\SalaryPoint`
- `Modules\Recruitment\Models\Onboarding`
- `Modules\Recruitment\Models\Termination`

## Permissions - الصلاحيات

الوحدة تستخدم نظام الصلاحيات المدمج مع Laravel Spatie Permission. يتم إنشاء الصلاحيات تلقائياً عبر `RecruitmentPermissionsSeeder`.

### الصلاحيات المتاحة:

#### Recruitment Dashboard
- `view Recruitment Dashboard`
- `create Recruitment Dashboard`
- `edit Recruitment Dashboard`
- `delete Recruitment Dashboard`
- `print Recruitment Dashboard`

#### Job Postings - الإعلانات الوظيفية
- `view Job Postings`
- `create Job Postings`
- `edit Job Postings`
- `delete Job Postings`
- `print Job Postings`

#### CVs - السير الذاتية
- `view CVs`
- `create CVs`
- `edit CVs`
- `delete CVs`
- `print CVs`

#### Interviews - المقابلات
- `view Interviews`
- `create Interviews`
- `edit Interviews`
- `delete Interviews`
- `print Interviews`

#### Interview Schedule - جدول المقابلات
- `view Interview Schedule`
- `create Interview Schedule`
- `edit Interview Schedule`
- `delete Interview Schedule`
- `print Interview Schedule`

#### Contracts - العقود
- `view Contracts`
- `create Contracts`
- `edit Contracts`
- `delete Contracts`
- `print Contracts`

#### Onboardings - إجراءات الانضمام والتوظيف
- `view Onboardings`
- `create Onboardings`
- `edit Onboardings`
- `delete Onboardings`
- `print Onboardings`

#### Terminations - إجراءات نهاية الخدمة
- `view Terminations`
- `create Terminations`
- `edit Terminations`
- `delete Terminations`
- `print Terminations`

#### Recruitment Statistics - إحصائيات التوظيف
- `view Recruitment Statistics`
- `create Recruitment Statistics`
- `edit Recruitment Statistics`
- `delete Recruitment Statistics`
- `print Recruitment Statistics`

### تشغيل Seeder للصلاحيات

```bash
php artisan db:seed --class="Modules\Recruitment\database\seeders\RecruitmentPermissionsSeeder"
```

أو عبر DatabaseSeeder الرئيسي:

```bash
php artisan db:seed
```

## Controllers

- `Modules\Recruitment\Http\Controllers\RecruitmentDashboardController`
- `Modules\Recruitment\Http\Controllers\JobPostingController`
- `Modules\Recruitment\Http\Controllers\CvController`
- `Modules\Recruitment\Http\Controllers\InterviewController`
- `Modules\Recruitment\Http\Controllers\ContractController`
- `Modules\Recruitment\Http\Controllers\OnboardingController`
- `Modules\Recruitment\Http\Controllers\TerminationController`

## Livewire Components

جميع مكونات Livewire Volt موجودة في `Modules/Recruitment/Resources/views/livewire/`:

- `recruitment.cvs.manage-cvs` - إدارة السير الذاتية (Volt Component)
- `recruitment.contracts.manage-contracts` - إدارة العقود (Volt Component)
- `recruitment.onboardings.manage-onboardings` - إدارة إجراءات الانضمام والتوظيف (Volt Component)

## Database Migrations

جميع migrations موجودة في `Modules/Recruitment/database/migrations/`:

- `create_job_postings_table`
- `create_interviews_table`
- `create_interview_schedules_table`
- `create_onboardings_table`
- `create_terminations_table`
- `add_interview_id_to_contracts_table`
- `add_cv_id_to_contracts_table`
- `add_job_posting_id_to_cvs_table`

## Installation

1. تأكد من تشغيل migrations:
```bash
php artisan migrate
```

2. قم بتشغيل seeder للصلاحيات:
```bash
php artisan db:seed --class="Modules\Recruitment\database\seeders\RecruitmentPermissionsSeeder"
```

3. قم بتعيين الصلاحيات للمستخدمين المناسبين عبر لوحة التحكم أو مباشرة في قاعدة البيانات.

## Notes

- جميع الصلاحيات يتم تعيينها مباشرة للمستخدمين عبر جدول `model_has_permissions`
- لا يتم استخدام الأدوار (Roles) لتعيين الصلاحيات في هذا النظام
- جميع النماذج تستخدم `BranchScope` للتصفية حسب الفرع

