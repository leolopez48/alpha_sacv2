<?php

namespace App\Http\Controllers\Checks;

use App\Models\Checks\Bank;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banks = Bank::all();

        return response()->json(['message' => 'success', 'banks'=>$banks]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Bank::insert($request->all());
        return response()->json(['message'=>'success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bank  $banks
     * @return \Illuminate\Http\Response
     */
    public function show(Bank $bank)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bank  $banks
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bank $bank)
    {
        Bank::where('id', $bank->id)->update($request->all());
        return response()->json(["message"=>"success"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bank  $banks
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bank $bank)
    {
        Bank::where('id', $bank->id)->delete();
        return response()->json(["message"=>"success"]);
    }
}
