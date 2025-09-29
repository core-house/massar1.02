<?php

namespace Modules\Inquiries\Livewire;

use Livewire\Component;
use App\Enums\ClientType;
use Livewire\WithFileUploads;
use App\Models\{City, Town, Client};
use Modules\Inquiries\Enums\{KonTitle, StatusForKon, InquiryStatus};
use Modules\Progress\Models\ProjectProgress;
use Modules\Inquiries\Enums\{KonPriorityEnum, ProjectSizeEnum, ClientPriorityEnum};
use Modules\Inquiries\Models\{WorkType, Inquiry, InquirySource, SubmittalChecklist, ProjectDocument, WorkCondition};

class CreateInquiry extends Component
{
    use WithFileUploads;

    public $workTypeSteps = [1 => null];
    public $inquirySourceSteps = [1 => null];
    public $selectedWorkPath = [];
    public $selectedInquiryPath = [];
    public $finalWorkType = '';
    public $finalInquirySource = '';
    public $projectId;
    public $inquiryDate;
    public $reqSubmittalDate;
    public $projectStartDate;
    public $cityId;
    public $townId;
    public $status;
    public $statusForKon;
    public $konTitle;
    public $clientId;
    public $assignedEngineer;
    public $mainContractorId;
    public $consultantId;
    public $ownerId;
    public $isPriority = false;
    public $projectSize;
    public $tenderNo;
    public $tenderId;
    public $estimationStartDate;
    public $estimationFinishedDate;
    public $submittingDate;
    public $totalProjectValue;
    public $quotationStateReason;
    public $quotationState;
    public $totalSubmittalScore = 0;
    public $totalConditionsScore = 0;
    public $projectDifficulty = 1;
    public $documentFile;

    public $workTypes = [];
    public $inquirySources = [];
    public $projects = [];
    public $cities = [];
    public $towns = [];
    public $clients = [];
    public $mainContractors = [];
    public $consultants = [];
    public $owners = [];
    public $statusOptions = [];
    public $statusForKonOptions = [];
    public $konTitleOptions = [];
    public $projectSizeOptions = [];
    public $inquiryName;

    public $clientPriority;
    public $konPriority;
    public $clientPriorityOptions = [];
    public $konPriorityOptions = [];

    public $engineers = [];
    public $quotationStateOptions = [];

    public $projectDocuments = [
        ['name' => 'Soil report', 'checked' => false],
        ['name' => 'Arch. Drawing', 'checked' => false],
        ['name' => 'Str. Drawing', 'checked' => false],
        ['name' => 'Spacification', 'checked' => false],
        ['name' => 'Pile design', 'checked' => false],
        ['name' => 'shoring design', 'checked' => false],
        ['name' => 'other', 'checked' => false, 'description' => '']
    ];

    public $submittalChecklist = [
        ['name' => 'Pre qualification', 'checked' => false, 'value' => 0],
        ['name' => 'Design', 'checked' => false, 'value' => 1],
        ['name' => 'MOS', 'checked' => false, 'value' => 0],
        ['name' => 'Material Submittal', 'checked' => false, 'value' => 1],
        ['name' => 'Methodology', 'checked' => false, 'value' => 1],
        ['name' => 'Time schedule', 'checked' => false, 'value' => 1],
        ['name' => 'Insurances', 'checked' => false, 'value' => 1],
        ['name' => 'Project team', 'checked' => false, 'value' => 1]
    ];

    public $workingConditions = [
        ['name' => 'Safety level', 'checked' => false, 'options' => ['Normal' => 1, 'Medium' => 2, 'High' => 3], 'selectedOption' => null, 'value' => 0],
        ['name' => 'Vendor list', 'checked' => false, 'value' => 1],
        ['name' => 'Consultant approval', 'checked' => false, 'value' => 1],
        ['name' => 'Machines approval', 'checked' => false, 'value' => 0],
        ['name' => 'Labours approval', 'checked' => false, 'value' => 0],
        ['name' => 'Security approvals', 'checked' => false, 'value' => 0],
        ['name' => 'Working Hours', 'checked' => false, 'options' => ['Normal(10hr/6 days)' => 1, 'Half week(8hr, 4day)' => 2, 'Half day(4hr/6days)' => 2, 'Half week-Half day(4hr/4day)' => 3], 'selectedOption' => null, 'value' => 0],
        ['name' => 'Night shift required', 'checked' => false, 'value' => 1],
        ['name' => 'Tight time schedule', 'checked' => false, 'value' => 1],
        ['name' => 'Remote Location', 'checked' => false, 'value' => 2],
        ['name' => 'Difficult Access Site', 'checked' => false, 'value' => 1],
        ['name' => 'Without advance payment', 'checked' => false, 'value' => 1],
        ['name' => 'Payment conditions', 'checked' => false, 'options' => ['CDC' => 0, 'PDC 30 days' => 1, 'PDC 90 days' => 2], 'selectedOption' => null, 'value' => 0]
    ];

    protected $listeners = [
        'getWorkTypeChildren' => 'emitWorkTypeChildren',
        'getInquirySourceChildren' => 'emitInquirySourceChildren',
        'itemSelected' => 'handleItemSelected', // جديد

    ];

    public function mount()
    {
        $this->engineers = Client::where('type', ClientType::ENGINEER->value)->get()->toArray();
        $this->quotationStateOptions = Inquiry::getQuotationStateOptions();
        $this->projectSizeOptions = ProjectSizeEnum::values();
        $this->inquiryDate = now()->format('Y-m-d');
        $this->workTypes = WorkType::where('is_active', true)->whereNull('parent_id')->get()->toArray();
        $this->inquirySources = InquirySource::where('is_active', true)->whereNull('parent_id')->get()->toArray();
        $this->projects = ProjectProgress::all()->toArray();
        $this->cities = City::all()->toArray();
        $this->towns = $this->cityId ? Town::where('city_id', $this->cityId)->get()->toArray() : [];
        $this->clients = Client::whereIn('type', [ClientType::Person->value, ClientType::Company->value])->get()->toArray();
        $this->mainContractors = Client::where('type', ClientType::MainContractor->value)->get()->toArray();
        $this->consultants = Client::where('type', ClientType::Consultant->value)->get()->toArray();
        $this->owners = Client::where('type', ClientType::Owner->value)->get()->toArray();
        $this->statusOptions = Inquiry::getStatusOptions();
        $this->statusForKonOptions = Inquiry::getStatusForKonOptions();
        $this->konTitleOptions = Inquiry::getKonTitleOptions();
        $this->clientPriorityOptions = ClientPriorityEnum::values();
        $this->konPriorityOptions = KonPriorityEnum::values();

        $this->status = InquiryStatus::JOB_IN_HAND->value;
        $this->statusForKon = StatusForKon::EXTENSION->value;
        $this->konTitle = KonTitle::MAIN_PILING_CONTRACTOR->value;

        $this->calculateScores();
    }

    public function handleItemSelected($data)
    {
        $wireModel = $data['wireModel'];
        $value = $data['value'];

        // تحديث القيمة المناسبة
        $this->{$wireModel} = $value;
    }

    public function updatedWorkTypeSteps($value, $key)
    {
        $stepNum = (int) str_replace('step_', '', $key);
        $this->workTypeSteps = array_slice($this->workTypeSteps, 0, $stepNum + 1, true);

        if ($value) {
            $selectedWorkType = WorkType::where('is_active', true)->find($value);
            if ($selectedWorkType) {
                $this->selectedWorkPath = array_slice($this->selectedWorkPath, 0, $stepNum, true);
                $this->selectedWorkPath[$stepNum] = $selectedWorkType->name;
            }
        } else {
            $this->selectedWorkPath = array_slice($this->selectedWorkPath, 0, $stepNum, true);
        }
    }

    public function updatedInquirySourceSteps($value, $key)
    {
        $stepNum = (int) str_replace('inquiry_source_step_', '', $key);
        $this->inquirySourceSteps = array_slice($this->inquirySourceSteps, 0, $stepNum + 1, true);

        if ($value) {
            $selectedInquirySource = InquirySource::where('is_active', true)->find($value);
            if ($selectedInquirySource) {
                $this->selectedInquiryPath = array_slice($this->selectedInquiryPath, 0, $stepNum, true);
                $this->selectedInquiryPath[$stepNum] = $selectedInquirySource->name;
            }
        } else {
            $this->selectedInquiryPath = array_slice($this->selectedInquiryPath, 0, $stepNum, true);
        }
    }

    public function updatedCityId($value)
    {
        $this->townId = null;
        $this->towns = $value ? Town::where('city_id', $value)->get()->toArray() : [];
    }

    public function updatedProjectDocuments($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $index = $parts[0];
            $property = $parts[1];
            if (isset($this->projectDocuments[$index])) {
                $this->projectDocuments[$index][$property] = $value;
                if ($this->projectDocuments[$index]['name'] === 'other' && $property === 'checked' && !$value) {
                    $this->projectDocuments[$index]['description'] = '';
                }
            }
        }
    }

    // تحديث method للتعامل مع working conditions
    public function updatedWorkingConditions($value, $key)
    {
        // التحقق من نوع التحديث
        $parts = explode('.', $key);
        $index = (int) $parts[0];
        $property = $parts[1] ?? 'checked';

        if ($property === 'checked') {
            // إذا تم إلغاء التحديد، إعادة تعيين القيمة المختارة
            if (!$this->workingConditions[$index]['checked']) {
                $this->workingConditions[$index]['selectedOption'] = null;
                $this->workingConditions[$index]['value'] = 0;
            } else {
                // إذا تم التحديد وله خيارات، استخدم القيمة الافتراضية
                if (isset($this->workingConditions[$index]['options'])) {
                    if (!$this->workingConditions[$index]['selectedOption']) {
                        $firstOption = array_values($this->workingConditions[$index]['options'])[0];
                        $this->workingConditions[$index]['selectedOption'] = $firstOption;
                        $this->workingConditions[$index]['value'] = $firstOption;
                    }
                } else {
                    // إذا لم تكن له خيارات، استخدم القيمة الافتراضية
                    $this->workingConditions[$index]['value'] = $this->workingConditions[$index]['value'] ?? 0;
                }
            }
        } elseif ($property === 'selectedOption') {
            // تحديث القيمة عند تغيير الخيار المحدد
            $this->workingConditions[$index]['value'] = $value;
        }

        // إعادة حساب النتائج
        $this->calculateScores();
    }

    // method جديد لحساب النتائج
    public function calculateScores()
    {
        // حساب اسكور التقديمات
        $this->totalSubmittalScore = 0;
        foreach ($this->submittalChecklist as $item) {
            if ($item['checked']) {
                $this->totalSubmittalScore += (int) $item['value'];
            }
        }

        // حساب اسكور الشروط
        $this->totalConditionsScore = 0;
        foreach ($this->workingConditions as $condition) {
            if ($condition['checked']) {
                $this->totalConditionsScore += (int) ($condition['value'] ?? 0);
            }
        }

        // حساب صعوبة المشروع
        $score = $this->totalConditionsScore;
        if ($score < 6) {
            $this->projectDifficulty = 1;
        } elseif ($score <= 10) {
            $this->projectDifficulty = 2;
        } elseif ($score <= 15) {
            $this->projectDifficulty = 3;
        } else {
            $this->projectDifficulty = 4;
        }
    }

    // تحديث method للتعامل مع submittal checklist
    public function updatedSubmittalChecklist($value, $key)
    {
        $this->calculateScores();
    }

    // إزالة الـ methods القديمة واستبدالها بـ methods جديدة
    public function updated($propertyName)
    {
        // إعادة حساب النتائج عند أي تحديث في الـ checklists
        if (
            strpos($propertyName, 'submittalChecklist') !== false ||
            strpos($propertyName, 'workingConditions') !== false
        ) {
            $this->calculateScores();
        }
    }

    public function emitWorkTypeChildren($stepNum, $parentId)
    {
        $children = $parentId ? WorkType::where('parent_id', $parentId)->where('is_active', true)->get()->toArray() : [];
        $this->dispatch('workTypeChildrenLoaded', stepNum: $stepNum, children: $children);
    }

    public function emitInquirySourceChildren($stepNum, $parentId)
    {
        $children = $parentId ? InquirySource::where('parent_id', $parentId)->where('is_active', true)->get()->toArray() : [];
        $this->dispatch('inquirySourceChildrenLoaded', stepNum: $stepNum, children: $children);
    }

    public function save()
    {
        // $this->validate([
        //     'projectSize' => 'required|in:' . implode(',', ProjectSizeEnum::values()),
        //     'projectId' => 'required|exists:project_progress,id',
        //     'inquiryDate' => 'required|date',
        //     'reqSubmittalDate' => 'nullable|date',
        //     'projectStartDate' => 'nullable|date',
        //     'cityId' => 'required|exists:cities,id',
        //     'townId' => 'nullable|exists:towns,id',
        //     'status' => 'required|in:' . implode(',', array_column(InquiryData::getStatusOptions(), 'value')),
        //     'statusForKon' => 'nullable|in:' . implode(',', array_column(InquiryData::getStatusForKonOptions(), 'value')),
        //     'konTitle' => 'required|in:' . implode(',', array_column(InquiryData::getKonTitleOptions(), 'value')),
        //     'clientId' => 'nullable|exists:clients,id',
        //     'mainContractorId' => 'nullable|exists:clients,id',
        //     'consultantId' => 'nullable|exists:clients,id',
        //     'ownerId' => 'nullable|exists:clients,id',
        //     'assignedEngineer' => 'nullable|exists:clients,id',
        //     'finalWorkType' => 'required|string',
        //     'finalInquirySource' => 'required|string',
        //     'documentFile' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:10240',
        //     'quotationState' => 'required|in:' . implode(',', array_column(InquiryData::getQuotationStateOptions(), 'value')),
        //     'clientPriority' => 'nullable|in:' . implode(',', ClientPriorityEnum::values()),
        //     'konPriority' => 'nullable|in:' . implode(',', KonPriorityEnum::values()),
        // ]);
        $inquiry = Inquiry::create([
            'inquiry_name' => 'Inquiry-' . now()->format('YmdHis'),
            'project_id' => $this->projectId,
            'work_type_id' => $this->workTypeSteps[array_key_last($this->workTypeSteps)] ?? null,
            'final_work_type' => $this->finalWorkType,
            'inquiry_source_id' => $this->inquirySourceSteps[array_key_last($this->inquirySourceSteps)] ?? null,
            'final_inquiry_source' => $this->finalInquirySource,
            'client_id' => $this->clientId,
            'main_contractor_id' => $this->mainContractorId,
            'consultant_id' => $this->consultantId,
            'owner_id' => $this->ownerId,
            'assigned_engineer_id' => $this->assignedEngineer,
            'city_id' => $this->cityId,
            'town_id' => $this->townId,
            'inquiry_date' => $this->inquiryDate,
            'req_submittal_date' => $this->reqSubmittalDate,
            'project_start_date' => $this->projectStartDate,
            'status' => $this->status,
            'status_for_kon' => $this->statusForKon,
            'kon_title' => $this->konTitle,
            'client_priority' => $this->clientPriority,
            'kon_priority' => $this->konPriority,
            'project_size' => $this->projectSize,
            'quotation_state' => $this->quotationState,
            'total_submittal_check_list_score' => $this->totalSubmittalScore,
            'total_work_conditions_score' => $this->totalConditionsScore,
            'project_difficulty' => $this->projectDifficulty,
            'tender_number' => $this->tenderNo,
            'tender_id' => $this->tenderId,
            'estimation_start_date' => $this->estimationStartDate,
            'estimation_finished_date' => $this->estimationFinishedDate,
            'submitting_date' => $this->submittingDate,
            'total_project_value' => $this->totalProjectValue,
            'rejection_reason' => $this->quotationStateReason
        ]);

        if ($this->documentFile) {
            $inquiry
                ->addMedia($this->documentFile->getRealPath())
                ->usingFileName($this->documentFile->getClientOriginalName())
                ->toMediaCollection('inquiry-documents');
        }
        // حفظ التقديمات في جدول submittal_checklists وجدول inquiry_submittal_checklist
        foreach ($this->submittalChecklist as $item) {
            if ($item['checked']) {
                $submittal = SubmittalChecklist::firstOrCreate(
                    ['name' => $item['name']],
                    ['score' => $item['value']]
                );
                $inquiry->submittalChecklists()->attach($submittal->id);
            }
        }

        // حفظ شروط العمل في جدول work_conditions وجدول inquiry_work_condition
        foreach ($this->workingConditions as $condition) {
            if ($condition['checked']) {
                $workCondition = WorkCondition::firstOrCreate(
                    ['name' => $condition['name']],
                    ['score' => $condition['value'] ?? 0]
                );
                $inquiry->workConditions()->attach($workCondition->id);
            }
        }

        // حفظ الوثائق في جدول project_documents وجدول inquiry_project_document
        foreach ($this->projectDocuments as $document) {
            if ($document['checked']) {
                $projectDocument = ProjectDocument::firstOrCreate(
                    ['name' => $document['name']],
                    ['description' => $document['description'] ?? null]
                );
                $inquiry->projectDocuments()->attach($projectDocument->id, [
                    'description' => $document['description'] ?? null
                ]);
            }
        }

        session()->flash('message', 'تم حفظ الاستفسار بنجاح!');
        return redirect()->route('inquiries.index');
    }

    public function render()
    {
        return view('inquiries::livewire.create-inquiry');
    }
}
