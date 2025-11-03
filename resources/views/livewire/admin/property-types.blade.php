<?php

use Livewire\Volt\Component;
use App\Models\PropertyType;

new class extends Component {
    //
    public $propertyTypes = null;
    public $name = '';
    public $showModal = false;
    public $currentPropertyType = null;
    public bool $isEditing = false;
    public bool $showDeleteConfirmation = false;
    public $propertyTypeId = null;

    public function mount()
    {
        $this->propertyTypes = PropertyType::all();
    }
    public function openModal()
    {
        $this->resetErrorBag();
        $this->reset(['name']);
        $this->showModal = true;
        $this->isEditing = false;
        $this->currentPropertyType = null;
    }
    public function openEditModal($propertyTypeId)
    {
        $this->resetErrorBag();
        $this->reset(['name']);
        $this->currentPropertyType = PropertyType::find($propertyTypeId);
        $this->name = $this->currentPropertyType->name;
        $this->showModal = true;
        $this->isEditing = true;
    }
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->reset(['name']);
        $this->currentPropertyType = null;
        $this->isEditing = false;
    }
    public function addPropertyType()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
        ]);
        PropertyType::create($validated);
        $this->dispatch('toastMagic',
            status: 'success', title: 'Success',
            message: 'Property type added successfully'
        );
        $this->reset(['name']);
        $this->propertyTypes = PropertyType::all();
        $this->showModal = false;
        $this->isEditing = false;
    }
    public function updatePropertyType()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
        ]);
        $this->currentPropertyType->update($validated);
        $this->dispatch('toastMagic',
            status: 'success', title: 'Success',
            message: 'Property type updated successfully'
        );
        $this->reset(['name']);
        $this->propertyTypes = PropertyType::all();
        $this->showModal = false;
        $this->isEditing = false;
    }
    public function confirmDelete($propertyTypeId)
    {
        $this->propertyTypeId = $propertyTypeId;
        $this->showDeleteConfirmation = true;
    }
    public function deletePropertyType()
    {
        $propertyType = PropertyType::find($this->propertyTypeId);
        if ($propertyType) {
            $propertyType->delete();
            $this->dispatch('toastMagic',
                status: 'success', title: 'Success',
                message: 'Property type deleted successfully'
            );
            $this->propertyTypes = PropertyType::all();
        } else {
            $this->dispatch('toastMagic',
                status: 'error', title: 'Error',
                message: 'Property type not found'
            );
        }
        $this->showDeleteConfirmation = false;
    }
    public function resetState()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->reset(['name']);
        $this->currentPropertyType = null;
        $this->isEditing = false;
    }

}; ?>

<div class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-card>
        <x-slot name="title">
            Property Types
        </x-slot>
        <x-slot name="action">
            <x-button wire:click="openModal" primary>Add Property Type</x-button>
        </x-slot>

        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($propertyTypes as $propertyType)
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $propertyType->name }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <x-button wire:click="openEditModal({{ $propertyType->id }})" primary label="Edit" icon="pencil"/>
                            <x-button wire:click="confirmDelete({{ $propertyType->id }})" danger label="Delete" icon="trash"/>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>
    @if($showModal)
        <x-modal wire:model="showModal">
            <x-card title="{{ $isEditing ? 'Edit Property Type' : 'Add Property Type' }}">
                <form wire:submit.prevent="{{ $isEditing ? 'updatePropertyType' : 'addPropertyType' }}">
                    <div class="mb-4">
                        <x-input
                            label="Name"
                            wire:model.defer="name"
                            required
                            placeholder="Enter property type name"
                        />
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <x-button primary type="submit">{{ $isEditing ? 'Update Property Type' : 'Add Property Type' }}</x-button>
                        <x-button wire:click="closeModal" secondary>Cancel</x-button>
                    </div>
                </form>
            </x-card>
        </x-modal>
    @endif
    @if($showDeleteConfirmation)
        <x-modal wire:model="showDeleteConfirmation">
            <x-card title="Confirm Deletion">
                <p>Are you sure you want to delete this Property Type?</p>
                <div class="mt-4">
                    <x-button wire:click="deletePropertyType" danger label="Delete" />
                    <x-button wire:click="resetState" secondary label="Cancel" />
                </div>
            </x-card>
        </x-modal>
    @endif
</div>
