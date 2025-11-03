<?php

use Livewire\Volt\Component;
use App\Models\SubArea;
use App\Models\RegistrationArea;

new class extends Component {
    /*
    [
		'registration_area_id',
		'name'
	]*/
    public $subAreas = null;
    public $registrationAreas = null;
    public $showModal = false;
    public $currentSubArea = null;
    public string $name = '';
    public $registration_area_id = null;
    public $isEditing = false;
    public $showDeleteConfirmation = false;
    public $subAreaId = null;

    public function mount()
    {
        $this->subAreas = SubArea::all();
        $this->registrationAreas = RegistrationArea::all();
    }

    public function openModal()
    {
        $this->resetErrorBag();
        $this->reset(['name', 'registration_area_id']);
        $this->showModal = true;
        $this->isEditing = false;
        $this->currentSubArea = null;
    }
    public function openEditModal($subAreaId)
    {
        $this->resetErrorBag();
        $this->reset(['name', 'registration_area_id']);
        $this->currentSubArea = SubArea::find($subAreaId);
        $this->name = $this->currentSubArea->name;
        $this->registration_area_id = $this->currentSubArea->registration_area_id;
        $this->showModal = true;
        $this->isEditing = true;
    }
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->reset(['name', 'registration_area_id']);
        $this->currentSubArea = null;
        $this->isEditing = false;
    }
    public function addSubArea()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'registration_area_id' => 'required|exists:' . RegistrationArea::class . ',id',
        ]);
        SubArea::create($validated);

        $this->dispatch('toastMagic',
            status: 'success',
            title: 'Success',
            message: 'Sub Area added successfully'
        );

        $this->reset(['name', 'registration_area_id']);
        $this->showModal = false;
        $this->isEditing = false;
        $this->subAreas = SubArea::all();
    }
    public function editSubArea()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'registration_area_id' => 'required|exists:' . RegistrationArea::class . ',id',
        ]);
        $this->currentSubArea->update($validated);

        $this->dispatch('toastMagic',
            status: 'success',
            title: 'Success',
            message: 'Sub Area updated successfully'
        );

        $this->reset(['name', 'registration_area_id']);
        $this->showModal = false;
        $this->isEditing = false;
        $this->closeModal();
        $this->subAreas = SubArea::all();
    }
    public function confirmDelete($subAreaId)
    {
        $this->subAreaId = $subAreaId;
        $this->showDeleteConfirmation = true;
    }
    public function deleteSubArea()
    {
        $subArea = SubArea::find($this->subAreaId);
        if ($subArea) {
            $subArea->delete();
            $this->dispatch('toastMagic',
                status: 'success',
                title: 'Success',
                message: 'Sub Area deleted successfully'
            );
            $this->subAreas = SubArea::all();
        } else {
            $this->dispatch('toastMagic',
                status: 'error',
                title: 'Error',
                message: 'Sub Area not found'
            );
        }
        $this->showDeleteConfirmation = false;
        $this->subAreaId = null;
    }
    public function closeDeleteConfirmation()
    {
        $this->showDeleteConfirmation = false;
        $this->subAreaId = null;
        $this->dispatch('toastMagic',
            status: 'info',
            title: 'Cancelled',
            message: 'Sub Area deletion cancelled'
        );
    }
    
}; ?>

<div class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
    

    @if($showModal)
        <x-modal wire:model="showModal">
            <x-card title="{{ $isEditing ? 'Edit Sub Area' : 'Add Sub Area' }}">
                <form wire:submit.prevent="{{ $isEditing ? 'editSubArea' : 'addSubArea' }}">
                    <div class="mb-4">
                        <x-input type="text" id="name" 
                        wire:model.defer="name" 
                        class="mt-1 block w-full" required/>
                    </div>
                    <div class="mb-4">
                        <x-select
                            label="Registration Area"
                            wire:model.defer="registration_area_id"
                            :options="$registrationAreas"
                            option-label="name"
                            option-value="id"
                            required
                            placeholder="Select Registration Area"
                        />
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <x-button primary type="submit">{{ $isEditing ? 'Update Sub Area' : 'Add Sub Area' }}</x-button>
                        <x-button wire:click="closeModal" secondary>Cancel</x-button>
                    </div>
                </form>
            </x-card>
        </x-modal>
                        
    @endif

    @if($showDeleteConfirmation)
        <x-modal
            wire:model="showDeleteConfirmation"
        >
            <x-card title="Confirm Deletion">
                <p>Are you sure you want to delete this Sub Area?</p>
                <div class="mt-4">
                    <x-button wire:click="deleteSubArea" danger label="Delete" />
                    <x-button wire:click="closeDeleteConfirmation" secondary label="Cancel" />
                </div>
            </x-card>
        </x-modal>
    @endif

    <x-card title="Sub Areas" class="mx-auto">
        <x-slot name="action">
            <x-button wire:click="openModal" primary>Add Sub Area</x-button>
        </x-slot>
        <table class="min-w-full">
        <thead>
            <tr>
                <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 border-b-2 border-gray-200  text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Registration Area</th>
                <th class="px-6 py-3 border-b-2 border-gray-200">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subAreas as $subArea)
                <tr>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $subArea->name }}</td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $subArea->registration_area->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                        <x-button wire:click="openEditModal({{ $subArea->id }})" primary label="Edit" icon="pencil"/>
                        <x-button wire:click="confirmDelete({{ $subArea->id }})" danger lable="Delete" icon="trash"/>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </x-card>
</div>
