<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\SimpleType\Jc;
use App\Models\ExecutiveSignature;
use Dompdf\Dompdf;
use Dompdf\Options;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\SimpleType\JcTable;

class PrintSchedulesController extends Controller
{
   public function index(Request $request)
{
    $selectedApplications = json_decode($request->input('selectedApplications'), true);
    $meetingDate = $request->input('meetingDate');

    $applications = Application::with([
        'development_area',
        'change_of_land_use',
        'registration_organization',
        'district',
        'registration_area',
        'subArea',
        'landUse',
        'applicationClassification',
        'applicationApplicants.applicant_title',
        'applicationApplicants.applicant_type',
        'applicationResolutions.resolution',
        'applicationSubmissions.application_classification',
    ])->whereIn('id', $selectedApplications)->get();

    $estimatedRowsPerPage = 8;
    $totalPages = max(1, ceil(count($applications) / $estimatedRowsPerPage));

    $data = [
        'applications' => $applications,
        'meetingDate' => $meetingDate,
        'totalPages' => $totalPages
    ];

    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $options->set('defaultFont','times');
    $options->set('defaultPaperSize','a4');
    $options->set('chroot', public_path());

    $pdf = new Dompdf($options);
    $pdf->setPaper('A4','landscape');

    $data['pdf'] = $pdf;
    $font = $pdf->getFontMetrics()->get_font('times', 'normal');
    $size = 11;
    $data['font'] = $font;
    $data['size'] = $size;
    $data['y'] = $pdf->getCanvas()->get_height() - 20;
    $data['x'] = $pdf->getCanvas()->get_width() - 15 - $pdf->getFontMetrics()->get_text_width('1/1', $font, $size);

    $html = view('pdf.planning_applications', $data)->render(); // 👈 render the blade properly

    $pdf->loadHtml($html);
    $pdf->render();

    // Add page numbers
    $canvas = $pdf->getCanvas();
    $y = $canvas->get_height() - 45;
    $x = $canvas->get_width() - 430 - $pdf->getFontMetrics()->get_text_width('1/1', $font, $size);
    $canvas->page_text($x, $y, substr($applications->first()->applicationClassification->reg_key,0,3).': {PAGE_NUM}/{PAGE_COUNT}', $font, $size);

    // ✅ Correct way to return in Laravel
    return response($pdf->output(), 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename="planning_applications.pdf"');
}


    public function print_notices(Request $request)
{
    $selectedApplications = json_decode($request->input('selectedApplications'), true);

    $applications = Application::with([
        'development_area',
        'change_of_land_use',
        'registration_organization',
        'district',
        'registration_area',
        'subArea',
        'landUse',
        'applicationClassification',
        'applicationApplicants.applicant_title',
        'applicationApplicants.applicant_type',
        'applicationResolutions.resolution',
        'applicationSubmissions.application_classification',
    ])->whereIn('id', $selectedApplications)->get();

    $phpWord = new PhpWord();

    $fontStyleName = 'rStyle';
    $phpWord->addFontStyle($fontStyleName, ['bold' => false, 'italic' => false, 'size' => 15, 'allCaps' => true]);
    $paragraphStyleName = 'pStyle';
    $phpWord->addParagraphStyle($paragraphStyleName, ['alignment' => Jc::CENTER, 'spaceAfter' => 100]);

    $phpWord->addTitleStyle(1, ['bold' => false, 'size' => 13], ['spaceAfter' => 240, 'alignment' => Jc::CENTER]);

    //$section = $phpWord->addSection();

    foreach ($applications as $index=> $application) {
         // Create a new section for each application
    $section = $phpWord->addSection();

    // Add header to THIS section with increased height
    $header = $section->addHeader();

    $appNumber = $application->application_id ?? 'D/LKPPA/KAF/18278';

    // Add some spacing at the top
    $header->addTextBreak(1);

    // Create table with exact layout
   $table = $header->addTable([
    'width' => 9000, // around 6 inches (~15cm)
    'unit' => 'dxa', // twips (1/20th of a point)
    'borderSize' => 0,
    'borderColor' => 'FFFFFF',
    'alignment' => JcTable::CENTER,
]);

    $table->addRow(700); // Increase row height

    // Left cell - Form information
    $cell1 = $table->addCell(6000, ['valign' => 'top']);
    $cell1->addText('Form U and RP 8 (Rev)', ['size' => 8], ['alignment' => Jc::START, 'spaceAfter' => 0]);
    $cell1->addText('Stocked by Govt Printers', ['size' => 8], ['alignment' => Jc::START, 'spaceAfter' => 0]);
    $cell1->addText('20m W346 973', ['size' => 8], ['alignment' => Jc::START, 'spaceAfter' => 0]);

    // Right cell - Application number section (side by side)
    $cell2 = $table->addCell(4000, [
    'valign' => 'center',
    'alignment' => Jc::START,
]);

    // Create inner table for side-by-side layout
  $innerTable = $cell2->addTable([
    'width' => 90 * 50,
    'unit' => 'pct',
    'alignment' => JcTable::CENTER,
]);

$innerTable->addRow();

// Left part: "Registered Number of Application" text (NO BORDER)
$textCell = $innerTable->addCell(1000, [
    'valign' => 'center',
    'borderSize' => 0,
    'borderColor' => 'FFFFFF',
    'marginRight' => 90
]);
$textCell->addText('Registered', ['size' => 8], ['alignment' => Jc::START, 'spaceAfter' => 0]);
$textCell->addText('Number of', ['size' => 8], ['alignment' => Jc::START, 'spaceAfter' => 0]);
$textCell->addText('Application', ['size' => 8], ['alignment' => Jc::START, 'spaceAfter' => 0]);

// Right part: Boxed application number (WITH BORDER)
$boxCell = $innerTable->addCell(2000, [
    'valign' => 'center',
    'borderSize' => 6,
    'borderColor' => '000000',
    'bgColor' => 'FFFFFF',
    'marginRight' => 300
]);
$boxCell->addText(
    $appNumber,
    ['bold' => true, 'size' => 10,'padding'=> 10],
    ['alignment' => Jc::CENTER, 'spaceAfter' => 100, 'spaceBefore' => 100]
);

    // Add spacing after header
    $header->addTextBreak(1);


        $section->addTitle('THE URBAN AND REGIONAL PLANNING ACT NO. 3 OF 2015');
        $section->addText('NOTIFICATION OF APPROVAL FOR PLANNING PERMISSION', $fontStyleName, $paragraphStyleName);
        $section->addText($application->sub_plot_number . ' ' . $application->district->name . ' ' . $application->development_sub_area);

        $day = date('d', strtotime($application->application_date));
        $month = date('F', strtotime($application->application_date));
        $year = date('Y', strtotime($application->application_date));

        $section->addText("Your application numbered as above, submitted on $day $month $year for permission");
        $section->addText(optional($application->applicationSubmissions->first())->application_text);

        foreach ($application->applicationResolutions as $resolutions) {
            $sitting_date = $resolutions->resolution_date;
            $sitting_day = date('d', strtotime($sitting_date));
            $sitting_month = date('F', strtotime($sitting_date));
            $sitting_year = date('Y', strtotime($sitting_date));
            $resolutionType = $resolutions->resolution->resolution_type;

            $text = strtolower($resolutionType) === 'approval' ? '' : ':-';

            $textApproval = "{$application->sub_plot_number} {$application->district->name} {$application->development_sub_area} has been $resolutionType on $sitting_day $sitting_month, $sitting_year by LUSAKA PROVINCE PLANNING AUTHORITY$text";
            $section->addText($textApproval);

            if (strtolower($resolutionType) !== 'approval' && $resolutions->resolution_details) {
                $reasons = '';
                $x = 1;
                foreach (explode("\n", preg_replace('/<br\s*\/?>/i', "\n", $resolutions->resolution_details)) as $condition) {
                    $reasons .= "$x. $condition\n";
                    $x++;
                }
                $section->addText($reasons);
            }

            $section->addTextBreak();
            $section->addText('CC: Commissioner of Lands, P O Box 30069, Lusaka');
            $section->addText('CC: Council Secretary ' . $application->district->name);
            $section->addText('Date: ' . "$sitting_day $sitting_month $sitting_year");

            $signaturePath = ExecutiveSignature::first()?->signature;
            if ($signaturePath && file_exists(public_path('storage/' . $signaturePath))) {
                $section->addImage(public_path('storage/' . $signaturePath), ['width' => 50, 'height' => 50,'alignment' => Jc::CENTER]);
            }

            $section->addText('Signed...........................', null, ['alignment' => Jc::CENTER]);
            $section->addText('Richard C. Mukozomba', null, ['alignment' => Jc::CENTER]);
            $section->addText('EXECUTIVE OFFICER', null, ['alignment' => Jc::CENTER]);

            $section->addText('NOTES:', ['bold' => true]);



                $section->addText('1. In the case of subdivision approvals where the records of the sub divisional survey required by section ten (i) and twenty-one of the Land Survey Act are not lodged with the Surveyor-General within the period stated in the approval, such approval shall be deemed to be cancelled',['size'=>9]);
                $section->addText('2. If the applicant is aggrieved by the decision of the planning authority to refuse permission for the proposed development or subdivision or to grant permission subject to conditions, he may, by notice served within twenty-eight days of the receipt of this notification or such longer period as the Town and Country Planning Tribunal in writing may agree, appeal to the Tribunal in terms of section twenty-nine of the Act.',['size'=>9]);
                $section->addText('3. The Tribunal shall not be required to entertain an appeal under the aforesaid section twenty-nine in respect of the determination of an application for permission to develop or subdivide land if it appears to the President of the Tribunal that permission or approval for that development or subdivision could not have been granted otherwise than subject to the conditions imposed having regard to the provisions of section twenty-five of the Act and of the appropriate development or subdivision order and to any directions given under such order.',['size'=>9]);
                $section->addText('4. In certain circumstances a claim may be made against the Minister or planning authority for compensation or acquisition of land affected where permission or approval is refused or granted subject to conditions. The circumstances in which such compensation is payable or acquisition of land may be required are set out in Part VI of the Act.',['size'=>9]);
            // Add more notes if needed
        }
        // Add a page break unless it's the last application
    if ($index < count($applications) - 1) {
        $section->addPageBreak();
    }
    }

    // Save file
    $filename = 'notice_' . time() . '.docx';
    $relativePath = 'notices/word/' . $filename;
    $fullPath = storage_path('app/public/' . $relativePath);

    // Ensure directory exists
    if (!file_exists(dirname($fullPath))) {
        mkdir(dirname($fullPath), 0755, true);
    }

    $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save($fullPath);

    return view('notices.document', ['output' => $relativePath]);
}
public function printSchedule(Request $request)
{
    $selectedApplications = json_decode($request->input('selectedApplications'), true);
    $meetingDate = $request->input('meetingDate');

    $applications = Application::with([
        'development_area',
        'change_of_land_use',
        'registration_organization',
        'district',
        'registration_area',
        'subArea',
        'landUse',
        'applicationClassification',
        'applicationApplicants.applicant_title',
        'applicationApplicants.applicant_type',
        'applicationResolutions.resolution',
        'applicationSubmissions.application_classification',
    ])->whereIn('id', $selectedApplications)->get();

    $data = [
        'meetingDate' => $meetingDate,
        'applications' => $applications
    ];

    $html = view('pdf.mpdf-supported', $data)->render();

    // Optional: Custom font config (if needed)
    $defaultConfig = (new ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];
    $defaultFontConfig = (new FontVariables())->getDefaults();
    $fontData = $defaultFontConfig['fontdata'];

    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4-L', // A4 landscape
        'margin_top' => 5,
        'margin_bottom' => 25, // Increased bottom margin for footer
        'margin_left' => 10,
        'margin_right' => 10,
        'fontDir' => array_merge($fontDirs, [public_path('fonts')]),
        'fontdata' => $fontData + [
            'times' => [
                'R' => 'times.ttf',
                'B' => 'timesbd.ttf',
                'I' => 'timesi.ttf',
                'BI' => 'timesbi.ttf',
            ],
        ],
        'default_font' => 'times'
    ]);

    // Set footer FIRST
    $mpdf->SetHTMLFooter('
        <div style="text-align: center; font-size: 14px; font-style: times;">
            '.$applications->first()->applicationClassification->reg_key.': {PAGENO}/{nbpg}
        </div>
    ');

    // Add first page explicitly to apply footer from the beginning
   $mpdf->AddPage();

    // Write HTML content with mode 2 to continue on the same page
    $mpdf->WriteHTML($html);

    return response($mpdf->Output('', 'S'), 200)
        ->header('Content-Type', 'application/pdf');
}
}
