<?php

use Livewire\Volt\Component;
use App\Models\District;
use App\Models\RegistrationArea;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;

new class extends Component {
    //
    use WithPagination;
    public $registrationAreas = null;
    public $districts = null;
    public $showModal = false;
    public $currentArea = null;
    public string $name = '';
    public string $area_key = '';
    public $district_id = null;
    public $isEditing = false;
    public $showDeleteConfirmation = false;
    public $areaId = null;
    public array $pageLimitOptions = [];
    public $pageLimit = 10;
    public $totalRecords = 0;

    public function mount()
    {
        $this->registrationAreas = RegistrationArea::with('district')->get();
        $this->districts = District::all();
        $this->totalRecords = RegistrationArea::count();
        $this->pageLimitOptions =  [
        ['label'=>10,'value'=>10],
        ['label'=>25,'value'=>25],
        ['label'=>50,'value'=>50],
        ['label'=>100,'value'=>100],
        ['label'=>250,'value'=>250],
        ['label'=>500,'value'=>500],
        ['label'=>number_format(1000),'value'=>1000],
        ['label'=>'View All ('.number_format($this->totalRecords).')','value'=>$this->totalRecords],];
    }

    public function loadData()
    {
        return RegistrationArea::query()->orderBy('id','desc')->paginate($this->pageLimit);
    }
    public function openModal()
    {
        $this->resetErrorBag();
        $this->reset(['name', 'district_id','area_key']);
        $this->showModal = true;
        $this->isEditing = false;
        $this->currentArea = null;
    }
    public function openEditModal($areaId)
    {
        $this->resetErrorBag();
        $this->reset(['name', 'district_id','area_key']);
        $this->currentArea = RegistrationArea::find($areaId);
        $this->name = $this->currentArea->name;
        $this->area_key = $this->currentArea->area_key;
        $this->district_id = $this->currentArea->district_id;
        $this->showModal = true;
        $this->isEditing = true;
    }
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->reset(['name', 'district_id']);
        $this->currentArea = null;
        $this->isEditing = false;
    }
    public function addArea()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'area_key' => 'required|string|max:255',
            'district_id' => 'required|exists:' . District::class . ',id',
        ]);
        RegistrationArea::create($validated);
        $this->dispatch('toastMagic',
            status: 'success',
            title: 'Success',
            message: 'Registration Area added successfully'
        );
        $this->reset(['name', 'district_id', 'area_key']);
        $this->showModal = false;
        $this->isEditing = false;
        $this->closeModal();
        $this->registrationAreas = RegistrationArea::with('district')->get();
        $this->dispatch('toastMagic',
            status: 'success',
            title: 'Success',
            message: 'Registration Area added successfully'
        );
    }
    public function editArea()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'area_key' => 'required|string|max:255',
            'district_id' => 'required|exists:' . District::class . ',id',
        ]);
        $this->currentArea->update($validated);
        $this->dispatch('toastMagic',
            status: 'success',
            title: 'Success',
            message: 'Registration Area updated successfully'
        );
        $this->reset(['name', 'district_id', 'area_key']);
        $this->showModal = false;
        $this->isEditing = false;
        $this->closeModal();
        $this->registrationAreas = RegistrationArea::with('district')->get();
        $this->dispatch('toastMagic',
            status: 'success',
            title: 'Success',
            message: 'Registration Area updated successfully'
        );
    }
    public function confirmDelete($areaId)
    {
        $this->areaId = $areaId;
        $this->showDeleteConfirmation = true;
    }
    public function deleteArea()
    {
        $area = RegistrationArea::find($this->areaId);
        if ($area) {
            $area->delete();
            $this->dispatch('toastMagic',
                status: 'success',
                title: 'Success',
                message: 'Registration Area deleted successfully'
            );
            $this->registrationAreas = RegistrationArea::with('district')->get();
        }
        $this->showDeleteConfirmation = false;
        $this->areaId = null;
        $this->dispatch('toastMagic',
            status: 'success',
            title: 'Success',
            message: 'Registration Area deleted successfully'
        );
    }

}; ?>

<div class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-card title="Registration Areas" class="mx-auto">
        <x-slot name="action">
            <x-button primary wire:click="openModal">Add Registration Area</x-button>
        </x-slot>
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
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area Key</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">District</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class=" divide-y divide-gray-200">
                @foreach($this->loadData() as $area)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $area->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $area->area_key }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $area->district->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-button wire:click="openEditModal({{ $area->id }})" icon="pencil" label="Edit" />
                            <x-button wire:click="confirmDelete({{ $area->id }})" icon="trash" label="Delete" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $this->loadData()->links() }}
    </x-card>
    <x-modal
        title="{{ $isEditing ? 'Edit Registration Area' : 'Add Registration Area' }}"
        wire:model="showModal"
        maxWidth="2xl"
    >
        <x-card>
            <form wire:submit.prevent="{{ $isEditing ? 'editArea' : 'addArea' }}">
            <div class="space-y-4">
                <x-input
                    label="Area Name"
                    wire:model.defer="name"
                    required
                    placeholder="Enter area name"
                />
                <x-input
                    label="Area Key"
                    wire:model.defer="area_key"
                    required
                    placeholder="Enter area key"
                />
                <x-select
                    label="District"
                    wire:model.defer="district_id"
                    required
                    :options="$districts"
                    placeholder="Select a district"
                    option-label="name"
                    option-value="id"
                />
                
            </div>
            <div class="mt-4 flex justify-end space-x-2">
                <x-button primary type="submit">{{ $isEditing ? 'Update Registration Area' : 'Add Registration Area' }}</x-button>
                <x-button secondary wire:click="closeModal">Cancel</x-button>
            </div>
        </form>
        </x-card>
    </x-modal>
    <x-modal
        title="Delete Registration Area"
        wire:model="showDeleteConfirmation"
        maxWidth="sm"
    >
        <p>Are you sure you want to delete this registration area?</p>
        <div class="mt-4 flex justify-end space-x-2">
            <x-button
                negative
                wire:click="deleteArea"
                label="Delete"
            />
            <x-button
                secondary
                wire:click="$set('showDeleteConfirmation', false)"
                label="Cancel"
            />
        </div>
    </x-modal>
</div>
