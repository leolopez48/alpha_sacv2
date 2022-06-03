<?php

namespace App\Http\Controllers\CurrentAccount;

use App\Http\Controllers\Controller;
use App\Models\CurrentAccount\Cuenta;
use App\Models\CurrentAccount\DetalleRecibo;
use App\Models\CurrentAccount\Recibo;
use Encrypt;
use Illuminate\Http\Request;
use Luecano\NumeroALetras\NumeroALetras;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;

class ReciboController extends Controller
{
    /**
      * Display a listing of the resource.
      *
      * @return \Illuminate\Http\Response
      */
    public function index(Request $request)
    {
        $skip = $request->skip;
        $limit = $request->take - $skip; // the limit
        $recibos = [];

        if (isset($request->search)) {
            $search = '%'.$request->search.'%';

            $recibos = Recibo::skip($skip)->take($limit)
                ->orderBy('id', 'desc')
                ->where('nombres', 'like', $search)
                ->orWhere('apellidos', 'like', $search)
                ->orWhere('dui', 'like', $search)
                ->get();
        } else {
            $recibos = Recibo::skip($skip)->take($limit)
                ->orderBy('id', 'desc')
                ->get();
        }

        foreach ($recibos as $recibo) {
            $recibo->detail_receipts = DetalleRecibo::where('recibo_id', $recibo->id)->get();

            foreach ($recibo->detail_receipts as $detail) {
                $account = Cuenta::where('id', $detail->cuenta_id)->first();

                if (!empty($account)) {
                    $detail->nombre_cuenta = $account->nombre_cuenta;
                } else {
                    $detail->nombre_cuenta = 'FIESTA';
                }
            }
        }
        $recibos = Encrypt::encryptObject($recibos, 'id');

        $total = Recibo::count();

        return response()->json([
            "status"=>"success",
            "message"=>"Registros obtenidos correctamente.",
            'recibos'=>$recibos,
            'total'=>$total
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

        $recibo_id = $recibo->id;
        $details = $request->detail_receipts;
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
        $dataRequest = $request->except('detail_receipts');
        $dataRequest = Encrypt::decryptArray($dataRequest, 'id');

        $details = $request->detail_receipts;

        DetalleRecibo::where('recibo_id', $dataRequest['id'])->delete();

        foreach ($details as $detail) {
            // dd($data['id']);
            $data = [];

            if ($detail['nombre_cuenta'] == 'FIESTA') {
                $data = [
                    'recibo_id' => $dataRequest['id'],
                    'cantidad' => $detail['cantidad'],
                    'subtotal' => number_format($detail['subtotal'], 2),
                ];
                DetalleRecibo::create($data);
            } else {
                $cuenta = Cuenta::where('nombre_cuenta', $detail['nombre_cuenta'])->first();
                $cuenta_id = $cuenta->id;

                $data = [
                    'recibo_id' => $dataRequest['id'],
                    'cuenta_id' => $cuenta_id,
                    'cantidad' => $detail['cantidad'],
                    'subtotal' => number_format($detail['subtotal'], 2),
                ];
                DetalleRecibo::create($data);
            }
        }

        Recibo::where('id', $dataRequest)->update($dataRequest);
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
        $id = Encrypt::decryptValue($id);

        $receipts = Recibo::where("recibo.id", $id)->join('detalles_recibo', 'recibo.id', '=', 'detalles_recibo.recibo_id')
        ->select('recibo.*', 'detalles_recibo.*')
        ->where('detalles_recibo.deleted_at', '=', null)
        ->get();

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
                        if (!empty($receipts[$i]->cuenta_id)) {
                            $account = Cuenta::where('id', $receipts[$i]->cuenta_id)->first();
                            $template->setValue('account_name'.($i+1), $account->nombre_cuenta);
                            $j++;
                            $template->setValue('codigo'.($i+1), $account->codigo);
                        } else {
                            if ($j < count($receipts)) {
                                $template->setValue('account_name'.($i+1), "FIESTA");
                                $template->setValue('codigo'.($i+1), "1411");
                                $j++;
                            }
                        }
                        $template->setValue('subtotal'.($i+1), $receipts[$i]->subtotal);
                    } catch (\Throwable $th) {
                        $template->setValue('codigo'.($i+1), "");
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

            return response()->download($tempFile, "Recibo de ingreso $id.docx", $headers)->deleteFileAfterSend(true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //Use phpword to create a table
    public function downloadReportByDate(Request $request)
    {
        $dateStart = $request->dateStart;
        $dateEnd = $request->dateEnd;

        $recibos = Recibo::where('fecha_registro', '>=', $dateStart)
        ->where('fecha_registro', '<=', $dateEnd)
        ->get();

        //Create table
        $document_with_table = new PhpWord();
        $section = $document_with_table->addSection();
        $table = $section->addTable('tableStyle');

        $headers = [
            'CODIGO',
            'IMPUESTO',
            'MONTO',
        ];
        $table->addRow();
        for ($i = 0; $i < count($headers); $i++) {
            $table->addCell(4000, [
                'borderSize' => 6,
                'bgColor' => '#e0e0e0',
                ])
            ->addTextRun([
                'alignment' => 'center',
                'spaceBefore' => 30,
                'spaceAfter' => 30,
            ])
            ->addText($headers[$i], ['bold' => true]);
        }

        $accounts = Cuenta::all();
        $total = 0;
        // dd($accounts[0]);
        for ($r = 0; $r < count($accounts); $r++) {
            $table->addRow();
            for ($c = 0; $c < count($headers); $c++) {
                $width = 1200;
                $text = "";
                switch ($c) {
                    case 0:
                        $width = 4000;
                        $text = $accounts[$r]->codigo;
                    break;
                    case 1:
                        $width = 8000;
                        $text = $accounts[$r]->nombre_cuenta;
                        break;
                    case 2:
                        $width = 4000;
                        $text .= "$ ";
                        $subtotal = DetalleRecibo::where('cuenta_id', $accounts[$r]->id)->where('fecha_registro', '>=', $dateStart)
                        ->where('fecha_registro', '<=', $dateEnd)
                        ->where('detalles_recibo.deleted_at', '=', null)
                        ->join('recibo', 'recibo.id', '=', 'detalles_recibo.recibo_id')
                        ->sum('subtotal');
                        $text = $text.number_format($subtotal, 2);
                        $total += $subtotal;
                    break;
                }
                $table->addCell($width, [
                    // 'borderStyle' => 'dotted',
                    'borderSize' => 6
                    ])
                ->addTextRun([
                    // 'alignment' => 'center',
                    'spaceAfter' => 0,
                    'spaceBefore' => 0,
                ])
                ->addText($text);
            }
        }

        $table->addRow();
        $table->addCell($width, [
            // 'borderStyle' => 'dotted',
            'borderSize' => 6
            ])
        ->addTextRun([
            // 'alignment' => 'center',
            'spaceAfter' => 0,
            'spaceBefore' => 0,
        ])
        ->addText(count($accounts)+1);
        $table->addCell($width, [
            // 'borderStyle' => 'dotted',
            'borderSize' => 6
            ])
        ->addTextRun([
            // 'alignment' => 'center',
            'spaceAfter' => 0,
            'spaceBefore' => 0,
        ])
        ->addText("FIESTAS");

        $fiestas = DetalleRecibo::where('cuenta_id', '=', null)->where('fecha_registro', '>=', $dateStart)
        ->where('fecha_registro', '<=', $dateEnd)
        ->where('detalles_recibo.deleted_at', '=', null)
        ->join('recibo', 'recibo.id', '=', 'detalles_recibo.recibo_id')
        ->sum('subtotal');
        $total += $fiestas;
        $table->addCell($width, [
            // 'borderStyle' => 'dotted',
            'borderSize' => 6
            ])
        ->addTextRun([
            // 'alignment' => 'center',
            'spaceAfter' => 0,
            'spaceBefore' => 0,
        ])
        ->addText("$ ".number_format($fiestas, 2));

        $table->addRow();
        $table->addCell(4000, [
            'borderSize' => 6,
            'gridSpan' => 2,
        ])
        ->addTextRun([
            'alignment' => 'center',
            'spaceBefore' => 30,
            'spaceAfter' => 30,
            'bold' => true,
        ])
        ->addText("TOTAL DE INGRESOS", ['bold' => true]);

        $table->addCell(4000, [
            'borderSize' => 6,
            'bgColor' => '#e0e0e0',
        ])
        ->addTextRun([
            // 'alignment' => 'center',
            'spaceBefore' => 30,
            'spaceAfter' => 30,
        ])
        ->addText("$ ".number_format($total, 2), ['bold' => true]);

        // Create writer to convert document to xml
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($document_with_table, 'Word2007');

        // Get all document xml code
        $fullxml = $objWriter->getWriterPart('Document')->write();

        // Get only table xml code
        $tablexml = preg_replace('/^[\s\S]*(<w:tbl\b.*<\/w:tbl>).*/', '$1', $fullxml);

        //Open template with ${table}
        $template_document = new \PhpOffice\PhpWord\TemplateProcessor(storage_path('reports/recibo_tributos.docx'));

        // Replace mark by xml code of table
        $template_document->setValue('table', $tablexml);
        $template_document->setValue('dateStart', $dateStart);
        $template_document->setValue('dateEnd', $dateEnd);

        //save template with table
        $tempFile = tempnam(sys_get_temp_dir(), 'PHPWord');
        $template_document->saveAs($tempFile);

        $headers = [
            'Content-Type: application/octet-stream',
        ];

        return response()->download($tempFile, "Reporte de tributos.docx", $headers)->deleteFileAfterSend(true);
    }
}
