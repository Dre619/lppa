<?php

use Livewire\Volt\Component;
use App\Models\Applicationclassification;

new class extends Component {
    
    public $applicationClasses = null;
    public $showModal = false;
    public $currentClass = null;
    public string $classification = '';
    public string $description = '';
    public $isEditing = false;
    public $showDeleteConfirmation = false;
    public $classId = null;

    public function mount()
    {
        $this->applicationClasses = Applicationclassification::all();
    }

    public function openModal()
    {
        $this->resetErrorBag();
        $this->reset(['classification']);
        $this->showModal = true;
        $this->isEditing = false;
        $this->currentClass = null;
    }

    public function openEditModal($classId)
    {
        $this->resetErrorBag();
        $this->reset(['classification']);
        $this->currentClass = Applicationclassification::find($classId);
        $this->classification = $this->currentClass->classification;
        $this->showModal = true;
        $this->isEditing = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->reset(['classification']);
        $this->currentClass = null;
        $this->isEditing = false;
    }

    public function addApplicationClass()
    {
        $validated = $this->validate([
            'classification' => 'required|string|max:255',
        ]);
        
        Applicationclassification::create($validated);
        
        $this->dispatch('toastMagic',
            status: 'success',
            title: 'Success',
            message: 'Application Class added successfully'
        );
        
        $this->reset(['classification']);
        $this->showModal = false;
        $this->isEditing = false;
        $this->applicationClasses = Applicationclassification::all();
    }
    public function editApplicationClass()
    {
        $validated = $this->validate([
            'classification' => 'required|string|max:255',
        ]);
        
        $this->currentClass->update($validated);
        
        $this->dispatch('toastMagic',
            status: 'success',
            title: 'Success',
            message: 'Application Class updated successfully'
        );
        
        $this->reset(['classification']);
        $this->applicationClasses = Applicationclassification::all();
        $this->showModal = false;
        $this->isEditing = false;
    }
    public function deleteApplicationClass($classId)
    {
        $this->classId = $classId;
        $this->showDeleteConfirmation = true;
    }
    public function confirmDelete()
    {
        if ($this->classId) {
            Applicationclassification::destroy($this->classId);
            $this->dispatch('toastMagic',
                status: 'success',
                title: 'Success',
                message: 'Application Class deleted successfully'
            );
            $this->applicationClasses = Applicationclassification::all();
        }
        $this->showDeleteConfirmation = false;
        $this->classId = null;
    }
    public function cancelDelete()
    {
        $this->showDeleteConfirmation = false;
        $this->classId = null;
    }
}; ?>

<div class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-card title="Application classifications" class="mx-auto">
        <x-slot name="action">
            <x-button wire:click="openModal" primary>Add classification</x-button>
        </x-slot>

        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">classification</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class=" divide-y divide-gray-200">
                @foreach($applicationClasses as $class)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $class->classification }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <x-button wire:click="openEditModal({{ $class->id }})" secondary>Edit</x-button>
                            <x-button wire:click="deleteApplicationClass({{ $class->id }})" danger>Delete</x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($showModal)
            <x-modal
                wire:model="showModal"
            >
            <x-card
                title="{{ $isEditing ? 'Edit Application classification' : 'Add Application classification' }}"
            >
                <form wire:submit.prevent="{{ $isEditing ? 'editApplicationClass' : 'addApplicationClass' }}">
                    <div class="space-y-4">
                        <x-input
                            label="classification"
                            wire:model.defer="classification"
                            required
                        />
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <x-button wire:click="closeModal" secondary>Cancel</x-button>
                        <x-button type="submit" primary>{{ $isEditing ? 'Update' : 'Add' }}</x-button>
                    </div>
                </form>
            </x-card>
                
            </x-modal>
        @endif

        @if($showDeleteConfirmation)
            <x-modal
                title="Confirm Deletion"
                wire:model="showDeleteConfirmation"
                footerActions="confirmDelete, cancelDelete"
            >
                <p>Are you sure you want to delete this application classification?</p>
            </x-modal>
        @endif
    </x-card>

</div>
