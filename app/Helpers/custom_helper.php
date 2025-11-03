<?php
// app/Helpers/ImportHelpers.php

use App\Models\{
    Resolution,
    ApplicantTitle,
    District,
    Alias,
    DevelopmentArea,
    LandUs,
    RegistrationType,
    RegistrationArea,
    RegistrationOrganization,
    ApplicantType,
    ChangeUseStage
};

function getResolutionId($res)
{
    $resolution = Resolution::where('resolution_type', $res)->first();
    return $resolution ? $resolution->id : null;
}

function change_of_land_use_stage(?int $id)
{

    $name = ChangeUseStage::find($id);
    if($name && $name->exists())
    {
        return $name->stage_name;
    } else
    {
        return '';
    }
}

function getApplicantTitleId($title)
{
    $appTitle = ApplicantTitle::where('title', $title)->first();
    return $appTitle ? $appTitle->id : 1; // Default title ID
}

function getDistrictId($dst)
{
    if (!$dst) return 6;

    $input = trim($dst);

    // Try exact or partial match on the name
    $district = District::where('name', 'LIKE', '%' . $input . '%')->first();

    if (!$district) {
        // Try alias match
        $district = Alias::where('alias', 'LIKE', '%' . $input . '%')
                         ->first()?->district;
    }

    return $district?->id;
}

function getDevelopmentAreaId($area)
{
    $dev = DevelopmentArea::where('name', $area)->first();
    return $dev ? $dev->id : null;
}

function getLandUseId($use)
{
    $landUse = LandUs::where('name', $use)->first();
    return $landUse ? $landUse->id : null;
}

function getRegistrationTypeId($appClass)
{
    $class = RegistrationType::where('reg_key', $appClass)->first();
    return $class ? $class->id : null;
}

function getRegistrationAreaId($reg)
{
    $area = RegistrationArea::where('area_key', $reg)->first();
    return $area ? $area->id : null;
}

function getOrganizationId($org)
{
    $organization = RegistrationOrganization::where('name', $org)->first();
    return $organization ? $organization->id : 1; // Default organization ID
}

function getApplicantTypeId($type)
{
    $applicantType = ApplicantType::where('name', $type)->first();
    return $applicantType ? $applicantType->id : 1;
}
