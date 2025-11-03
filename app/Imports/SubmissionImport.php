<?php

namespace App\Imports;

use App\Models\{
    Application,
    ApplicationApplicant,
    ApplicationResolution,
    ApplicationSubmission
};
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue as QueueShouldQueue;
use App\Imports\BaseImport;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class SubmissionImport extends BaseImport implements OnEachRow, WithHeadingRow, QueueShouldQueue, WithChunkReading
{
    protected $importLogId;
    protected $importLog;

    public function __construct($importLogId)
    {
        $this->importLogId = $importLogId;
    }

    public function onRow(Row $row)
    {
        $data = array_change_key_case($row->toArray(), CASE_LOWER);

        try {
            // Create or update application
            $application = $this->createOrUpdateApplication($data);
            
            if ($application) {
                // Create submission
                $this->createSubmission($application, $data);
                
                // Create applicants
                $this->createApplicants($application, $data);
                
                // Create resolutions
                $this->createResolutions($application, $data);
            }
            // Update processed rows
            $this->importLog->increment('processed_rows');
        } catch (\Exception $e) {
            Log::error("Error importing submission row {$data['s_reg_num']}: " . $e->getMessage());
            // Collect failed rows if needed
            $this->importLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now()
            ]);
            throw $e;
        }
    }
    
    protected function createOrUpdateApplication(array $data): ?Application
    {
        $registrationOrgId = getOrganizationId($data['s_reg_org'] ?? null);
        $registrationAreaId = getRegistrationAreaId($data['s_reg_area'] ?? null);
        $districtId = getDistrictId($data['s_loc_district'] ?? null);
        $developmentAreaId = getDevelopmentAreaId($data['s_loc_area'] ?? null);

        if (!$registrationOrgId || !$registrationAreaId || !$districtId) {
            Log::warning("Missing required lookup for submission application {$data['s_reg_num']}");
            return null;
        }

        $applicationId = $this->generateApplicationId($data);

        // Check if application with this ID already exists
        if (Application::where('application_id', $applicationId)->exists()) {
            $this->importLog->increment('processed_rows');
            $this->importLog->increment('skipped_rows');
            return null; // Skip this row
        }

        if(!getRegistrationTypeId($data['s_reg_type']))
        {
            $this->importLog->increment('processed_rows');
            $this->importLog->increment('skipped_rows');
            return null;
        }

        return Application::updateOrCreate(
            ['registration_number' => $data['s_reg_num']],
            [
                'application_id' => $applicationId,
                'registration_sub_number' => $data['s_reg_subnum'] ?? null,
                'registration_organization_id' => $registrationOrgId,
                'registration_area_id' => $registrationAreaId,
                'application_classification_id' => getRegistrationTypeId($data['s_reg_type']),
                'application_date' => $this->parseDate($data['s_date_submission']),
                'is_institution' => !empty($data['s_institution']),
                'institution_name' => $data['s_institution'] ?? null,
                'development_area_id' => $developmentAreaId,
                'development_sub_area' => $data['s_address_dev'] ?? null,
                'local_area' => $data['s_loc_area'],
                'district_id' => $districtId,
                'parcel_id' => $data['s_loc_address'],
                'clean_parcel_id' => $data['s_loc_address'],
                'global_key' => $data['s_reg_num'],
                'application_status' => 'Submitted'
            ]
        );
    }
    
    protected function createSubmission(Application $application, array $data)
    {
        $submissionText = implode(' ', array_filter([
            $data['s_type_applications'] ?? null,
            $data['s_prep01'] ?? null,
            $data['s_size_applications'] ?? null,
            $data['s_prep02'] ?? null,
            $data['s_loc_address'] ?? null
        ]));

        ApplicationSubmission::create([
            'application_id' => $application->id,
            'application_classification_id' => getRegistrationTypeId($data['s_reg_type']),
            'application_text' => $submissionText,
            'submission_date' => $this->parseDate($data['s_date_submission']),
        ]);
    }
    
    protected function createApplicants(Application $application, array $data)
    {
        $applicants = [];
        
        // Primary applicant (01)
        if ($this->hasApplicantData($data, '01')) {
            $applicants[] = [
                'application_id' => $application->id,
                'applicant_title_id' => getApplicantTitleId($data['s_name_title01'] ?? null),
                'first_name' => $data['s_name_first01'] ?? null,
                'last_name' => $data['s_name_family01'] ?? null,
                'middle_name' => $data['s_name_middle01'] ?? null,
                'applicant_type_id' => getApplicantTypeId('Primary')
            ];
        }
        
        // Secondary applicant (03)
        if ($this->hasApplicantData($data, '03')) {
            $applicants[] = [
                'application_id' => $application->id,
                'applicant_title_id' => getApplicantTitleId($data['s_name_title03'] ?? null),
                'first_name' => $data['s_name_first03'] ?? null,
                'last_name' => $data['s_name_family03'] ?? null,
                'middle_name' => $data['s_name_middle03'] ?? null,
                'applicant_type_id' => getApplicantTypeId('Secondary')
            ];
        }
        
        foreach ($applicants as $applicant) {
            ApplicationApplicant::create($applicant);
        }
    }
    
    protected function createResolutions(Application $application, array $data)
    {
        // First resolution
        if (!empty($data['s_1st_resolution'])) {
            $resolutionId = getResolutionId($data['s_1st_resolution']);
            if ($resolutionId) {
                $application->resolutions()->attach($resolutionId, [
                    'resolution_details' => $this->compileReasons($data, '1st'),
                    'resolution_date' => $this->parseDate($data['s_1st_date'] ?? null),
                    'sequence' => 1
                ]);
            }
        }
        
        // Second resolution
        if (!empty($data['s_2nd_resolution'])) {
            $resolutionId = getResolutionId($data['s_2nd_resolution']);
            if ($resolutionId) {
                $application->resolutions()->attach($resolutionId, [
                    'resolution_details' => '',
                    'resolution_date' => $this->parseDate($data['s_2nd_date'] ?? null),
                    'sequence' => 2
                ]);
            }
        }
    }
    
    // Helper methods specific to this class
    protected function hasApplicantData(array $data, string $suffix): bool
    {
        return !empty($data["s_name_title{$suffix}"]) || 
               !empty($data["s_name_first{$suffix}"]) || 
               !empty($data["s_name_family{$suffix}"]);
    }
    
    protected function compileReasons(array $data, string $prefix): string
    {
        $reasons = [];
        
        for ($i = 1; $i <= 10; $i++) {
            $column = "s_{$prefix}_reason0{$i}";
            if (!empty($data[$column])) {
                $reasons[] = $data[$column];
            }
        }
        
        return implode('<br/>', $reasons);
    }
    
    protected function generateApplicationId(array $data): string
    {
        return implode('/', array_filter([
            $data['s_reg_type'] ?? null,
            $data['s_reg_org'] ?? null,
            $data['s_reg_area'] ?? null,
            $data['s_reg_num'] ?? null
        ]));
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


    public function batchSize(): int
    {
        return 200;
    }

    public function chunkSize(): int
    {
        return 200;
    }
}