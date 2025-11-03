<?php

use Livewire\Volt\Component;
use App\Models\District;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;
    public $districts = null;
    public $aliases = '';
    public $showModal = false;
    public $currentDistrict = null;
    public string $name = '';
    public $isEditing = false;
    public $showDeleteConfirmation = false;
    public $districtId = null;
    public $pageLimit = 10;
    public $totalRecords = 0;
    public array $pageLimitOptions = [];

    public function mount()
    {
        $this->districts = District::with('aliases')->get();
        $this->totalRecords = District::count();
        $this->pageLimitOptions =  [
            ['label'=>5,'value'=>5],
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

    public function loadData()
    {
        return District::query()->orderBy('id','desc')->paginate($this->pageLimit);
    }

    public function openModal()
    {
        $this->resetErrorBag();
        $this->reset(['name', 'aliases']);
        $this->showModal = true;
        $this->isEditing = false;
        $this->currentDistrict = null;
    }

    public function openEditModal($districtId)
    {
        $this->resetErrorBag();
        $this->reset(['name', 'aliases']);
        $this->currentDistrict = District::with('aliases')->find($districtId);
        $this->name = $this->currentDistrict->name;
        $this->aliases = $this->currentDistrict->aliases->pluck('alias')->implode(', ');
        $this->showModal = true;
        $this->isEditing = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->reset(['name', 'aliases']);
        $this->currentDistrict = null;
        $this->isEditing = false;
    }

    public function addDistrict()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255|unique:districts,name',
            'aliases' => 'nullable|string'
        ]);

        $district = District::create([
            'name' => $validated['name'],
        ]);

        if (!empty($validated['aliases'])) {
            $aliasArray = array_filter(array_map(function ($alias) {
                return ['alias' => trim(strtoupper($alias))];
            }, explode(',', $validated['aliases'])));

            $district->aliases()->createMany($aliasArray);
        }

        $this->dispatch('toastMagic',
            status: 'success',
            title: 'Success',
            message: 'District added successfully'
        );
        
        $this->closeModal();
        $this->districts = District::with('aliases')->get();
    }

    public function editDistrict()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255|unique:districts,name,' . $this->currentDistrict->id,
            'aliases' => 'nullable|string'
        ]);

        if ($this->currentDistrict) {
            $this->currentDistrict->update(['name' => $validated['name']]);

            // Update aliases
            $this->currentDistrict->aliases()->delete();
            
            if (!empty($validated['aliases'])) {
                $aliasArray = array_filter(array_map(function ($alias) {
                    return ['alias' => trim(strtoupper($alias))];
                }, explode(',', $validated['aliases'])));
                
                $this->currentDistrict->aliases()->createMany($aliasArray);
            }

            $this->dispatch('toastMagic',
                status: 'success',
                title: 'Success',
                message: 'District updated successfully'
            );
        }

        $this->closeModal();
        $this->districts = District::with('aliases')->get();
    }

    public function confirmDelete($districtId)
    {
        $this->districtId = $districtId;
        $this->showDeleteConfirmation = true;
    }

    public function deleteDistrict()
    {
        if ($this->districtId) {
            $district = District::find($this->districtId);
            if ($district) {
                $district->delete();
                $this->dispatch('toastMagic',
                    status: 'success',
                    title: 'Success',
                    message: 'District deleted successfully'
                );
                $this->districts = District::with('aliases')->get();
            } else {
                $this->dispatch('toastMagic',
                    status: 'error',
                    title: 'Error',
                    message: 'District not found'
                );
            }
        }
        $this->showDeleteConfirmation = false;
        $this->districtId = null;
    }
}; ?>

<div class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-card title="District Management" subtitle="Manage districts in the system">
        <x-slot name="action">
            <x-button primary wire:click="openModal">Add District</x-button>
        </x-slot>

        <div class="mt-4">
            @if($this->loadData()->isEmpty())
                <x-alert icon="o-information-circle" class="mb-4">
                    No districts found. Add your first district!
                </x-alert>
            @else
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">District Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aliases</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($this->loadData() as $district)
                            <tr wire:key="district-{{ $district->id }}" class="hover:">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $district->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $district->aliases->pluck('alias')->implode(', ') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <x-button secondary wire:click="openEditModal({{ $district->id }})">Edit</x-button>
                                    <x-button danger wire:click="confirmDelete({{ $district->id }})">Delete</x-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $this->loadData()->links() }}
            @endif
        </div>
    </x-card>

    <!-- Add/Edit Modal -->
    <x-modal wire:model="showModal" maxWidth="md">
        <x-card :title="$isEditing ? 'Edit District' : 'Add District'">
            @if($errors->any())
                <x-alert icon="o-exclamation-triangle" class="mb-4" color="red">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif

            <form wire:submit.prevent="{{ $isEditing ? 'editDistrict' : 'addDistrict' }}">
                <div class="space-y-4">
                    <x-input
                        label="District Name"
                        id="district_name"
                        wire:model="name"
                        required
                    />

                    <x-input
                        label="Aliases (comma separated)"
                        id="district_aliases"
                        wire:model="aliases"
                        placeholder="e.g. NYC, NEW YORK"
                    />
                </div>

                <div class="mt-6 flex justify-end space-x-4">
                    <x-button secondary wire:click="closeModal">Cancel</x-button>
                    <x-button primary type="submit">
                        {{ $isEditing ? 'Update' : 'Save' }}
                    </x-button>
                </div>
            </form>
        </x-card>
    </x-modal>

    <!-- Delete Confirmation Modal -->
    <x-modal
        wire:model="showDeleteConfirmation"
       
    >
    <x-card  title="Delete District">
        <p>Are you sure you want to delete this district? This action cannot be undone.</p>
        <div class="mt-6 flex justify-end space-x-4">
            <x-button secondary wire:click="closeModal">Cancel</x-button>
            <x-button danger wire:click="deleteDistrict">Delete</x-button>
        </div>

    </x-card>
</x-modal>
</div>