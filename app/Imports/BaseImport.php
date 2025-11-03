<?php
namespace App\Imports;

use App\Models\ImportLog;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;

abstract class BaseImport implements WithEvents
{
    protected $importLogId;
    protected $importLog;

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function(BeforeImport $event) {
                if ($this->importLogId) {
                    $this->importLog = ImportLog::findOrFail($this->importLogId);
                    $this->importLog->update(['status' => 'processing']);
                }
            },
            AfterImport::class => function(AfterImport $event) {
                if ($this->importLog) {
                    $this->importLog->update([
                        'status' => 'completed',
                        'completed_at' => now()
                    ]);
                }
            },
        ];
    }
}