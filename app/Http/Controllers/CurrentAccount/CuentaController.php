<?php

namespace App\Http\Controllers\CurrentAccount;

use App\Http\Controllers\Controller;
use App\Models\CurrentAccount\Cuenta;
use Encrypt;
use Illuminate\Http\Request;

class CuentaController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        $cuentas = Cuenta::all();
        $cuentas = Encrypt::encryptObject($cuentas, 'id');


        return response()->json([
              "status"=>"success",
              "message"=>"Registros obtenidos correctamente.",
              'cuentas'=>$cuentas
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
        Cuenta::insert($request->all());

        return response()->json([
              "status"=>"success",
              "message"=>"Registro creado correctamente."
          ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cuenta  $department
     * @return \Illuminate\Http\Response
     */
    public function show(Cuenta $department)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cuenta  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = Encrypt::decryptArray($request->all(), 'id');

        Cuenta::where('id', $data)->update($data);
        return response()->json([
              "status"=>"success",
              "message"=>"Registro modificado correctamente."
          ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cuenta  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = Encrypt::decryptValue($id);

        Cuenta::where('id', $id)->delete();
        return response()->json([
              "status"=>"success",
              "message"=>"Registro eliminado correctamente."
        ]);
    }
}
