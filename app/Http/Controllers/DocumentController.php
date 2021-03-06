<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\DocumentService;

class DocumentController extends Controller
{
    //
    protected $documentService;
    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }
    public function index()
    {
        return view('doc.index');
    }
    public function celebNames(Request $request)
    {
        $params = $request->all();
        if ($csv = $request->file('csv'))
        {
            $fh = fopen($_FILES['csv']['tmp_name'], 'r+');
            $lines = array();
            while( ($row = fgetcsv($fh, 8192)) !== FALSE ) {
                $lines[] = $row[0];
            }
            $params['names'] = $lines;
        }
        
        $this->documentService->setCelebNames($params);
        return response()->json([
            'success' => true
        ]);
    }
}
