<?php

namespace App\Http\Controllers\Checks;

use App\Models\Checks\Bank;
use Illuminate\Http\Request;
use PDF;
use App\Models\Checks\Check;
use App\Models\Checks\Document;
use Jenssegers\Date\Date;
use Luecano\NumeroALetras\NumeroALetras;
use App\Http\Controllers\Controller;

class PDFController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function generateCheck($id)
    {
        Date::setLocale('es');

        $check = Check::select('*', 'checks.id as id')
        ->join('documents as do', 'checks.type_fund_id', '=', 'do.id')
        ->join('banks as ba', 'do.bank_id', '=', 'ba.id')
        ->join('suppliers as su', 'checks.supplier_id', '=', 'su.id')
        ->where('checks.id', $id)
        ->first();

        $formatter = new NumeroALetras();

        $day = ucfirst(mb_strtolower($formatter->toWords(intval(date('d', strtotime($check->date)))), 'UTF-8'));
        $newDate = new Date($check->date);
        $month = $newDate->format('F');
        $year = mb_strtolower($formatter->toWords(intval($newDate->format('Y'))), 'UTF-8');
        $check->dateLetters = "$day de $month del $year";

        $day = ucfirst($newDate->format('l'));
        $numberDay = date('d', strtotime($check->date));
        $month = $newDate->format('F');
        $year = $newDate->format('Y');
        $check->dateNumberLetters = ucfirst("$day $numberDay de $month de $year");

        $pdf = PDF::loadView('checks.PDF.report', compact('check'));

        return $pdf->stream('report-'.now().'.pdf');
    }

    public function generateSummary($id)
    {
        $bank = Document::where(['id'=>$id])
        ->first();

        $debe = Check::select('*', 'checks.id as id')
        ->join('documents as do', 'checks.type_fund_id', '=', 'do.id')
        ->join('banks as ba', 'do.bank_id', '=', 'ba.id')
        ->join('suppliers as su', 'checks.supplier_id', '=', 'su.id')
        ->where(['checks.movement'=>"Cargar", 'do.id'=>$id])
        ->get();

        $haber = Check::select('*', 'checks.id as id')
        ->join('documents as do', 'checks.type_fund_id', '=', 'do.id')
        ->join('banks as ba', 'do.bank_id', '=', 'ba.id')
        ->join('suppliers as su', 'checks.supplier_id', '=', 'su.id')
        ->where(['checks.movement'=> "Abonar", 'do.id'=>$id])
        ->get();

        $totalDebe = $bank->initial_amount;
        foreach ($debe as $key => $value) {
            $totalDebe += $value->net_total;
        }

        $totalHaber = 0;
        foreach ($haber as $key => $value) {
            $totalHaber += $value->net_total;
        }

        $totalGeneralDebe = 0;
        $totalGeneralHaber = 0;
        if ($totalDebe > $totalHaber) {
            $totalGeneralDebe = number_format($totalDebe - $totalHaber, 2);
        } else {
            $totalGeneralHaber = number_format($totalHaber - $totalDebe, 2);
        }

        $pdf = PDF::loadView('checks.PDF.summary', compact('bank', 'debe', 'haber', 'totalDebe', 'totalHaber', 'totalGeneralDebe', 'totalGeneralHaber'));

        return $pdf->stream('report-'.now().'.pdf');
    }
}
