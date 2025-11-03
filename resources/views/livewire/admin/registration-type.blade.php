<?php

use Livewire\Volt\Component;
use App\Models\RegistrationType;

new class extends Component {
    /*[
		'name',
		'reg_key'
	]*/
    public $registrationTypes = null;
    public $showModal = false;
    public $currentType = null;
    public string $name = '';
    public string $reg_key = '';
    public $isEditing = false;
    public $showDeleteConfirmation = false;
    public $typeId = null;

    public function mount()
    {
        $this->registrationTypes = RegistrationType::all();
    }

    public function openModal()
    {
        $this->resetErrorBag();
        $this->reset(['name', 'reg_key']);
        $this->showModal = true;
        $this->isEditing = false;
        $this->currentType = null;
    }

    public function openEditModal($typeId)
    {
        $this->resetErrorBag();
        $this->reset(['name', 'reg_key']);
        $this->currentType = RegistrationType::find($typeId);
        $this->name = $this->currentType->name;
        $this->reg_key = $this->currentType->reg_key;
        $this->showModal = true;
        $this->isEditing = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->reset(['name', 'reg_key']);
        $this->currentType = null;
        $this->isEditing = false;
    }

    public function addRegistrationType()
    {
        // Logic to add a registration type
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'reg_key' => 'required|string|max:255',
        ]);
        
        RegistrationType::create($validated);
        
        $this->dispatch('toastMagic',
            status: 'success',
            title: 'Success',
            message: 'Registration Type added successfully'
        );
        
        $this->reset(['name', 'reg_key']);
        $this->showModal = false;
        $this->isEditing = false;
        $this->registrationTypes = RegistrationType::all();
    }
    public function editRegistrationType()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'reg_key' => 'required|string|max:255',
        ]);
        
        $this->currentType->update($validated);
        
        $this->dispatch('toastMagic',
            status: 'success',
            title: 'Success',
            message: 'Registration Type updated successfully'
        );
        
        $this->reset(['name', 'reg_key']);
        $this->showModal = false;
        $this->isEditing = false;
        $this->closeModal();
        $this->registrationTypes = RegistrationType::all();
    }
    public function deleteRegistrationType($typeId)
    {
        $this->typeId = $typeId;
        $this->showDeleteConfirmation = true;
    }
    public function confirmDelete()
    {
        if ($this->typeId) {
            RegistrationType::destroy($this->typeId);
            $this->dispatch('toastMagic',
                status: 'success',
                title: 'Success',
                message: 'Registration Type deleted successfully'
            );
            $this->registrationTypes = RegistrationType::all();
        }
        $this->showDeleteConfirmation = false;
        $this->typeId = null;
    }
}; ?>

<div class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-card title="Registration Types" class="mx-auto">
               <x-slot name="action">
                    <x-button wire:click="openModal" primary>Add Registration Type</x-button>
                </x-slot>

        @if($registrationTypes->isEmpty())
            <p class="text-gray-500">No registration types found.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Registration Key</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($registrationTypes as $type)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $type->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $type->reg_key }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <x-button wire:click="openEditModal({{ $type->id }})" secondary>Edit</x-button>
                                <x-button wire:click="deleteRegistrationType({{ $type->id }})" danger>Delete</x-button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        @if($showModal)
            <x-modal wire:model="showModal">
                <x-slot name="title">
                    {{ $isEditing ? 'Edit Registration Type' : 'Add Registration Type' }}
                </x-slot>
                <form wire:submit.prevent="{{ $isEditing ? 'editRegistrationType' : 'addRegistrationType' }}">
                    <div class="space-y-4">
                        <x-input label="Name" wire:model.defer="name" required />
                        <x-input label="Registration Key" wire:model.defer="reg_key" required />
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <x-button wire:click="closeModal" secondary>Cancel</x-button>
                        <x-button type="submit" primary>{{ $isEditing ? 'Update' : 'Add' }}</x-button>
                    </div>
                </form>
            </x-modal>
        @endif
        @if($showDeleteConfirmation)
            <x-modal wire:model="showDeleteConfirmation">
                <x-slot name="title">Delete Registration Type</x-slot>
                <p>Are you sure you want to delete this registration type?</p>
                <div class="mt-4 flex justify-end space-x-2">
                    <x-button wire:click="confirmDelete" primary>Delete</x-button>
                    <x-button wire:click="$set('showDeleteConfirmation', false)" secondary>Cancel</x-button>
                </div>
            </x-modal>
        @endif
    </x-card>
</div>
