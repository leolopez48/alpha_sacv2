<?php

namespace App\Http\Controllers\Checks;

use App\Models\Checks\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suppliers = Supplier::paginate(15);

        return response()->json(['message' => 'success', 'suppliers'=>$suppliers]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Supplier::insert($request->except(['grade_name']));
        return response()->json(['message'=>'success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier  $suppliers
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier  $suppliers
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        Supplier::where('id', $supplier->id)->update($request->except(['grade_name']));
        return response()->json(["message"=>"success"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier  $suppliers
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        Supplier::where('id', $supplier->id)->delete();
        return response()->json(["message"=>"success"]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function supplierByDUI(Request $request)
    {
        $supplier = Supplier::where(['dui'=>$request->dui])->first();

        return response()->json(['message' => 'success', 'Supplier'=>$supplier]);
    }
}
