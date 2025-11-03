<?php

use Livewire\Volt\Component;
use App\Models\StopOrder;
use App\Models\Warning;
use App\Models\Enforcement;
use Livewire\WithFileUploads;
use Carbon\Carbon;


new class extends Component {
    use WithFileUploads;
    
    public $stopOrderId = null;
    public $warningId = null;
    public $enforcementId = null;
    public $jsonFile = null;
    public $editModal = false;
    public $deleteModal = false;
    public $warningModal = false;
    public $enforcementModal = false;
    public $warningEditModal = false;
    public $enforcementEditModal = false;
    public $warningDeleteModal = false;
    public $enforcementDeleteModal = false;
    public $showWarningsModal = false;
    public $showEnforcementsModal = false;
    
    // Form fields for StopOrder
    public $REMARKS = '';
    public $District = '';
    public $Location = '';
    public $Name = '';
    public $Plot_No = '';
    public $Phone_No = '';
    public $Description_of__Development = '';
    public $Stage_of__Construction = '';
    public $Observation_Notes = '';
    public $Inspection__Officer = '';
    public $Supervisor = '';
    public $Date = '';
    public $Zoning = '';
    public $Picture = '';
    public $Response_Date = '';
    public $Responded = '';

    // Warning fields
    public $notice_date = '';

    // Enforcement fields
    public $date_issued = '';
    public $description = '';
    public $current_stage = '';
    public $status = 'pending';

    public function mount()
    {
        // Initialize if needed
    }

    public function stop_orders()
    {
        return StopOrder::with(['warnings', 'enforcements'])->paginate(10);
    }

    public function import_json()
    {
        $this->validate([
            'jsonFile' => 'required|file|mimes:json'
        ]);

        $file = $this->jsonFile->getRealPath();
        $contents = file_get_contents($file);
        $json = json_decode($contents);

        if (!isset($json->features)) {
            
            $this->dispatch('toastMagic',status:'error',title:'Error',message: "Invalid JSON format");
            return;
        }

        $imported = 0;
        $updated = 0;
        $dates = ['Response_Date','Date'];

        foreach ($json->features as $data) {
            $keys = [];
            $values = [];

            foreach ($data->properties as $key => $value) {
                $cleanedKey = rtrim(rtrim(str_replace(' ', '_', str_replace('/', '_', $key)), '_'), '.');
                $keys[] = $cleanedKey;
                if(in_array($cleanedKey,$dates))
            {
                $values[] = trim($value === '' ? 'NO DATA' : $this->parseDate($value));
            } else {
                $values[] = trim($value === '' ? 'NO DATA' : $value);
            }
                
            }

            $record = array_combine($keys, $values);

            $result = StopOrder::updateOrInsert(
                ['Plot_No' => $record['Plot_No'] ?? null],
                $record
            );

            $result ? $imported++ : $updated++;
        }

       
        $this->dispatch('toastMagic',status:'success',title:'Success',message: "Imported $imported records, updated $updated records");

        $this->jsonFile = null;
    }

    protected function parseDate(?string $date): ?Carbon
    {
        try {
            if (!$date) {
                return null;
            }

            // If it's numeric (e.g. Excel serial like 43285)
            if (is_numeric($date)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date));
            }

            // If it's a string date
            return Carbon::createFromFormat('d/m/Y', $date);
        } catch (\Exception $e) {
            Log::error("Date parsing failed for '{$date}': " . $e->getMessage());
            return null;
        }
    }

    // StopOrder CRUD methods
    public function edit($id)
    {
        $stopOrder = StopOrder::findOrFail($id);
        $this->stopOrderId = $id;
        
        // Set all fields from the model
        $this->REMARKS = $stopOrder->REMARKS;
        $this->District = $stopOrder->District;
        $this->Location = $stopOrder->Location;
        $this->Name = $stopOrder->Name;
        $this->Plot_No = $stopOrder->Plot_No;
        $this->Phone_No = $stopOrder->Phone_No;
        $this->Description_of__Development = $stopOrder->Description_of__Development;
        $this->Stage_of__Construction = $stopOrder->Stage_of__Construction;
        $this->Observation_Notes = $stopOrder->Observation_Notes;
        $this->Inspection__Officer = $stopOrder->Inspection__Officer;
        $this->Supervisor = $stopOrder->Supervisor;
        $this->Date = $stopOrder->Date;
        $this->Zoning = $stopOrder->Zoning;
        $this->Picture = $stopOrder->Picture;
        $this->Response_Date = $stopOrder->Response_Date;
        $this->Responded = $stopOrder->Responded;

        $this->editModal = true;
    }

    public function update()
    {
        $validated = $this->validate([
            'REMARKS' => 'nullable|string',
            'District' => 'required|string',
            'Location' => 'required|string',
            'Name' => 'required|string',
            'Plot_No' => 'required|string',
            'Phone_No' => 'required|string',
            'Description_of__Development' => 'required|string',
            'Stage_of__Construction' => 'required|string',
            'Observation_Notes' => 'required|string',
            'Inspection__Officer' => 'required|string',
            'Supervisor' => 'required|string',
            'Date' => 'required|date',
            'Zoning' => 'required|string',
            'Picture' => 'nullable|string',
            'Response_Date' => 'nullable|date',
            'Responded' => 'nullable|string',
        ]);

        StopOrder::find($this->stopOrderId)->update($validated);

        $this->dispatch('toastMagic',status:'success',title:'Success',message: "Stop order updated successfully");

        $this->editModal = false;
        $this->resetFields();
    }

    public function confirmDelete($id)
    {
        $this->stopOrderId = $id;
        $this->deleteModal = true;
    }

    public function delete()
    {
        StopOrder::find($this->stopOrderId)->delete();

        $this->dispatch('toastMagic',status:'success',title:'Success',message: "Stop Order deleted successfully!");

        $this->deleteModal = false;
        $this->stopOrderId = null;
    }

    // Warning CRUD methods
    public function showWarnings($stopOrderId)
    {
        $this->stopOrderId = $stopOrderId;
        $this->showWarningsModal = true;
    }

    public function createWarning($stopOrderId)
    {
        $this->stopOrderId = $stopOrderId;
        $this->resetWarningFields();
        $this->warningModal = true;
    }

    public function storeWarning()
    {
        $validated = $this->validate([
            'notice_date' => 'required|date',
        ]);

        Warning::create([
            'stop_order_id' => $this->stopOrderId,
            'notice_date' => $validated['notice_date'],
        ]);

        $this->dispatch('toastMagic', status: 'success', title: 'Success', message: 'Warning created successfully');

        $this->warningModal = false;
        $this->resetWarningFields();
    }

    public function editWarning($id)
    {
        $warning = Warning::findOrFail($id);
        $this->warningId = $id;
        $this->stopOrderId = $warning->stop_order_id;
        $this->notice_date = $warning->notice_date ? $warning->notice_date->format('Y-m-d') : '';

        $this->warningEditModal = true;
    }

    public function updateWarning()
    {
        $validated = $this->validate([
            'notice_date' => 'required|date',
        ]);

        Warning::find($this->warningId)->update($validated);

        $this->dispatch('toastMagic', status: 'success', title: 'Success', message: 'Warning updated successfully');

        $this->warningEditModal = false;
        $this->resetWarningFields();
    }

    public function confirmDeleteWarning($id)
    {
        $this->warningId = $id;
        $this->warningDeleteModal = true;
    }

    public function deleteWarning()
    {
        Warning::find($this->warningId)->delete();

        $this->dispatch('toastMagic', status: 'success', title: 'Success', message: 'Warning deleted successfully');

        $this->warningDeleteModal = false;
        $this->warningId = null;
    }

    // Enforcement CRUD methods
    public function showEnforcements($stopOrderId)
    {
        $this->stopOrderId = $stopOrderId;
        $this->showEnforcementsModal = true;
    }

    public function createEnforcement($stopOrderId)
    {
        $this->stopOrderId = $stopOrderId;
        $this->resetEnforcementFields();
        $this->enforcementModal = true;
    }

    public function storeEnforcement()
    {
        $validated = $this->validate([
            'date_issued' => 'required|date',
            'description' => 'nullable|string',
            'current_stage' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        Enforcement::create([
            'stop_order_id' => $this->stopOrderId,
            'date_issued' => $validated['date_issued'],
            'description' => $validated['description'],
            'current_stage' => $validated['current_stage'],
            'status' => $validated['status'],
        ]);

        $this->dispatch('toastMagic', status: 'success', title: 'Success', message: 'Enforcement created successfully');

        $this->enforcementModal = false;
        $this->resetEnforcementFields();
    }

    public function editEnforcement($id)
    {
        $enforcement = Enforcement::findOrFail($id);
        $this->enforcementId = $id;
        $this->stopOrderId = $enforcement->stop_order_id;
        $this->date_issued = $enforcement->date_issued ? $enforcement->date_issued->format('Y-m-d') : '';
        $this->description = $enforcement->description;
        $this->current_stage = $enforcement->current_stage;
        $this->status = $enforcement->status;

        $this->enforcementEditModal = true;
    }

    public function updateEnforcement()
    {
        $validated = $this->validate([
            'date_issued' => 'required|date',
            'description' => 'nullable|string',
            'current_stage' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        Enforcement::find($this->enforcementId)->update($validated);

        $this->dispatch('toastMagic', status: 'success', title: 'Success', message: 'Enforcement updated successfully');

        $this->enforcementEditModal = false;
        $this->resetEnforcementFields();
    }

    public function confirmDeleteEnforcement($id)
    {
        $this->enforcementId = $id;
        $this->enforcementDeleteModal = true;
    }

    public function deleteEnforcement()
    {
        Enforcement::find($this->enforcementId)->delete();

        $this->dispatch('toastMagic', status: 'success', title: 'Success', message: 'Enforcement deleted successfully');

        $this->enforcementDeleteModal = false;
        $this->enforcementId = null;
    }

    private function resetFields()
    {
        $this->reset([
            'REMARKS', 'District', 'Location', 'Name', 'Plot_No', 'Phone_No',
            'Description_of__Development', 'Stage_of__Construction', 'Observation_Notes',
            'Inspection__Officer', 'Supervisor', 'Date', 'Zoning', 'Picture',
            'Response_Date', 'Responded'
        ]);
    }

    private function resetWarningFields()
    {
        $this->notice_date = '';
        $this->warningId = null;
    }

    private function resetEnforcementFields()
    {
        $this->date_issued = '';
        $this->description = '';
        $this->current_stage = '';
        $this->status = 'pending';
        $this->enforcementId = null;
    }
}; ?>

<div>
    <x-card title="Stop Orders Management" separator>
        <div class="mb-4">
             {{-- Loading indicator --}}
                <span wire:loading wire:target="jsonFile" class="text-blue-500 text-sm mt-1">
                    Uploading file...
                </span>

                {{-- File ready confirmation --}}
                @if ($jsonFile)
                    <i class="text-green-600 text-sm mt-1">
                        File "{{ $jsonFile->getClientOriginalName() }}" is ready to import.
                    </i>
                @endif
            <div class="flex  justify-start gap-3 items-start mb-4">
                <div>
                    <x-input.file type="file" label="Import JSON File" wire:model="jsonFile" accept=".json,.geojson" />
                </div>
                <div>
                    <x-button :disabled="!$jsonFile?true:false" wire:click="import_json" primary label="Import" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plot No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">District</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warnings</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enforcements</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class=" divide-y divide-gray-200">
                    @foreach($this->stop_orders() as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->Plot_No }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->Name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->Location }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->District }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->Phone_No }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                    {{ $order->warnings->count() }}
                                </span>
                                <x-button wire:click="showWarnings({{ $order->id }})" icon="eye" xs flat />
                                <x-button wire:click="createWarning({{ $order->id }})" icon="plus" xs positive />
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                    {{ $order->enforcements->count() }}
                                </span>
                                <x-button wire:click="showEnforcements({{ $order->id }})" icon="eye" xs flat />
                                <x-button wire:click="createEnforcement({{ $order->id }})" icon="plus" xs negative />
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-button wire:click="edit({{ $order->id }})" icon="pencil" primary xs />
                            <x-button wire:click="confirmDelete({{ $order->id }})" icon="trash" negative xs class="ml-2" />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $this->stop_orders()->links() }}
        </div>
    </x-card>

    <!-- Edit Modal -->
    <x-modal wire:model.defer="editModal">
        <x-card title="Edit Stop Order" separator>
            <div class="space-y-4">
                <x-input label="Remarks" wire:model="REMARKS" />
                <x-input label="District" wire:model="District" />
                <x-input label="Location" wire:model="Location" />
                <x-input label="Name" wire:model="Name" />
                <x-input label="Plot No" wire:model="Plot_No" />
                <x-input label="Phone No" wire:model="Phone_No" />
                <x-textarea label="Description of Development" wire:model="Description_of__Development" />
                <x-input label="Stage of Construction" wire:model="Stage_of__Construction" />
                <x-textarea label="Observation Notes" wire:model="Observation_Notes" />
                <x-input label="Inspection Officer" wire:model="Inspection__Officer" />
                <x-input label="Supervisor" wire:model="Supervisor" />
                <x-input type="date" label="Date" wire:model="Date" without-time />
                <x-input label="Zoning" wire:model="Zoning" />
                <x-input label="Picture" wire:model="Picture" />
                <x-input type="date" label="Response Date" wire:model="Response_Date" without-time />
                <x-input label="Responded" wire:model="Responded" />
            </div>

            <x-slot name="footer">
                <div class="flex justify-end gap-x-4">
                    <x-button flat label="Cancel" x-on:click="close" />
                    <x-button primary label="Save" wire:click="update" />
                </div>
            </x-slot>
        </x-card>
    </x-modal>

    <!-- Delete Confirmation Modal -->
    <x-modal wire:model.defer="deleteModal">
        <x-card title="Delete Stop Order" separator>
            <p class="text-gray-600">Are you sure you want to delete this stop order? This action cannot be undone.</p>

            <x-slot name="footer">
                <div class="flex justify-end gap-x-4">
                    <x-button flat label="Cancel" x-on:click="close" />
                    <x-button negative label="Delete" wire:click="delete" />
                </div>
            </x-slot>
        </x-card>
    </x-modal>

    <!-- Show Warnings Modal -->
    <x-modal wire:model.defer="showWarningsModal">
        <x-card title="Warnings" separator>
            <div class="space-y-4">
                @if($stopOrderId)
                    @php $stopOrder = App\Models\StopOrder::find($stopOrderId); @endphp
                    @if($stopOrder && $stopOrder->warnings->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notice Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($stopOrder->warnings as $warning)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $warning->notice_date ? $warning->notice_date->format('Y-m-d') : 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-button wire:click="editWarning({{ $warning->id }})" icon="pencil" primary xs />
                                            <x-button wire:click="confirmDeleteWarning({{ $warning->id }})" icon="trash" negative xs class="ml-2" />
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No warnings found.</p>
                    @endif
                @endif
            </div>

            <x-slot name="footer">
                <div class="flex justify-end gap-x-4">
                    <x-button flat label="Close" x-on:click="close" />
                </div>
            </x-slot>
        </x-card>
    </x-modal>

    <!-- Show Enforcements Modal -->
    <x-modal wire:model.defer="showEnforcementsModal">
        <x-card title="Enforcements" separator>
            <div class="space-y-4">
                @if($stopOrderId)
                    @php $stopOrder = App\Models\StopOrder::find($stopOrderId); @endphp
                    @if($stopOrder && $stopOrder->enforcements->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Issued</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($stopOrder->enforcements as $enforcement)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $enforcement->date_issued ? $enforcement->date_issued->format('Y-m-d') : 'N/A' }}</td>
                                        <td class="px-6 py-4">{{ Str::limit($enforcement->description, 50) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $enforcement->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                   ($enforcement->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 
                                                   ($enforcement->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800')) }}">
                                                {{ ucfirst($enforcement->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-button wire:click="editEnforcement({{ $enforcement->id }})" icon="pencil" primary xs />
                                            <x-button wire:click="confirmDeleteEnforcement({{ $enforcement->id }})" icon="trash" negative xs class="ml-2" />
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No enforcements found.</p>
                    @endif
                @endif
            </div>

            <x-slot name="footer">
                <div class="flex justify-end gap-x-4">
                    <x-button flat label="Close" x-on:click="close" />
                </div>
            </x-slot>
        </x-card>
    </x-modal>

    <!-- Create Warning Modal -->
    <x-modal wire:model.defer="warningModal">
        <x-card title="Create Warning" separator>
            <div class="space-y-4">
                <x-input type="date" label="Notice Date" wire:model="notice_date" required />
            </div>

            <x-slot name="footer">
                <div class="flex justify-end gap-x-4">
                    <x-button flat label="Cancel" x-on:click="close" />
                    <x-button primary label="Create" wire:click="storeWarning" />
                </div>
            </x-slot>
        </x-card>
    </x-modal>

    <!-- Edit Warning Modal -->
    <x-modal wire:model.defer="warningEditModal">
        <x-card title="Edit Warning" separator>
            <div class="space-y-4">
                <x-input type="date" label="Notice Date" wire:model="notice_date" required />
            </div>

            <x-slot name="footer">
                <div class="flex justify-end gap-x-4">
                    <x-button flat label="Cancel" x-on:click="close" />
                    <x-button primary label="Update" wire:click="updateWarning" />
                </div>
            </x-slot>
        </x-card>
    </x-modal>

    <!-- Delete Warning Confirmation Modal -->
    <x-modal wire:model.defer="warningDeleteModal">
        <x-card title="Delete Warning" separator>
            <p class="text-gray-600">Are you sure you want to delete this warning? This action cannot be undone.</p>

            <x-slot name="footer">
                <div class="flex justify-end gap-x-4">
                    <x-button flat label="Cancel" x-on:click="close" />
                    <x-button negative label="Delete" wire:click="deleteWarning" />
                </div>
            </x-slot>
        </x-card>
    </x-modal>

    <!-- Create Enforcement Modal -->
    <x-modal wire:model.defer="enforcementModal">
        <x-card title="Create Enforcement" separator>
            <div class="space-y-4">
                <x-input type="date" label="Date Issued" wire:model="date_issued" required />
                <x-textarea label="Description" wire:model="description" />
                <x-input label="Current Stage" wire:model="current_stage" />
                <x-select label="Status" wire:model="status" :options="[
                    ['label' => 'Pending', 'value' => 'pending'],
                    ['label' => 'In Progress', 'value' => 'in_progress'],
                    ['label' => 'Completed', 'value' => 'completed'],
                    ['label' => 'Cancelled', 'value' => 'cancelled']
                ]" option-label="label" option-value="value" />
            </div>

            <x-slot name="footer">
                <div class="flex justify-end gap-x-4">
                    <x-button flat label="Cancel" x-on:click="close" />
                    <x-button primary label="Create" wire:click="storeEnforcement" />
                </div>
            </x-slot>
        </x-card>
    </x-modal>

    <!-- Edit Enforcement Modal -->
    <x-modal wire:model.defer="enforcementEditModal">
        <x-card title="Edit Enforcement" separator>
            <div class="space-y-4">
                <x-input type="date" label="Date Issued" wire:model="date_issued" required />
                <x-textarea label="Description" wire:model="description" />
                <x-input label="Current Stage" wire:model="current_stage" />
                <x-select label="Status" wire:model="status" :options="[
                    ['label' => 'Pending', 'value' => 'pending'],
                    ['label' => 'In Progress', 'value' => 'in_progress'],
                    ['label' => 'Completed', 'value' => 'completed'],
                    ['label' => 'Cancelled', 'value' => 'cancelled']
                ]" option-label="label" option-value="value" />
            </div>

            <x-slot name="footer">
                <div class="flex justify-end gap-x-4">
                    <x-button flat label="Cancel" x-on:click="close" />
                    <x-button primary label="Update" wire:click="updateEnforcement" />
                </div>
            </x-slot>
        </x-card>
    </x-modal>

    <!-- Delete Enforcement Confirmation Modal -->
    <x-modal wire:model.defer="enforcementDeleteModal">
        <x-card title="Delete Enforcement" separator>
            <p class="text-gray-600">Are you sure you want to delete this enforcement? This action cannot be undone.</p>

            <x-slot name="footer">
                <div class="flex justify-end gap-x-4">
                    <x-button flat label="Cancel" x-on:click="close" />
                    <x-button negative label="Delete" wire:click="deleteEnforcement" />
                </div>
            </x-slot>
        </x-card>
    </x-modal>
</div>