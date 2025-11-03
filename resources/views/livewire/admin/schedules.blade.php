<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Application;
use App\Models\District;
use Barryvdh\DomPDF\Facade\Pdf;

new class extends Component {
    use WithPagination;

    public $registrationTypeId = null;
    public $search = '';
    public $date_from = '';
    public $date_to = '';
    public $application_range_from = null;
    public $application_range_to = null;
    public ?int $districtId = null;
    public $districts = null;
    public $meetingDate = '';
    public $pageLimit = 10;
    public $totalRecords = 0;
    public array $pageLimitOptions = [];
    public bool $selectAllApplications = false;
    public array $selectedApplications = []; // Array to store selected application IDs

    public function mount($registrationTypeId)
    {
        $this->totalRecords = Application::where('application_classification_id',$registrationTypeId)->count();
        $this->registrationTypeId = $registrationTypeId;
        $this->districts = District::all();
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
    }

    public function printSchedules()
    {
        $applications = Application::with([
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
    ])->whereIn('id', $this->selectedApplications)->get();
    $data['applications'] = $applications;
    $data['meetingDate'] = $this->meetingDate;

    $html = view('pdf.planning_applications',$data)->render();

    $pdf = PDF::loadHTML($html)
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial'
        ]);

    return $pdf->stream('planning_applications.pdf');


    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function updatedApplicationRangeFrom()
    {
        $this->resetPage();
    }

    public function updatedApplicationRangeTo()
    {
        $this->resetPage();
    }

    public function updatedDistrictId()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
         $this->date_from = '';
         $this->date_to = '';
         $this->application_range_from = null;
         $this->application_range_to = null;
         $this->districtId = null;
    }

    public function loadApplications()
    {
        $query = Application::query()
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
            ]);

        // Apply filters with AND logic instead of OR
        if ($this->application_range_from) {
            $query->where('registration_number', '>=', $this->application_range_from);
        }

        if ($this->application_range_to) {
            $query->where('registration_number', '<=', $this->application_range_to);
        }

        if ($this->date_from) {
            $query->where('application_date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->where('application_date', '<=', $this->date_to);
        }

        if ($this->districtId) {
            $query->where('district_id', $this->districtId);
        }

        return $query->orderBy('registration_number', 'desc')
                     ->paginate($this->pageLimit);
    }

    // Method to handle form submission
    public function filterApplications()
    {
        $this->resetPage();
    }

    // Toggle application selection
    public function toggleApplication($applicationId)
    {
        if (in_array($applicationId, $this->selectedApplications)) {
            $this->selectedApplications = array_diff($this->selectedApplications, [$applicationId]);
        } else {
            $this->selectedApplications[] = $applicationId;
        }
    }

    // Select all applications on current page
    public function selectAll()
    {
        $applications = $this->loadApplications();
        foreach ($applications as $application) {
            if (!in_array($application->id, $this->selectedApplications)) {
                $this->selectedApplications[] = $application->id;
            }
        }
    }

    public function updatedSelectAllApplications()
    {
        if ($this->selectAllApplications) {
            $this->selectAll();
        } else {
            $this->clearSelection();
        }
    }

    // Clear all selections
    public function clearSelection()
    {
        $this->selectedApplications = [];
    }

}; ?>

<div>
    <x-card title="Schedules">
        <form wire:submit.prevent='filterApplications' class="space-y-4">
        <div class="flex flex-wrap gap-4">
            <div class="w-full md:w-[48%]">
                <x-input
                    type="date"
                    wire:model.live='date_from'
                    label="Date From"
                />
            </div>
            <div class="w-full md:w-[48%]">
                <x-input
                    type="date"
                    wire:model.live='date_to'
                    label="Date To"
                />
            </div>
        </div>

        <div class="flex flex-wrap gap-4">
            <div class="w-full md:w-[48%]">
                <x-input
                    type="number"
                    wire:model.live='application_range_from'
                    label="Application Range From"
                />
            </div>
            <div class="w-full md:w-[48%]">
                <x-input
                    type="number"
                    wire:model.live='application_range_to'
                    label="Application Range To"
                />
            </div>
        </div>

        <div class="flex flex-wrap gap-4">
            <div class="w-full md:w-[48%]">
                <x-select
                    wire:model.live='districtId'
                    label="District"
                    :options="$districts"
                    option-label="name"
                    option-value="id"
                />
            </div>
            <div class="w-full md:w-[48%]">
                <x-input
                    wire:model.live='meetingDate'
                    label="Meeting Date"
                />
            </div>
        </div>

        <div class="mt-4">
            <x-button wire:click='resetFilters' primary type="button">Rest Filters</x-button>
        </div>
    </form>
    </x-card>

    <x-card title="Applications">
        <!-- Selection controls -->
        <div class="flex justify-between items-end my-4 gap-0.5">
            <div>
                <span class="text-sm text-gray-600">
                    {{ count($selectedApplications) }} applications selected
                </span>
            </div>
            @if(!empty($selectedApplications))
                    <div>
                        <form target="__blank" method="POST" action="{{ route('print.schedule') }}">
                            @csrf
                            @method('POST')
                            <input type="hidden" name="meetingDate" value="{{ $this->meetingDate }}" />
                            <input type="hidden" name="selectedApplications" value="{{ json_encode($this->selectedApplications) }}"/>
                            <x-button primary type="submit">Print Schedules</x-button>
                        </form>
                    </div>
                    <div>
                        <form target="__blank" method="POST" action="{{ route('print.schedule.mpdf') }}">
                            @csrf
                            @method('POST')
                            <input type="hidden" name="meetingDate" value="{{ $this->meetingDate }}" />
                            <input type="hidden" name="selectedApplications" value="{{ json_encode($this->selectedApplications) }}"/>
                            <x-button primary type="submit">Print Schedules MPDF</x-button>
                        </form>
                    </div>
                    <div>
                        <form target="__blank" method="POST" action="{{ route('print.notices') }}">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="meetingDate" value="{{ $this->meetingDate }}" />
                        <input type="hidden" name="selectedApplications" value="{{ json_encode($this->selectedApplications) }}"/>
                        <x-button primary type="submit">Print Notices</x-button>
                    </form>
                    </div>
                @endif


        </div>

        <!-- Applications table -->
        <div class="overflow-x-auto mt-4">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <x-checkbox
                                wire:model.live='selectAllApplications'
                                :label="$selectAllApplications ? 'Deselect All' : 'Select All'"
                            />
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicaiton #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">District</th>
                        <!-- Add other columns as needed -->
                    </tr>
                </thead>
                <tbody class=" divide-y divide-gray-200">
                    @forelse($this->loadApplications() as $application)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input
                                type="checkbox"
                                wire:click="toggleApplication({{ $application->id }})"
                                @if(in_array($application->id, $selectedApplications)) checked @endif
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            >
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $application->application_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $application->application_date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $application->district->name ?? '' }}</td>
                        <!-- Add other columns as needed -->
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            No applications found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $this->loadApplications()->links() }}
        </div>
    </x-card>
    <x-process/>
</div>
