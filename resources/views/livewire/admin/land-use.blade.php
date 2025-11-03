<?php

use Livewire\Volt\Component;
use App\Models\LandUs;

new class extends Component {
    //
    public $landUses = null;
    public $name = '';
    public $showModal = false;
    public $currentLandUse = null;
    public bool $isEditing = false;
    public bool $showDeleteConfirmation = false;
    public $landUseId = null;

    public function mount()
    {
        $this->landUses = LandUs::all();
    }
    public function openModal()
    {
        $this->resetErrorBag();
        $this->reset(['name']);
        $this->showModal = true;
        $this->isEditing = false;
        $this->currentLandUse = null;
    }
    public function openEditModal($landUseId)
    {
        $this->resetErrorBag();
        $this->reset(['name']);
        $this->currentLandUse = LandUs::find($landUseId);
        $this->name = $this->currentLandUse->name;
        $this->showModal = true;
        $this->isEditing = true;
    }
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->reset(['name']);
        $this->currentLandUse = null;
        $this->isEditing = false;
    }
    public function addLandUse()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
        ]);
        LandUs::create($validated);
        $this->dispatch('toastMagic',
            status: 'success', title: 'Success',
            message: 'Land use added successfully'
        );
        $this->reset(['name']);
        $this->landUses = LandUs::all();
        $this->showModal = false;
        $this->isEditing = false;
    }
    public function updateLandUse()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
        ]);
        $this->currentLandUse->update($validated);
        $this->dispatch('toastMagic',
            status: 'success', title: 'Success',
            message: 'Land use updated successfully'
        );
        $this->reset(['name']);
        $this->landUses = LandUs::all();
        $this->showModal = false;
        $this->isEditing = false;
    }
    public function deleteLandUse($landUseId)
    {
        $this->landUseId = $landUseId;
        $this->showDeleteConfirmation = true;
    }
    public function confirmDelete()
    {
        $landUse = LandUs::find($this->landUseId);
        if ($landUse) {
            $landUse->delete();
            $this->dispatch('toastMagic',
                status: 'success', title: 'Success',
                message: 'Land use deleted successfully'
            );
            $this->landUses = LandUs::all();
        }
        $this->showDeleteConfirmation = false;
        $this->reset(['landUseId']);
    }
    public function cancelDelete()
    {
        $this->showDeleteConfirmation = false;
        $this->reset(['landUseId']);
    }
}; ?>

<div class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-card title="Land Use" class="mx-auto">
        <x-slot name="action">
            <x-button wire:click="openModal" primary>Add Land Use</x-button>
        </x-slot>

        <table class="min-w-full divide-y divide-gray-200 mt-4">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($landUses as $landUse)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $landUse->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-button wire:click="openEditModal({{ $landUse->id }})" icon="pencil" label="Edit" />
                            <x-button wire:click="deleteLandUse({{ $landUse->id }})" icon="trash" label="Delete" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($showModal)
            <x-modal wire:model="showModal" title="{{ $isEditing ? 'Edit Land Use' : 'Add Land Use' }}">
                <x-card>
                    <form wire:submit.prevent="{{ $isEditing ? 'updateLandUse' : 'addLandUse' }}" enctype="multipart/form-data">
                        <x-input label="Name" wire:model.defer="name" required />
                        <div class="mt-6 flex justify-end space-x-2">
                            <x-button type="button" wire:click="closeModal" secondary>Cancel</x-button>
                            <x-button type="submit" primary>{{ $isEditing ? 'Update' : 'Add' }}</x-button>
                        </div>
                    </form>
                </x-card>
            </x-modal>
        @endif

        @if($showDeleteConfirmation)
            <x-modal wire:model="showDeleteConfirmation" title="Confirm Deletion">
                <x-card>
                    <p>Are you sure you want to delete this land use?</p>
                    <div class="mt-6 flex justify-end space-x-2">
                        <x-button wire:click="cancelDelete" secondary>Cancel</x-button>
                        <x-button wire:click="confirmDelete" danger>Delete</x-button>
                    </div>
                </x-card>
            </x-modal>
        @endif
    </x-card>
</div>
