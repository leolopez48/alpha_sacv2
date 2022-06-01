<?php

namespace App\Http\Controllers\CurrentAccount;

use App\Http\Controllers\Controller;
use App\Models\CurrentAccount\Cuenta;
use App\Models\CurrentAccount\DetalleRecibo;
use App\Models\CurrentAccount\Recibo;
use Encrypt;
use Illuminate\Http\Request;
use Luecano\NumeroALetras\NumeroALetras;
use PhpOffice\PhpWord\TemplateProcessor;

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
        $data = [
            'fecha_registro' => date('Y-m-d h:m', strtotime($request->fecha_registro)),
            'dui' => $request->dui,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'direccion' => $request->direccion,
            'concepto' => $request->concepto,
            'total' => number_format($request->total, 2),
        ];
        $recibo = Recibo::create($data);

        //insert detail_receipts
        $recibo_id = $recibo->id;
        $details = $request->details_receipts;
        foreach ($details as $detail) {
            $data = [];

            $fiesta = false;
            try {
                // If can be exploded will be Fiesta detail
                $nombreCuentaExploded = explode(" - ", $detail['nombre_cuenta']);
                if ($nombreCuentaExploded[0] == 'FIESTA') {
                    $fiesta = true;
                }
            } catch (\Throwable $th) {
                //throw $th;
            }

            if ($fiesta) {
                $data = [
                        'recibo_id' => $recibo_id,
                        'cantidad' => $detail['cantidad'],
                        'subtotal' => number_format($detail['subtotal'], 2),
                    ];
                DetalleRecibo::create($data);
            } else {
                $cuenta = Cuenta::where('nombre_cuenta', $detail['nombre_cuenta'])->first();
                $cuenta_id = $cuenta->id;

                $data = [
                    'recibo_id' => $recibo_id,
                    'cuenta_id' => $cuenta_id,
                    'cantidad' => $detail['cantidad'],
                    'subtotal' => number_format($detail['subtotal'], 2),
                ];
                DetalleRecibo::create($data);
            }
        }

        return response()->json([
            "status"=>"success",
            "message"=>"Registro creado correctamente.",
            'reciboId'=>$recibo_id

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

    public function downloadReceipt($id)
    {
        $receipts = Recibo::where("recibo.id", $id)->join('detalles_recibo', 'recibo.id', '=', 'detalles_recibo.recibo_id')
        // ->join('cuenta', 'detalles_recibo.cuenta_id', '=', 'cuenta.id')
        ->select('recibo.*', 'detalles_recibo.*')
        ->get();

        // dd($receipts);

        try {
            $formatter = new NumeroALetras();

            $template = new TemplateProcessor(storage_path('reports/recibo_ingreso.docx'));

            $j = 0;
            for ($i = 0; $i < 13; $i++) {
                if ($i == 0) {
                    $template->setValue('day', date('d', strtotime($receipts[$i]->fecha_registro)));
                    $template->setValue('month', date('m', strtotime($receipts[$i]->fecha_registro)));
                    $template->setValue('year', date('Y', strtotime($receipts[$i]->fecha_registro)));
                    $template->setValue('name', $receipts[$i]->nombres);
                    $template->setValue('surname', $receipts[$i]->apellidos);
                    $template->setValue('amount_letters', $formatter->toInvoice($receipts[$i]->total, 2, 'dolares'));
                    $template->setValue('concepto', $receipts[$i]->concepto);
                    $template->setValue('total', "$".number_format($receipts[$i]->total, 2));
                }

                if ($i < 13) {
                    try {
                        $template->setValue('account_code'.($i+1), "");
                        if (!empty($receipts[$i]->cuenta_id)) {
                            $account = Cuenta::where('id', $receipts[$i]->cuenta_id)->first();
                            $template->setValue('account_name'.($i+1), $account->nombre_cuenta);
                            $j++;
                        } else {
                            if ($j < count($receipts)) {
                                $template->setValue('account_name'.($i+1), "FIESTA");
                                $j++;
                            }
                        }
                        $template->setValue('subtotal'.($i+1), $receipts[$i]->subtotal);
                    } catch (\Throwable $th) {
                        $template->setValue('account_code'.($i+1), "");
                        $template->setValue('account_name'.($i+1), "");
                        $template->setValue('subtotal'.($i+1), "");
                    }
                }
            }

            $tempFile = tempnam(sys_get_temp_dir(), 'PHPWord');
            $template->saveAs($tempFile);

            $headers = [
                'Content-Type: application/octet-stream',
            ];

            return response()->download($tempFile, 'recibo.docx', $headers)->deleteFileAfterSend(true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
