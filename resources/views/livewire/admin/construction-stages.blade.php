<?php

use Livewire\Volt\Component;
use App\Models\ConstructionStage;

new class extends Component {
    //
    public $constructionStages = null;
    public $stage_name = '';
    public $showModal = false;
    public $currentStage = null;
    public bool $isEditing = false;
    public bool $showDeleteConfirmation = false;
    public $stageId = null;

    public function mount()
    {
        $this->constructionStages = ConstructionStage::all();
    }
    public function openModal()
    {
        $this->resetErrorBag();
        $this->reset(['stage_name']);
        $this->showModal = true;
        $this->isEditing = false;
        $this->currentStage = null;
    }
    public function openEditModal($stageId)
    {
        $this->resetErrorBag();
        $this->reset(['stage_name']);
        $this->currentStage = ConstructionStage::find($stageId);
        $this->stage_name = $this->currentStage->stage_name;
        $this->showModal = true;
        $this->isEditing = true;
    }
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->reset(['stage_name']);
        $this->currentStage = null;
        $this->isEditing = false;
    }
    public function addStage()
    {
        $validated = $this->validate([
            'stage_name' => 'required|string|max:255',
        ]);
        ConstructionStage::create($validated);
        $this->dispatch('toastMagic',
            status: 'success', title: 'Success',
            message: 'Construction stage added successfully'
        );
        $this->reset(['stage_name']);
        $this->constructionStages = ConstructionStage::all();
        $this->showModal = false;
        $this->isEditing = false;
    }
    public function updateStage()
    {
        $validated = $this->validate([
            'stage_name' => 'required|string|max:255',
        ]);
        $this->currentStage->update($validated);
        $this->dispatch('toastMagic',
            status: 'success', title: 'Success',
            message: 'Construction stage updated successfully'
        );
        $this->reset(['stage_name']);
        $this->constructionStages = ConstructionStage::all();
        $this->showModal = false;
        $this->isEditing = false;
    }
    public function confirmDelete($stageId)
    {
        $this->stageId = $stageId;
        $this->showDeleteConfirmation = true;
    }
    public function deleteStage()
    {
        $stage = ConstructionStage::find($this->stageId);
        if ($stage) {
            $stage->delete();
            $this->dispatch('toastMagic',
                status: 'success', title: 'Success',
                message: 'Construction stage deleted successfully'
            );
            $this->constructionStages = ConstructionStage::all();
        } else {
            $this->dispatch('toastMagic',
                status: 'error', title: 'Error',
                message: 'Construction stage not found'
            );
        }
        $this->showDeleteConfirmation = false;
        $this->reset(['stageId']);
    }
}; ?>

<div class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-card title="Construction Stages" class="mx-auto">
        <x-slot name="action">
            <x-button wire:click="openModal" primary>Add Construction Stage</x-button>
        </x-slot>

        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Stage Name</th>
                    <th class="px-6 py-3 border-b-2 border-gray-200">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($constructionStages as $stage)
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $stage->stage_name }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <x-button wire:click="openEditModal({{ $stage->id }})" primary label="Edit" icon="pencil"/>
                            <x-button wire:click="confirmDelete({{ $stage->id }})" danger label="Delete" icon="trash"/>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </x-card>
    @if($showModal)
        <x-modal wire:model="showModal">
            <x-card :title="$isEditing ? 'Edit Construction Stage' : 'Add Construction Stage'">
                <form wire:submit.prevent="{{ $isEditing ? 'updateStage' : 'addStage' }}">
                    <div class="mb-4">
                        <x-input
                            label="Stage Name"
                            wire:model.defer="stage_name"
                            required
                            placeholder="Enter stage name"
                        />
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <x-button primary type="submit">{{ $isEditing ? 'Update Stage' : 'Add Stage' }}</x-button>
                        <x-button wire:click="closeModal" secondary>Cancel</x-button>
                    </div>
                </form>
            </x-card>
        </x-modal>
    @endif
    @if($showDeleteConfirmation)
        <x-modal wire:model="showDeleteConfirmation">
            <x-card title="Confirm Deletion">
                <p>Are you sure you want to delete this construction stage?</p>
                <div class="mt-4">
                    <x-button wire:click="deleteStage" danger label="Delete" />
                    <x-button wire:click="closeDeleteConfirmation" secondary label="Cancel" />
                </div>
            </x-card>
        </x-modal>
    @endif
</div>
