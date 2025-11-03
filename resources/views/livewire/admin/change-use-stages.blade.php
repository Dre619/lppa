<?php

use Livewire\Volt\Component;
use App\Models\ChangeUseStage;

new class extends Component {
    /*[
        'stage_name',
        'description'
    ]*/
    public $changeUseStages = null;
    public $stage_name = '';
    public $description = '';
    public $showModal = false;
    public $currentChangeUseStage = null;
    public bool $isEditing = false;
    public bool $showDeleteConfirmation = false;
    public $changeUseStageId = null;

    public function mount()
    {
        $this->changeUseStages = ChangeUseStage::all();
    }
    public function openModal()
    {
        $this->resetErrorBag();
        $this->reset(['stage_name', 'description']);
        $this->showModal = true;
        $this->isEditing = false;
        $this->currentChangeUseStage = null;
    }
    public function openEditModal($changeUseStageId)
    {
        $this->resetErrorBag();
        $this->reset(['stage_name', 'description']);
        $this->currentChangeUseStage = ChangeUseStage::find($changeUseStageId);
        $this->stage_name = $this->currentChangeUseStage->stage_name;
        $this->description = $this->currentChangeUseStage->description;
        $this->showModal = true;
        $this->isEditing = true;
    }
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->reset(['stage_name', 'description']);
        $this->currentChangeUseStage = null;
        $this->isEditing = false;
    }
    public function addChangeUseStage()
    {
        $validated = $this->validate([
            'stage_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);
        ChangeUseStage::create($validated);
        $this->dispatch('toastMagic',
            status: 'success', title: 'Success',
            message: 'Change of use stage added successfully'
        );
        $this->reset(['stage_name', 'description']);
        $this->changeUseStages = ChangeUseStage::all();
        $this->showModal = false;
        $this->isEditing = false;
    }
    public function updateChangeUseStage()
    {
        $validated = $this->validate([
            'stage_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);
        $this->currentChangeUseStage->update($validated);
        $this->dispatch('toastMagic',
            status: 'success', title: 'Success',
            message: 'Change of use stage updated successfully'
        );
        $this->reset(['stage_name', 'description']);
        $this->changeUseStages = ChangeUseStage::all();
        $this->showModal = false;
        $this->isEditing = false;
    }
    public function deleteChangeUseStage($changeUseStageId)
    {
        $this->currentChangeUseStage = ChangeUseStage::find($changeUseStageId);
        if ($this->currentChangeUseStage) {
            $this->showDeleteConfirmation = true;
            $this->changeUseStageId = $changeUseStageId;
        } else {
            $this->dispatch('toastMagic',
                status: 'error', title: 'Error',
                message: 'Change of use stage not found'
            );
        }
    }
    public function confirmDelete()
    {
        $changeUseStage = ChangeUseStage::find($this->changeUseStageId);
        if ($changeUseStage) {
            $changeUseStage->delete();
            $this->dispatch('toastMagic',
                status: 'success', title: 'Success',
                message: 'Change of use stage deleted successfully'
            );
            $this->changeUseStages = ChangeUseStage::all();
        } else {
            $this->dispatch('toastMagic',
                status: 'error', title: 'Error',
                message: 'Change of use stage not found'
            );
        }
        $this->showDeleteConfirmation = false;
        $this->reset(['changeUseStageId']);
    }
    public function cancelDelete()
    {
        $this->showDeleteConfirmation = false;
        $this->resetErrorBag();
        $this->reset(['changeUseStageId']);
        $this->currentChangeUseStage = null;
        $this->isEditing = false;
    }

}; ?>

<div class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-card title="Change of Use Stages" class="mx-auto">
        <x-slot name="action">
            <x-button wire:click="openModal" icon="plus" label="Add Change of Use Stage" />
        </x-slot>
        <div class="mt-4">
            @if($changeUseStages->isEmpty())
                <p class="text-gray-500">No change of use stages available.</p>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stage Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class=" divide-y divide-gray-200">
                        @foreach($changeUseStages as $stage)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $stage->stage_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $stage->description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-button wire:click="openEditModal({{ $stage->id }})" icon="pencil" label="Edit" />
                                    <x-button wire:click="deleteChangeUseStage({{ $stage->id }})" icon="trash" label="Delete" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            @if($showModal)
                <x-modal wire:model="showModal" title="{{ $isEditing ? 'Edit Change of Use Stage' : 'Add Change of Use Stage' }}">
                    <x-card>
                        <form wire:submit.prevent="{{ $isEditing ? 'updateChangeUseStage' : 'addChangeUseStage' }}">
                            <div class="space-y-4">
                                <x-input label="Stage Name" wire:model.defer="stage_name" required />
                                <x-textarea label="Description" wire:model.defer="description" />
                            </div>
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
                        <p>Are you sure you want to delete this change of use stage?</p>
                    <div class="mt-6 flex justify-end space-x-2">
                        <x-button wire:click="cancelDelete" secondary>Cancel</x-button>
                        <x-button wire:click="confirmDelete" danger>Delete</x-button>
                    </div>
                    </x-card>
                </x-modal>
            @endif
        </x-card>
</div>

