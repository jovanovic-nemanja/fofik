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
        $this->documentService->setCelebNames($params);
        return response()->json([
            'success' => true
        ]);
    }
}
