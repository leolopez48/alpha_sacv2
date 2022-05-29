<?php

namespace App\Http\Controllers\Checks;

use App\Models\Checks\Bank;
use App\Models\Checks\Document;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $documents = Document::select('*', "documents.id as id")
        ->join('banks as ba', 'documents.bank_id', '=', 'ba.id')
        ->paginate(15);

        return response()->json(['message' => 'success', 'documents'=>$documents]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->except('name_bank');
        $data['bank_id'] = Bank::where('name_bank', $request->name_bank)->first()->id;

        Document::insert($data);
        return response()->json(['message'=>'success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Document  $documents
     * @return \Illuminate\Http\Response
     */
    public function show(Document $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Document  $documents
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Document $document)
    {
        $data = $request->except('name_bank');
        $data['bank_id'] = Bank::where('name_bank', $request->name_bank)->first()->id;

        Document::where('id', $document->id)->update($data);
        return response()->json(["message"=>"success"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Document  $documents
     * @return \Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {
        Document::where('id', $document->id)->delete();
        return response()->json(["message"=>"success"]);
    }
}
