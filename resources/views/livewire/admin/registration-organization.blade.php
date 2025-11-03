<?php

use Livewire\Volt\Component;
use App\Models\RegistrationOrganization;

new class extends Component {
    /*
    */
    public $name = '';
    public $showModal = false;
    public $currentOrganization = null;
    public bool $isEditing = false;
    public $registrationOrganizations = null;
    public bool $showDeleteConfirmation = false;
    public $organizationId = null;
    
    public function mount()
    {
        $this->registrationOrganizations = RegistrationOrganization::all();
    }
    public function openModal()
    {
        $this->resetErrorBag();
        $this->reset(['name']);
        $this->showModal = true;
        $this->isEditing = false;
        $this->currentOrganization = null;
    }
    public function openEditModal($organizationId)
    {
        $this->resetErrorBag();
        $this->reset(['name']);
        $this->currentOrganization = RegistrationOrganization::find($organizationId);
        $this->name = $this->currentOrganization->name;
        $this->showModal = true;
        $this->isEditing = true;
    }
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->reset(['name']);
        $this->currentOrganization = null;
        $this->isEditing = false;
    }
    public function addOrganization()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
        ]);
        RegistrationOrganization::create($validated);
        $this->dispatch('toastMagic',
            status: 'success', title: 'Success',
            message: 'Organization added successfully'
        );
        $this->reset(['name']);
        $this->registrationOrganizations = RegistrationOrganization::all();
        $this->showModal = false;
        $this->isEditing = false;
    }
    public function updateOrganization()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
        ]);
        $this->currentOrganization->update($validated);
        $this->dispatch('toastMagic',
            status: 'success', title: 'Success',
            message: 'Organization updated successfully'
        );
        $this->reset(['name']);
        $this->registrationOrganizations = RegistrationOrganization::all();
        $this->showModal = false;
        $this->isEditing = false;
    }
    public function confirmDelete($organizationId)
    {
        $this->organizationId = $organizationId;
        $this->showDeleteConfirmation = true;
    }
    public function deleteOrganization()
    {
        $organization = RegistrationOrganization::find($this->organizationId);
        if ($organization) {
            $organization->delete();
            $this->dispatch('toastMagic',
                status: 'success', title: 'Success',
                message: 'Organization deleted successfully'
            );
            $this->registrationOrganizations = RegistrationOrganization::all();
        } else {
            $this->dispatch('toastMagic',
                status: 'error', title: 'Error',
                message: 'Organization not found'
            );
        }
        $this->showDeleteConfirmation = false;
    }

}; ?>

<div class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-card title="Registration Organizations" class="mx-auto">
        <x-slot name="action">
            <x-button wire:click="openModal" primary>Add Organization</x-button>
        </x-slot>
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($registrationOrganizations as $organization)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $organization->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <x-button wire:click="openEditModal({{ $organization->id }})" secondary>Edit</x-button>
                            <x-button wire:click="confirmDelete({{ $organization->id }})" danger>Delete</x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($showDeleteConfirmation)
            <x-modal wire:model="showDeleteConfirmation">
                <x-slot name="title">Confirm Deletion</x-slot>
                <p>Are you sure you want to delete this organization?</p>
                <x-slot name="footer">
                    <x-button wire:click="deleteOrganization" danger>Delete</x-button>
                    <x-button wire:click="$set('showDeleteConfirmation', false)" secondary>Cancel</x-button>
                </x-slot>
            </x-modal>  
        @endif
        @if($showModal)
            <x-modal wire:model="showModal">
                <x-card title="{{ $isEditing ? 'Edit Organization' : 'Add Organization' }}">
                    <form wire:submit.prevent="{{ $isEditing ? 'updateOrganization' : 'addOrganization' }}">
                        <x-input label="Name" wire:model.defer="name" required />
                        <div class="mt-6 flex justify-end space-x-2">
                            <x-button type="button" wire:click="closeModal" secondary>Cancel</x-button>
                            <x-button type="submit" primary>{{ $isEditing ? 'Update' : 'Add' }}</x-button>
                        </div>
                    </form>
                </x-card>
            </x-modal>
        @endif  
    </x-card>

</div>
