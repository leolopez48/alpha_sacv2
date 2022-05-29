<?php

namespace App\Http\Controllers\CurrentAccount;

use App\Http\Controllers\Controller;
use App\Models\CurrentAccount\Recibo;
use Encrypt;
use Illuminate\Http\Request;

class ReciboController extends Controller
{
    /**
      * Display a listing of the resource.
      *
      * @return \Illuminate\Http\Response
      */
    public function index()
    {
        $recibos = Recibo::all();
        $recibos = Encrypt::encryptObject($recibos, 'id');

        return response()->json([
            "status"=>"success",
            "message"=>"Registros obtenidos correctamente.",
            'recibos'=>$recibos
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Recibo::insert($request->all());

        return response()->json([
            "status"=>"success",
            "message"=>"Registro creado correctamente."
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Recibo  $department
     * @return \Illuminate\Http\Response
     */
    public function show(Recibo $department)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Recibo  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = Encrypt::decryptArray($request->all(), 'id');

        Recibo::where('id', $data)->update($data);
        return response()->json([
            "status"=>"success",
            "message"=>"Registro modificado correctamente."
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Recibo  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = Encrypt::decryptValue($id);

        Recibo::where('id', $id)->delete();
        return response()->json([
            "status"=>"success",
            "message"=>"Registro eliminado correctamente."
        ]);
    }
}
