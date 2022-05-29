<?php

namespace App\Http\Controllers\Checks;

use App\Models\Checks\Bank;
use App\Models\Checks\Document;
use App\Models\Checks\Supplier;
use Illuminate\Http\Request;
use App\Models\NumeroEnLetras;
use App\Http\Controllers\Controller;
use App\Models\Checks\Check;

class CheckController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $checks = Check::select('*', 'checks.id as id')
        ->join('documents as do', 'checks.type_fund_id', '=', 'do.id')
        ->join('suppliers as su', 'checks.supplier_id', '=', 'su.id')
        ->orderBy('checks.id', 'desc')
        ->paginate(15);

        return response()->json(['message' => 'success', 'checks'=>$checks]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $lastModel = Check::latest()->first();

        $check = new Check;
        $check->check_number = date('Ymd').(($lastModel == null)?1:($lastModel->id+1));
        $check->concept = $request->concept;
        $check->date = $request->date;
        $check->net_total = $request->net_total;
        $check->amount = $request->net_total + ($request->net_total * $request->iva) + ($request->net_total * $request->rent);
        $check->movement = $request->movement;
        $check->total_letters = NumeroEnLetras::convertir($check->amount, ($check->amount == 1)?"DÓLAR":"DÓLARES", false, 'CENTAVOS');

        $check->number_project = $request->number_project;
        $check->supplier_id = Supplier::where('name_supplier', $request->name_supplier)->first()->id;
        $check->type_fund_id = Document::where('document_name', $request->document_name)->first()->id;
        $check->iva = $request->iva;
        $check->rent = $request->rent;
        $check->document = $request->document;

        $check->save();

        // Check::insert($request->except(['grade_name']));
        return response()->json(['message'=>'success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Check  $checks
     * @return \Illuminate\Http\Response
     */
    public function show(Check $check)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Check  $checks
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Check $check)
    {
        $check->concept = $request->concept;
        $check->date = $request->date;
        $check->net_total = $request->net_total;
        $check->amount =  $request->net_total + ($request->net_total * $request->iva) + ($request->net_total * $request->rent);
        $check->movement = $request->movement;
        // dd($check->amount);
        $check->total_letters = NumeroEnLetras::convertir($check->amount, ($check->amount >= 1 || $check->amount <= 1.99)?"DÓLAR":"DÓLARES", false, 'CENTAVOS');
        $check->number_project = $request->number_project;
        $check->type_fund_id = Document::where('document_name', $request->document_name)->first()->id;
        $check->iva = $request->iva;
        $check->rent = $request->rent;

        $check->document = $request->document;

        $check->save();

        return response()->json(["message"=>"success"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Check  $checks
     * @return \Illuminate\Http\Response
     */
    public function destroy(Check $check)
    {
        Check::where('id', $check->id)->delete();
        return response()->json(["message"=>"success"]);
    }

    public function summary()
    {
        $debe = Check::select('*', 'checks.id as id')
        // ->join('banks as ba', 'checks.bank_id', '=', 'ba.id')
        ->join('documents as do', 'checks.type_fund_id', '=', 'do.id')
        ->join('suppliers as su', 'checks.supplier_id', '=', 'su.id')
        ->where('checks.movement', "Cargar")
        ->get();

        $haber = Check::select('*', 'checks.id as id')
        // ->join('banks as ba', 'checks.bank_id', '=', 'ba.id')
        ->join('documents as do', 'checks.type_fund_id', '=', 'do.id')
        ->join('suppliers as su', 'checks.supplier_id', '=', 'su.id')
        ->where('checks.movement', "Abonar")
        ->get();

        $totalDebe = 0;
        foreach ($debe as $key => $value) {
            $totalDebe += $value->amount;
        }

        $totalHaber = 0;
        foreach ($haber as $key => $value) {
            $totalHaber += $value->amount;
        }

        return response()->json([
            'message' => 'success',
            'debe'=>$debe,
            'haber'=>$haber,
            'totalHaber'=>$totalHaber,
            'totalDebe'=>$totalDebe
        ]);
    }
}
