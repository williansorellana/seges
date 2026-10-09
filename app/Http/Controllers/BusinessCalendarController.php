<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessCalendarController extends Controller
{
    public function index()
    {
        return view('renditions.calendar', ['holidays' => DB::table('business_holidays')->orderBy('date')->get(),
            'years' => DB::table('business_calendar_years')->orderBy('year')->pluck('year')]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['date' => 'required|date_format:Y-m-d', 'name' => 'required|string|max:255']);
        DB::table('business_holidays')->updateOrInsert(['date' => $data['date']],
            ['name' => $data['name'], 'created_at' => now(), 'updated_at' => now()]);

        return back()->with('success', 'Feriado guardado. Los plazos ya comunicados se conservan.');
    }

    public function destroy(int $holiday)
    {
        DB::table('business_holidays')->where('id', $holiday)->delete();

        return back()->with('success', 'Feriado eliminado del calendario. Los plazos existentes se conservan.');
    }

    public function confirmYear(Request $request)
    {
        $data = $request->validate(['year' => 'required|integer|min:2026|max:2100', 'reviewed' => 'accepted']);
        DB::table('business_calendar_years')->updateOrInsert(['year' => $data['year']], ['created_at' => now(), 'updated_at' => now()]);

        return back()->with('success', 'Calendario anual habilitado para calcular nuevos plazos.');
    }
}
