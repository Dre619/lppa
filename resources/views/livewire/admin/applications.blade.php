<?php

use Livewire\Volt\Component;
use App\Models\Application;
use App\Models\DevelopmentArea;
use App\Models\ChangeUseStage;
use App\Models\RegistrationOrganization;
use App\Models\District;
use App\Models\RegistrationArea;
use App\Models\SubArea;
use App\Models\LandUs;
use App\Models\ConstructionStage;
use App\Models\ApplicantType;
use App\Models\RegistrationType;
use App\Models\ApplicationApplicant;
use App\Models\ApplicationClassification;
use App\Models\ApplicationNote;
use App\Models\ApplicationResolution;
use App\Models\ApplicationSubmission;
use App\Models\Resolution;
use App\Models\ApplicantTitle;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\WithFileUploads;
use App\Imports\Development;
use App\Imports\ChangeOfLandUseImport;
use App\Imports\SubmissionImport;
use App\Models\ImportLog;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

new class extends Component {
    use WithFileUploads;
    use WithPagination;
    public $applications = null;
    public $developmentAreas = null;
    public $changeUseStages = null;
    public $registrationOrganizations = null;
    public $districts = null;
    public $registrationAreas = null;
    public $subAreas = null;
    public $landUses = null;
    public $constructionStages = null;
    public $applicantTypes = null;
    public $registrationTypes = null;
    public $applicationClassifications = null;
    public $applicationApplicants = null;
    public $applicationNotes = null;
    public $applicationResolutions = null;
    public $applicationSubmissions = null;
    public $applicantTitles = null;
    public $resolutions = null;
    public $importSheet;

    public $showModal = false;
    public $currentApplication = null;
    public $showDeleteConfirmation = false;
    public $applicationId = null;
    public bool $isEditing = false;
    public string $search = '';
    public $application_date = null;

    // Form fields
    public string $applicationNumber = '';
    public $applicationDate = null;
    public string $applicationStatus = 'pending';
    public string $applicationType = '';
    public string $applicationDescription = '';

    public ?int $applicationClassificationId = null;
    public ?int $developmentAreaId = null;
    public ?int $changeUseStageId = null;
    public ?int $registrationOrganizationId = null;
    public ?int $districtId = null;
    public ?int $registrationAreaId = null;
    public ?int $subAreaId = null;
    public ?int $landUseId = null;
    public ?int $landUseFrom = null;
    public ?int $landUseTo = null;
    public ?int $constructionStageId = null;
    public ?int $registrationStageId = null;
    public ?int $applicantTypeId = null;
    public ?int $registrationTypeId = null;
    public ?int $applicationApplicantId = null;
    public ?int $applicationNoteId = null;
    public ?int $applicationResolutionId = null;
    public ?int $applicationSubmissionId = null;

    public string $AreaKey = '';
    public bool $is_institution = false;
    public string $parcel_id = '';
    public string $clean_parcel_id = '';
    public string $sub_plot_number = '';
    public string $institution_name = '';
    public string $affected_area = '';
    public string $development_sub_area = '';
    public string $local_area = '';
    public ?int $print_order = 0;
    public ?int $lastRegistrationNumber = 0;

    //applicant properties
    public $applicantId = null;
    public $showApplicantModal = false;
    public $applicant_title_id = false;
    public $first_name = null;
    public $last_name = null;
    public $middle_name = null;
    public $applicant_type_id = null;
    public $phone = null;
    public $email = null;
    public $address = null;
    public $nrc_number = null;
    public $showDeleteConfirmationApplicant = false;


    //resolution properties
    public $resolutionId = null;
    public $showResolutionModal = false;
    public $resolution_details = null;
    public $resolution_date = null;
    public $resolutionEditingId = null;
    public $showDeleteConfirmationResolution = false;



    //view application details properties
    public $viewApplication = null;
    public $showViewModal = false;

    public int $totalRecords = 0;

    public $pageLimit = 10;
    public array $pageLimitOptions =[];



    public function mount($registrationTypeId)
    {
        $this->totalRecords = Application::where('application_classification_id',$registrationTypeId)->count();
        $this->registrationTypeId = $registrationTypeId;
        $this->applicationClassificationId = $registrationTypeId;
        $this->pageLimitOptions =  [
        ['label'=>10,'value'=>10],
        ['label'=>25,'value'=>25],
        ['label'=>50,'value'=>50],
        ['label'=>100,'value'=>100],
        ['label'=>250,'value'=>250],
        ['label'=>500,'value'=>500],
        ['label'=>number_format(1000),'value'=>1000],
        ['label'=>'View All ('.number_format($this->totalRecords).')','value'=>$this->totalRecords],
    ];

        $this->loadApplications();

        $this->developmentAreas = DevelopmentArea::all();
        $this->changeUseStages = ChangeUseStage::all();
        $this->registrationOrganizations = RegistrationOrganization::first();
        $this->districts = District::all();
        $this->registrationAreas = RegistrationArea::all();
        $this->applicantTitles = ApplicantTitle::all();
        $this->subAreas = collect();
        $this->landUses = LandUs::all();
        $this->resolutions = Resolution::all();
        $this->constructionStages = ConstructionStage::all();
        $this->applicantTypes = ApplicantType::all();
        $this->registrationTypes = RegistrationType::find($registrationTypeId);
        $this->applicationClassifications = ApplicationClassification::all();
    }

    public function viewApplicationv($applicationId)
    {
        $this->showViewModal = true;
        $this->viewApplication = Application::with([
                'development_area',
                'change_of_land_use',
                'registration_organization',
                'district',
                'registration_area',
                'subArea',
                'landUse',
                'applicationClassification',
                'applicationApplicants.applicant_title',
                'applicationApplicants.applicant_type',
                'applicationResolutions.resolution',
                'applicationSubmissions.application_classification',
])->find($applicationId);
        Log::info(json_encode($this->viewApplication));
    }

    public function closeViewApplication()
    {
        $this->showViewModal = false;
        $this->viewApplication = null;
    }

    public function editApplication($applicationId)
    {
        $this->showModal = true;
        $this->isEditing = true;
        $this->applicationId = $applicationId;
        $this->currentApplication = Application::find($applicationId);
        $this->applicationNumber = $this->currentApplication->application_id;
        $this->applicationDate = $this->currentApplication->application_date;
        $this->applicationStatus = $this->currentApplication->application_status;
        $this->developmentAreaId = $this->currentApplication->development_area_id;
        $this->changeUseStageId = $this->currentApplication->change_of_use_stage_id;
        $this->registrationOrganizationId = $this->currentApplication->registration_organization_id;
        $this->districtId = $this->currentApplication->district_id;
        $this->registrationAreaId = $this->currentApplication->registration_area_id;
        $this->subAreaId = $this->currentApplication->sub_area_id;
        $this->landUseId = $this->currentApplication->current_use_of_land_id;
        $this->landUseFrom = $this->currentApplication->change_land_use_from;
        $this->landUseTo = $this->currentApplication->change_land_use_to;
        $this->constructionStageId = $this->currentApplication->construction_stage_id;
        $this->is_institution = $this->currentApplication->is_institution;
        $this->institution_name = $this->currentApplication->institution_name??'';
        $this->parcel_id = $this->currentApplication->parcel_id;
        $this->clean_parcel_id = $this->currentApplication->clean_parcel_id;
        $this->sub_plot_number = $this->currentApplication->sub_plot_number ?? '';
        //application submission
        $applicationSubmission = ApplicationSubmission::where('application_id', $applicationId)->first();
        $this->registrationTypeId = $applicationSubmission->application_classification_id;
        $this->applicationDescription = $applicationSubmission?->application_text;
        ///$this->applicationDate = $applicationSubmission->id;
    }



public function import()
{
    $this->validate([
        'importSheet' => 'required|file|mimes:xlsx,xls'
    ]);
    try {
        // Store the file
        $filePath = $this->importSheet->store('imports');

        // Create import log
        $importLog = ImportLog::create([
            'user_id' => auth()->id(),
            'type' => $this->registrationTypes->reg_key,
            'file_path' => $filePath,
            'status' => 'pending',
            'total_rows' => $this->countRowsInExcel($this->importSheet),
        ]);

        // Dispatch the appropriate importer
        $importer = match($this->registrationTypes->reg_key) {
            'D' => new Development($importLog->id),
            'CU' => new ChangeOfLandUseImport($importLog->id),
            'S' => new SubmissionImport($importLog->id),
        };

        Excel::queueImport($importer, $filePath);

        $this->dispatch('toastMagic',
            status: 'success',
            title: 'Import Started',
            message: 'Your import has been queued for processing.'
        );

        $this->reset('importSheet');

    } catch (\Exception $e) {
        \Log::error("Import failed: " . $e->getMessage());
        $this->dispatch('toastMagic',
            status: 'error',
            title: 'Import Failed',
            message: 'Error starting import: ' . $e->getMessage()
        );
    }
}

protected function countRowsInExcel($file): int
{
    try {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getRealPath());
        $spreadsheet = $reader->load($file->getRealPath());
        return $spreadsheet->getActiveSheet()->getHighestDataRow() - 1; // Subtract header row
    } catch (\Exception $e) {
        return 0;
    }
}

    public function loadApplications()
{
    return Application::query()
        ->where('application_classification_id', $this->registrationTypeId)
        ->with([
            'development_area',
            'change_of_land_use',
            'registration_organization',
            'district',
            'registration_area',
            'subArea',
            'landUse',
            'applicationClassification',
            'applicationApplicants',
            'applicationResolutions',
            'applicationSubmissions',
        ])
        ->where(function($query) {
            $query->where('application_id', 'like', '%' . $this->search . '%')
                ->orWhere('application_date', 'like', '%' . $this->search . '%')
                ->orWhere('application_status', 'like', '%' . $this->search . '%')
                ->orWhereHas('development_area', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('applicationApplicants',function($q){
                    $q->where('first_name',$this->search)
                    ->orWhere('last_name',$this->search)
                    ->orWhere('middle_name',$this->search)
                    ->orWhere('phone',$this->search)
                    ->orWhere('email',$this->search)
                    ->orWhere('address',$this->search)
                    ->orWhere('nrc_number',$this->search);
                })
                ->orWhereHas('district',function($q){
                    $q->where('name',$this->search);
                })
                ->orWhereHas('change_of_land_use', function ($q) {
                    $q->where('stage_name', 'like', '%' . $this->search . '%');
                });
        })
        ->orderBy('registration_number', 'desc')
        ->paginate($this->pageLimit);
}

    public function updatedSearch()
    {
        $this->loadApplications();
    }

    public function updatedRegistrationAreaId()
    {
        $area = RegistrationArea::find($this->registrationAreaId);
        $this->subAreas = SubArea::where('registration_area_id', $this->registrationAreaId)->get();

        $count = Application::where('registration_area_id', $this->registrationAreaId)
        ->where('application_classification_id', $this->registrationTypeId)
        ->orderBy('registration_number', 'desc')
        ->first();

        if ($area) {
            Log::info($count->registration_number);
            $this->AreaKey = $area->area_key;
            $this->lastRegistrationNumber = $count ? $count->registration_number + 1 : 1;
            $this->applicationNumber = $this->registrationTypes->reg_key.'/'.
                                     $this->registrationOrganizations->name.'/'.
                                     $this->AreaKey.'/'.
                                     $this->lastRegistrationNumber;
            Log::info($this->lastRegistrationNumber);
            $this->registrationOrganizationId = $this->registrationOrganizations->id;
        } else {
            $this->AreaKey = null;
            $this->subAreas = collect();
            $this->applicationNumber = null;
            $this->registrationOrganizationId = null;
        }
    }

    public function saveApplication()
    {
        try{

        $this->validate([
            'changeUseStageId' => 'nullable|exists:change_of_use,id',
            'registrationOrganizationId' => 'required|exists:registration_organizations,id',
            'applicationClassificationId' => 'required|exists:application_classifications,id',
            'registrationAreaId' => 'required|exists:registration_areas,id',
            'applicationNumber' => 'required|string',
            'sub_plot_number' => 'nullable|string',
            'is_institution' => 'nullable|boolean',
            'institution_name' => 'nullable|string|required_if:is_institution,true',
            'parcel_id' => 'required|string',
            'clean_parcel_id' => 'nullable|string',
            'affected_area' => 'nullable|string',
            'districtId' => 'required|exists:districts,id',
            'developmentAreaId' => 'required|exists:development_areas,id',
            'development_sub_area' => 'required|string',
            'local_area' => 'nullable|string',
            'print_order' => 'nullable|integer',
            'landUseId' => 'required|exists:land_uses,id',
            'landUseFrom' => 'nullable|exists:land_uses,id',
            'landUseTo' => 'nullable|exists:land_uses,id',
            'applicationStatus' => 'nullable|string',
            'applicationDate' => 'required|date',
            'applicationDescription' => 'required|string',
            'subAreaId' => 'required|exists:sub_areas,id'
        ]);
        } catch(ValidationException $e)
        {
            Log::info(json_encode($e));
        }

        $applicationData = [
            'change_of_use_stage_id' => $this->changeUseStageId,
            'registration_organization_id' => $this->registrationOrganizationId,
            'application_classification_id' => $this->applicationClassificationId,
            'registration_area_id' => $this->registrationAreaId,
            'registration_number' => $this->lastRegistrationNumber,
            'registration_sub_number' => null,
            'application_id' => $this->applicationNumber,
            'is_institution' => $this->is_institution,
            'institution_name' => $this->is_institution ? $this->institution_name : null,
            'parcel_id' => $this->parcel_id,
            'clean_parcel_id' => $this->clean_parcel_id,
            'affected_area' => $this->affected_area,
            'sub_plot_number' => $this->sub_plot_number,
            'district_id' => $this->districtId,
            'development_area_id' => $this->developmentAreaId,
            'development_sub_area' => $this->development_sub_area,
            'local_area' => $this->local_area,
            'print_order' => $this->print_order,
            'current_use_of_land_id' => $this->landUseId,
            'change_land_use_from' => $this->landUseFrom,
            'change_land_use_to' => $this->landUseTo,
            'application_status' => $this->applicationStatus,
            'sub_area_id' => $this->subAreaId,
            'application_date' => $this->applicationDate
        ];

        if($this->isEditing)
        {
            Application::find($this->applicationId)->update($applicationData);
            $applicationSubmission = ApplicationSubmission::where('application_id', $this->applicationId)->first();
            $applicationSubmission->update([
                'application_classification_id' => $this->registrationTypeId,
                'application_text' => $this->applicationDescription,
                'submission_date' => $this->applicationDate,
            ]);
        } else {
             $application = Application::create($applicationData);

            ApplicationSubmission::create([
                'application_id' => $application->id,
                'application_classification_id' => $this->registrationTypeId,
                'application_text' => $this->applicationDescription,
                'submission_date' => $this->applicationDate,
            ]);
        }

        $this->resetForm();
        $this->showModal = false;
        $this->isEditing = false;
        $this->applicationId = null;
        $this->loadApplications();
        $this->lastRegistrationNumber = Application::max('registration_number') ?? 0;
    }

    //delete application
    public function confirmDelete($applicationId)
    {
        $this->applicationId = $applicationId;
        $this->showDeleteConfirmation = true;
    }

    public function deleteApplication()
    {
        Application::find($this->applicationId)->delete();
        $this->applicationId = null;
        $this->showDeleteConfirmation = false;
        $this->loadApplications();
    }

    public function cancelDeleteApplication()
    {
         $this->applicationId = false;
        $this->showDeleteConfirmation = false;
    }

    public function addResolution($applicationId)
    {
        $this->showResolutionModal = true;
        $this->applicationId = $applicationId;

    }

    public function closeResolutionModal()
    {
        $this->showResolutionModal = false;
        $this->applicationId = null;
        $this->resolutionId = null;
        $this->resolutionEditingId = null;
        $this->applicationId = null;
        $this->resolution_details = null;
        $this->resolution_date = null;
    }

    public function addApplicant($applicationId)
    {
        $this->showApplicantModal = true;
        $this->applicationId = $applicationId;
    }

    //applicant deletion logic

    public function confirmDeleteApplicant($applicantId)
    {
        $this->showDeleteConfirmationApplicant = true;
        $this->applicantId = $applicantId;
    }

    public function cancelDeleteApplicant()
    {
        $this->showDeleteConfirmationApplicant = false;
        $this->applicantId = null;
    }

    public function deleteApplicant()
    {
        ApplicationApplicant::find($this->applicantId)->delete();
        $this->showDeleteConfirmationApplicant = false;
        $this->cancelDeleteApplicant();
        $this->dispatch('toastMagic',status:'success',title:"Success",message:"Applicant deleted successfully");
    }

    //resolution deletion logic
    public function confirmDeleteResolution($resolutionId)
    {
        $this->showDeleteConfirmationResolution = true;
        $this->resolutionId = $resolutionId;
    }

    public function deleteResolution()
    {
        ApplicationResolution::find($this->resolutionId)->delete();
        $this->showDeleteConfirmationResolution = false;
        $this->cancelDeleteResolution();
        $this->dispatch('toastMagic',status:'success',title:"Success",message:"Resolution deleted successfully");
    }

    public function cancelDeleteResolution()
    {
        $this->showDeleteConfirmationResolution = false;
        $this->resolutionId = null;
    }

    public function saveResolution()
    {
        try{
            $this->validate([
                'resolution_details' => 'nullable|string',
                'resolution_date' => 'required|date',
            ]);
        }catch(ValidationException $e)
        {
            Log::info(json_encode($e));
        }
        $resolutionData = [
            'application_id' => $this->applicationId,
            'resolution_id' => $this->resolutionId,
            'resolution_details' => $this->resolution_details,
            'resolution_date' => $this->resolution_date,
        ];
        if($this->resolutionEditingId)
        {
            ApplicationResolution::find($this->resolutionEditingId)->update($resolutionData);
        } else {
            ApplicationResolution::create($resolutionData);
        }

        $this->closeResolutionModal();
        $this->dispatch('toastMagic',status:'success',title:"Success",message:"Resolution saved successfully");
    }

    public function editResolution($resolutionId)
    {
        $resolutionDetails = ApplicationResolution::find($resolutionId);
        $this->resolutionId = $resolutionDetails->resolution_id;
        $this->resolutionEditingId = $resolutionId;
        $this->showResolutionModal = true;
        $this->applicationId = $resolutionDetails->application_id;
        $this->resolution_details = $resolutionDetails->resolution_details;
        $this->resolution_date = $resolutionDetails->resolution_date;
    }

    public function editApplicant($applicantId)
    {
        $applicantDetails = ApplicationApplicant::find($applicantId);
        $this->applicantId = $applicantId;
        $this->showApplicantModal = true;
        $this->applicationId = $applicantDetails->application_id;
        $this->applicant_title_id = $applicantDetails->applicant_title_id;
        $this->first_name = $applicantDetails->first_name;
        $this->last_name = $applicantDetails->last_name;
        $this->middle_name = $applicantDetails->middle_name;
        $this->applicant_type_id = $applicantDetails->applicant_type_id;
        $this->phone = $applicantDetails->phone;
        $this->email = $applicantDetails->email;
        $this->address = $applicantDetails->address;
        $this->nrc_number = $applicantDetails->nrc_number;
    }

    public function closeApplicantModal()
    {
        $this->showApplicantModal = false;
        $this->applicationId = null;
        $this->resetForm();
    }

    public function saveApplicant()
    {
        try{
            $this->validate([
                'applicant_title_id' => 'required|exists:applicant_titles,id',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'middle_name' => 'nullable|string',
                'applicant_type_id' => 'required|exists:applicant_types,id',
                'phone' => 'required|string',
                'email' => 'required|email',
                'address' => 'required|string',
                'nrc_number' => 'required|string',
            ]);
        }catch(ValidationException $e)
        {
            Log::info(json_encode($e));
        }
        $applicantData = [
            'application_id' => $this->applicationId,
            'applicant_title_id' => $this->applicant_title_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'applicant_type_id' => $this->applicant_type_id,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'nrc_number' => $this->nrc_number,
        ];
        if($this->applicantId)
        {
            ApplicationApplicant::find($this->applicantId)->update($applicantData);
        } else {
            ApplicationApplicant::create($applicantData);
        }

        $this->closeApplicantModal();
        $this->dispatch('toastMagic',status:'success',title:"Success",message:"Applicant added successfully");
    }

    public function resetForm()
    {
        $this->reset([
            'applicationNumber', 'applicationDate', 'applicationStatus', 'applicationDescription',
            'developmentAreaId', 'changeUseStageId', 'districtId', 'registrationAreaId',
            'subAreaId', 'landUseId','landUseFrom','landUseTo', 'constructionStageId', 'is_institution', 'institution_name',
            'parcel_id', 'clean_parcel_id', 'sub_plot_number', 'affected_area', 'development_sub_area',
            'local_area', 'print_order'
        ]);
    }

    public function updatedShowModal($value)
    {
        if (!$value) {
            $this->resetForm();
        }
    }
}; ?>

<div>

    <x-card title="{{ $this->registrationTypes->name }}" class="mb-4">
    <form class="mb-4" wire:submit.prevent="import" enctype="multipart/form-data">
        {{-- Loading indicator --}}
                <span wire:loading wire:target="importSheet" class="text-blue-500 text-sm mt-1">
                    Uploading file...
                </span>

                {{-- File ready confirmation --}}
                @if ($importSheet)
                    <i class="text-green-600 text-sm mt-1">
                        File "{{ $importSheet->getClientOriginalName() }}" is ready to import.
                    </i>
                @endif
    <div class="flex justify-start items-end gap-4">
        <div class="">
            <x-input.file
                type="file"
                wire:model="importSheet"
                label="Import File"
                accept=".xlsx,.xls"
                required
            />
        </div>
        <div>
            <x-button
            :disabled="!$importSheet?true:false"
            :loading="$importSheet"
                type="submit"
                label="{{ __('Submit') }}"
            />
        </div>
    </div>
</form>

        <div class="flex justify-between items-center mb-4">
            <div>
                <x-input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search applications...') }}" />
            </div>
            <div>
                <x-button wire:click="$toggle('showModal')" icon="plus" label="{{ __('Add Application') }}" />
            </div>
        </div>

        @if($this->loadApplications()->isEmpty())
            <p class="text-gray-500">{{ __('No applications found.') }}</p>
        @else
            <div class="overflow-x-auto">
                 <div class="mb-3 flex justify-start items-center gap-2">
             <div class="w-50">
                <x-select
                    wire:model.live='pageLimit'
                    :options='$pageLimitOptions'
                    option-label='label'
                    option-value='value'
                    label="Page Limit"
                    :clearable="false"
                />
             </div>
           </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Application Number') }}</th>
                            @if($this->registrationTypeId ==1)
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Application Stage') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Current Land use') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Land use from') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Land Use to') }}</th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Application Date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($this->loadApplications() as $application)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $application->application_id }}</td>
                                @if($this->registrationTypeId == 1)
                                    <td class="px-6 py-4 whitespace-nowrap">{{ change_of_land_use_stage($application?->change_of_use_stage_id) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $application->landUse?->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $application->formLandUse?->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $application->toLandUse?->name }}</td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">{{ $application->application_date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $application->application_status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($application->application_status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                    <x-button wire:click="viewApplicationv({{ $application->id }})" icon="eye" class="bg-blue-500 hover:bg-blue-600" title="View Application Details"/>
                                        @if($application->application_classification_id == 1)
                                            <x-button wire:click='addApplicant({{ $application->id }})' icon="document-text" class="bg-gray-500 hover:bg-gray-600" title="Generate Advert"/>
                                            <x-button wire:click='addApplicant({{ $application->id }})' icon="document" class="bg-gray-500 hover:bg-gray-600" title="Letter to Minister"/>
                                        @endif
                                    <x-button wire:click='addApplicant({{ $application->id }})' icon="user-plus" class="bg-gray-500 hover:bg-gray-600" title="add applicants"/>
                                    <x-button wire:click="addResolution({{ $application->id }})" icon="document-text" class="bg-blue-500 hover:bg-blue-600" title="add resolution"/>
                                    <x-button wire:click="editApplication({{ $application->id }})" icon="pencil" class="bg-blue-500 hover:bg-blue-600" />
                                    <x-button wire:click="confirmDelete({{ $application->id }})" icon="trash" class="bg-red-500 hover:bg-red-600" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $this->loadApplications()->links() }}
            </div>
        @endif


        <x-modal wire:model="showViewModal" class="w-full max-w-7xl" blur="lg">
    <x-card title="{{ __('Application Details') }}" class="border-0 shadow-xl">
        <!-- Card Header with Status Indicator -->
        <div class="px-6 pt-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <div class="h-3 w-3 rounded-full bg-indigo-500 animate-pulse"></div>
                <span class="text-sm font-medium text-gray-500">View Mode</span>
            </div>
            <x-button wire:click="$set('showViewModal', false)" icon="eye-slash" flat squared />
        </div>

        @if($this->viewApplication)
        <div class="space-y-8 px-6 pb-6">
            <!-- Application Information Card -->
                <div class="bg-gradient-to-br from-gray-50 to-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ __('Application Information') }}
                    </h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('App ID') }}</p>
                                <p class="text-gray-900 font-mono">{{ $this->viewApplication->application_id }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Created') }}</p>
                                <p class="text-gray-900">{{ $this->viewApplication->created_at?->format('M d, Y') ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Updated') }}</p>
                                <p class="text-gray-900">{{ $this->viewApplication->updated_at?->format('M d, Y') ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Registration Area') }}</p>
                                <p class="text-gray-900 font-mono">{{ $this->viewApplication->registration_area->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Registration Organzation') }}</p>
                                <p class="text-gray-900">{{ $this->viewApplication->registration_organization->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Registration Classification') }}</p>
                                <p class="text-gray-900">{{ $this->viewApplication->applicationClassification->reg_key ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Registration Number') }}</p>
                                <p class="text-gray-900 font-mono">{{ $this->viewApplication->registration_number }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Registration Sub Number') }}</p>
                                <p class="text-gray-900">{{ $this->viewApplication->registration_sub_number ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Institution') }}</p>
                                <p class="text-gray-900">{{ $this->viewApplication->institution_name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Parcel Number') }}</p>
                                <p class="text-gray-900 font-mono">{{ $this->viewApplication->parcel_id }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Cleaned Parcel Number') }}</p>
                                <p class="text-gray-900">{{ $this->viewApplication->clean_parcel_id ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Affected Area') }}</p>
                                <p class="text-gray-900">{{ $this->viewApplication->affected_area ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Sub Plot Number') }}</p>
                                <p class="text-gray-900 font-mono">{{ $this->viewApplication->sub_lot_number }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('District') }}</p>
                                <p class="text-gray-900">{{ $this->viewApplication->district->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Development Area') }}</p>
                                <p class="text-gray-900">{{ $this->viewApplication->development_area->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Development Sub Area') }}</p>
                                <p class="text-gray-900 font-mono">{{ $this->viewApplication->development_sub_area }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Local Area') }}</p>
                                <p class="text-gray-900">{{ $this->viewApplication->local_area ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Current use of Land') }}</p>
                                <p class="text-gray-900">{{ $this->viewApplication->change_of_land_use->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            <!-- Application Basic Info - Modern Card Layout -->
            @if($this->viewApplication->applicationSubmissions)
                @php
                    $applicationSubmission = $this->viewApplication->applicationSubmissions->first();
                @endphp
                <div class="bg-gradient-to-br from-gray-50 to-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                        </svg>
                        {{ __('Submission Details') }}
                    </h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Classification') }}</p>
                                <p class="text-gray-900">{{ $applicationSubmission->application_classification->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Submitted') }}</p>
                                <p class="text-gray-900">{{ $applicationSubmission->submission_date?->format('M d, Y') ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @if($applicationSubmission->application_text)
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">{{ __('Application Text') }}</p>
                            <div class="bg-gray-50/50 p-3 rounded-lg border border-gray-100">
                                <p class="text-gray-900 whitespace-pre-wrap text-sm">{{ preg_replace('/<br\s*\/?>/i', "\n",$applicationSubmission->application_text) }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

            <!-- Applicants Section - Modern Table -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <x-button wire:click="addApplicant({{ $this->viewApplication->id }})" icon="user-plus" title="Add Applicant"/>
                </div>
                @if($this->viewApplication->applicationApplicants && $this->viewApplication->applicationApplicants->count() > 0)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        {{ __('Applicants') }}
                    </h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        {{ $this->viewApplication->applicationApplicants->count() }} {{ Str::plural('applicant', $this->viewApplication->applicationApplicants->count()) }}
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Name') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Type') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Contact') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Details') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($this->viewApplication->applicationApplicants as $applicant)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                                            {{ strtoupper(substr($applicant->first_name, 0, 1)) }}{{ strtoupper(substr($applicant->last_name, 0, 1)) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $applicant->applicant_title->title ?? null }} {{ $applicant->first_name }} {{ $applicant->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $applicant->applicant_type->name ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $applicant->applicant_type->name === 'Primary' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $applicant->applicant_type->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $applicant->email ?? '-' }}</div>
                                    <div class="text-sm text-gray-500">{{ $applicant->phone ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $applicant->nrc_number ?? '-' }}</div>
                                    <div class="text-sm text-gray-500 truncate max-w-xs">{{ $applicant->address ?? '-' }}</div>
                                </td>
                                <td>
                                    <x-button wire:click="editApplicant({{ $applicant->id }})" icon="pencil" class="bg-blue-500 hover:bg-blue-600" />
                                    <x-button wire:click="confirmDeleteApplicant({{ $applicant->id }})" icon="trash" class="bg-red-500 hover:bg-red-600" />
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            </div>

            <div>
                <div class="flex justify-between items-center mb-4">
                    <x-button wire:click="addResolution({{ $this->viewApplication->id }})" icon="document-text" title="Add Resolution"/>
                </div>
                <!-- Resolutions Section - Timeline Style -->
            @if($this->viewApplication->applicationResolutions && $this->viewApplication->applicationResolutions->count() > 0)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        {{ __('Resolution History') }}
                    </h3>
                </div>
                <div class="overflow-hidden rounded-lg border border-gray-200 shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resolution Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($this->viewApplication->applicationResolutions as $resolution)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $resolution->resolution->resolution_type ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $resolution->resolution_date?->format('M j, Y \a\t g:i a') ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($resolution->resolution_details)
                                    <div class="text-sm text-gray-700 bg-gray-50/50 p-3 rounded-lg border border-gray-100 max-w-prose">
                                        <p class="whitespace-pre-wrap">{!! $resolution->resolution_details !!}</p>
                                    </div>
                                    @else
                                    <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td>
                                    <x-button wire:click="editResolution({{ $resolution->id }})" icon="pencil" class="bg-blue-500 hover:bg-blue-600" />
                                    <x-button wire:click="confirmDeleteResolution({{ $resolution->id }})" icon="trash" class="bg-red-500 hover:bg-red-600" />
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex justify-end px-6 py-4 bg-gray-50 border-t border-gray-100 rounded-b-xl">
            <x-button wire:click="$set('showViewModal', false)">
                {{ __('Close') }}
            </x-button>
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No application data') }}</h3>
            <p class="mt-1 text-sm text-gray-500">{{ __('Select an application to view details') }}</p>
        </div>
        @endif
    </x-card>
</x-modal>
        <x-modal wire:model="showResolutionModal" class="w-full max-w-7xl" blur="sm">
            <x-card title="{{ __('Add Resolution') }}">
                <form wire:submit.prevent="saveResolution">
                    <div class="flex gap-4 mt-4">
                        <div class="w-1/2">
                            <x-input
                                wire:model="resolution_date"
                                type="date"
                                label="{{ __('Resolution Date') }}"
                                required
                            />
                            </div>
                            <div class="w-1/2">
                                <x-select
                                    wire:model="resolutionId"
                                    :options="$this->resolutions"
                                    label="{{ __('Resolution') }}"
                                    placeholder="{{ __('Select Resolution') }}"
                                    option-label="resolution_type"
                                    option-value="id"
                                    required
                                />
                            </div>
                    </div>
                    <div class="w-full">
                        <x-textarea
                            wire:model="resolution_details"
                            label="{{ __('Resolution Details') }}"
                            placeholder="{{ __('Enter Resolution Details') }}"
                        />
                    </div>
                        <div class="flex justify-between mt-4">
                            <x-button wire:click="closeResolutionModal()" label="{{ __('Cancel') }}" />
                            <x-button type="submit" primary label="{{ __('Save') }}" />
                        </div>
                </form>
            </x-card>
                </x-modal>

        <x-modal wire:model="showApplicantModal" class="w-full max-w-7xl" blur="sm">
            <x-card title="{{ __('Add Applicant') }}">
                <form wire:submit.prevent="saveApplicant">
                    <div class="w-full">
                        <x-select
                            wire:model="applicant_title_id"
                            :options="$this->applicantTitles"
                            label="{{ __('Applicant Title') }}"
                            placeholder="{{ __('Select Applicant Title') }}"
                            option-label="title"
                            option-value="id"
                        />

                    </div>
                    <div class="flex gap-4 mt-4">
                        <div class="w-1/2">
                            <x-input
                                wire:model="first_name"
                                label="{{ __('First Name') }}"
                                placeholder="{{ __('Enter First Name') }}"
                                required
                            />
                        </div>
                        <div class="w-1/2">
                            <x-input
                                wire:model="last_name"
                                label="{{ __('Family Name') }}"
                                placeholder="{{ __('Enter Family Name') }}"
                                required
                            />
                        </div>
                    </div>
                    <div class="flex gap-4 mt-4">
                        <div class="w-1/">
                            <x-input
                                wire:model="middle_name"
                                label="{{ __('Middile Name') }}"
                                placeholder="{{ __('Enter Middlename') }}"

                            />
                        </div>
                        <div class="w-1/2">
                            <x-select
                                wire:model="applicant_type_id"
                                :options="$this->applicantTypes"
                                label="{{ __('Applicant Type') }}"
                                placeholder="{{ __('Select Applicant Type') }}"
                                option-label="name"
                                option-value="id"
                                required
                            />
                        </div>
                    </div>
                    <div class="flex gap-4 mt-4">
                        <div class="w-1/2">
                            <x-input
                                wire:model="phone"
                                label="{{ __('Phone') }}"
                                placeholder="{{ __('Enter Phone') }}"
                                required
                            />
                        </div>
                        <div class="w-1/2">
                            <x-input
                                wire:model="email"
                                label="{{ __('Email') }}"
                                placeholder="{{ __('Enter Email') }}"
                                required
                            />
                        </div>
                    </div>
                    <div class="flex gap-4 mt-4">
                        <div class="w-1/2">
                            <x-input
                                wire:model="address"
                                label="{{ __('Address') }}"
                                placeholder="{{__('Physical Address')}}"
                                required
                            />
                        </div>
                        <div class="w-1/2">
                            <x-input
                                wire:model="nrc_number"
                                label="{{ __('NRC Number') }}"
                                placeholder="{{ __('Enter NRC Number') }}"
                                required
                            />
                        </div>
                    </div>
                    <div class="mt-3 justify-center">
                        <div class="flex justify-between">
                            <x-button wire:click="closeApplicantModal()" label="{{ __('Cancel') }}" />
                            <x-button type="submit" primary label="{{ __('Save') }}" />
                        </div>
                    </div>
                </form>
            </x-card>
        </x-modal>

        <x-modal wire:model="showModal" class="w-[900px]">
            <x-card :title="$isEditing ? __('Edit Application') : __('Add Application')" class="w-[900px]">
                <form wire:submit.prevent="saveApplication">
                    <input wire:model="registrationOrganizationId" type="hidden">
                    <input wire:model="applicationClassificationId" type="hidden">

                    @if($this->registrationTypes->reg_key == 'CU')
                        <div class="w-full mb-4">
                            <x-select
                                wire:model="changeUseStageId"
                                :options="$this->changeUseStages"
                                label="{{ __('Change of Use Stage') }}"
                                placeholder="{{ __('Select Change of Use Stage') }}"
                                option-label="stage_name"
                                option-value="id"
                            />
                        </div>
                    @endif

                    <div class="flex gap-4">
                        <div class="w-1/2">
                            <x-select
                                wire:model.live="registrationAreaId"
                                :options="$this->registrationAreas"
                                label="{{ __('Registration Area') }}"
                                placeholder="{{ __('Select Registration Area') }}"
                                option-label="name"
                                option-value="id"
                                required
                            />
                        </div>

                        <div class="w-1/2">
                            <x-input
                                wire:model="applicationNumber"
                                label="{{ __('Application Number') }}"
                                placeholder="{{ __('Enter Application Number') }}"
                                readonly
                            />
                        </div>
                    </div>

                    <div class="w-full mt-4">
                        <x-checkbox
                            wire:model.live="is_institution"
                            label="{{ __('Is Institution') }}"
                        />
                    </div>

                    @if($this->is_institution)
                        <div class="w-full mt-4">
                            <x-input
                                wire:model="institution_name"
                                label="{{ __('Institution Name') }}"
                                placeholder="{{ __('Enter the name of the institution') }}"
                                required
                            />
                        </div>
                    @endif

                    <div class="flex gap-4 mt-4">
                        <div class="w-1/2">
                            <x-input
                                wire:model="parcel_id"
                                label="{{ __('Parcel ID') }}"
                                placeholder="{{ __('Enter Parcel ID') }}"
                                required
                            />
                        </div>
                        <div class="w-1/2">
                            <x-input
                                wire:model="clean_parcel_id"
                                label="{{ __('Cleaned Parcel ID') }}"
                                placeholder="{{ __('Enter Cleaned Parcel ID') }}"
                            />
                        </div>
                    </div>

                    <div class="flex gap-4 mt-4">
                        <div class="w-1/2">
                            <x-input
                                wire:model="sub_plot_number"
                                label="{{ __('Sub Plot Number') }}"
                                placeholder="{{ __('Enter Sub Plot Number') }}"
                            />
                        </div>
                        <div class="w-1/2">
                            <x-select
                                wire:model="districtId"
                                :options="$this->districts"
                                label="{{ __('District') }}"
                                placeholder="{{ __('Select District') }}"
                                option-label="name"
                                option-value="id"
                                required
                            />
                        </div>
                    </div>

                    <div class="flex gap-3 mt-4">
                        <div class="w-1/3">
                            <x-select
                                wire:model="landUseId"
                                :options="$this->landUses"
                                label="{{ __('Current Land Use') }}"
                                placeholder="{{ __('Select Land Use') }}"
                                option-label="name"
                                option-value="id"
                                required
                            />
                        </div>
                        <div class="w-1/3">
                            <x-select
                                wire:model="landUseFrom"
                                :options="$this->landUses"
                                label="{{ __('Change Land Use From') }}"
                                placeholder="{{ __('Select Land Use') }}"
                                option-label="name"
                                option-value="id"
                                required
                            />
                        </div>
                        <div class="w-1/2">
                            <x-select
                                wire:model="landUseTo"
                                :options="$this->landUses"
                                label="{{ __('Change Land Use To') }}"
                                placeholder="{{ __('Select Land Use') }}"
                                option-label="name"
                                option-value="id"
                                required
                            />
                        </div>
                    </div>
                    <div class="flex gap-4 mt-4">
                        <div class="w-1/2">
                            <x-select
                                wire:model="developmentAreaId"
                                :options="$this->developmentAreas"
                                label="{{ __('Development Area') }}"
                                placeholder="{{ __('Select Development Area') }}"
                                option-label="name"
                                option-value="id"
                                required
                            />
                        </div>
                    </div>

                    <div class="flex gap-4 mt-4">
                        <div class="w-1/2">
                            <x-select
                                wire:model="subAreaId"
                                :options="$this->subAreas"
                                label="{{ __('Location') }}"
                                placeholder="{{ __('Select Location') }}"
                                option-label="name"
                                option-value="id"
                                required
                            />
                        </div>
                        <div class="w-1/2">
                            <x-input
                                wire:model="applicationDate"
                                type="date"
                                label="{{ __('Application Date') }}"
                                required
                            />
                            @error('applicationDate')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="w-full mt-4">
                        <x-textarea
                            wire:model="applicationDescription"
                            label="{{ __('Application Description') }}"
                            placeholder="{{ __('Enter Application Description') }}"
                            required
                            rows="3"
                        />
                    </div>

                    <div class="w-full mt-4">
                        <x-select
                            wire:model="applicationStatus"
                            :options="[['value'=>'pending','label'=>'Pending'],['value'=>'approved','label'=>'Approved']]"
                            option-value="value"
                            option-label="label"
                            label="{{ __('Application Status') }}"
                        />
                    </div>

                    <div class="mt-3 justify-center">
                        <div class="flex justify-between">
                            <x-button wire:click="$toggle('showModal')" label="{{ __('Cancel') }}" />
                            <x-button type="submit" primary label="{{ __('Save') }}" />
                        </div>
                    </div>
                </form>
            </x-card>
        </x-modal>

        <!---delete applicant modal--------->
        <x-modal wire:model='showDeleteConfirmationApplicant' class='w-full max-w-7xl'>
            <x-card title="{{ __('Delete Applicant') }}">
                <p>Are you sure you want to delete this applicant?</p>
                <div class="mt-4 flex justify-end gap-3">
                    <x-button wire:click='deleteApplicant' negative label="Yes" />
                    <x-button wire:click='cancelDeleteApplicant' positive label="No" />
                </div>
            </x-card>
        </x-modal>

         <!---delete applicant modal--------->
        <x-modal wire:model='showDeleteConfirmationResolution' class='w-full max-w-7xl'>
            <x-card title="{{ __('Delete Resolution') }}">
                <p>Are you sure you want to delete this Resolution?</p>
                <div class="mt-4 flex justify-end gap-3">
                    <x-button wire:click='deleteResolution' negative label="Yes" />
                    <x-button wire:click='cancelDeleteResolution' positive label="No" />
                </div>
            </x-card>
        </x-modal>

         <!---delete applicant modal--------->
        <x-modal wire:model='showDeleteConfirmation' class='w-full max-w-7xl'>
            <x-card title="{{ __('Delete Application') }}">
                <p>Are you sure you want to delete this Application?</p>
                <div class="mt-4 flex justify-end gap-3">
                    <x-button wire:click='deleteApplication' negative label="Yes" />
                    <x-button wire:click='cancelDeleteApplication' positive label="No" />
                </div>
            </x-card>
        </x-modal>
    </x-card>
    <x-process/>
</div>
