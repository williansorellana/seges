<?php

namespace App\Http\Controllers;

use App\Services\RenditionReportService;
use App\Services\ReportWorkbookWriter;
use Illuminate\Http\Request;

class RenditionReportController extends Controller
{
    public function xlsx(Request $request)
    {
        $report = app(RenditionReportService::class);
        $data = $report->data($request);
        $body = app(ReportWorkbookWriter::class)->make($report->columns(), $data['rows'], $data['totalFunds'], $data['totalExpenses']);

        return response($body, 200, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="Reporte_Rendiciones_'.now()->format('Ymd_His').'.xlsx"']);
    }

    public function pdf(Request $request)
    {
        $data = app(RenditionReportService::class)->data($request);

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('renditions.report-pdf', $data)->setPaper('a4', 'landscape')
            ->download('Reporte_Rendiciones_'.now()->format('Ymd_His').'.pdf');
    }
}
