<?php

namespace App\Http\Controllers\Crm;

use App\Enums\ActivityAction;
use App\Enums\ActivityType;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use App\Repositories\ContactRepository;
use App\Repositories\DocumentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class DocumentController extends Controller
{
    protected $documentRepository;
    protected $contactRepository;

    public function __construct(DocumentRepository $documentRepository,ContactRepository $contactRepository)
    {
        $this->middleware('permission:document-list')->only('index','initDataTable');
        $this->middleware('permission:document-add')->only('create', 'store');
        $this->middleware('permission:document-edit')->only('edit', 'update');
        $this->middleware('permission:document-delete')->only('destroy');
        $this->middleware('permission:document-download')->only('download');

        $this->documentRepository = $documentRepository;
        $this->contactRepository = $contactRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->initDataTable($request);
        }
        return view('crm.document.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()    
    {
        $contacts = $this->contactRepository->getAllContacts();
        return view('crm.document.create', compact('contacts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDocumentRequest $request)
    {
        try {
            $document  = $this->documentRepository->create($request);
            activityLog(
                'Document Module',
                ActivityType::DOCUMENT,
                ActivityAction::CREATE,
                Document::class,
                $document->id ?? null,
                'Documents uploaded successfully',
                [],
                $request->all(),
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'added_by' => Auth::id()
                ]
            );
            session()->flash('success', 'Documents uploaded successfully');
            return response()->json([
                'status' => true,
                'redirect_url' => route('documents.index')
            ]);
        }catch (\Throwable $e) {
            Log::error('Document Upload Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Document upload failed. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($contactId)
    {
        $data = $this->documentRepository->getContactWithDocuments($contactId);
        return view('crm.document.edit', [
                'contact'   => $data['contact'],
                'documents' => $data['documents']
            ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentRequest $request, $contactId)
    {
        try {
            $documents = Document::where('contact_id', $contactId)->get();
            $oldData = $documents->toArray();
            $this->documentRepository->updateDocuments($request, $contactId);
            activityLog(
                'Document Module',
                ActivityType::DOCUMENT,
                ActivityAction::UPDATE,
                Document::class,
                $documents->first()->id ?? null,
                'Documents updated successfully',
                $oldData,
                $request->all(),
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'added_by' => Auth::id()
                ]
            );  
            session()->flash('success', 'Documents Updated successfully');
            return response()->json([
                'status' => true,
                'redirect_url' => route('documents.index')
            ]);
        } catch (\Throwable $e) {
            return back()->with(
                'error',
                'Something went wrong while updating documents.'
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($contact_id)
    {
        $documents = Document::where('contact_id', $contact_id)->get();
        if ($documents->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No documents found for this contact'
            ], 404);
        }

        $oldData = $documents->toArray();
        DB::beginTransaction();
        try {
            foreach ($documents as $document) {
                if (!empty($document->document) && Storage::disk('public')->exists($document->document)) {
                    Storage::disk('public')->delete($document->document);
                }
                $document->delete();
            }
            DB::commit();
            activityLog(
                'Document Module',
                ActivityType::DOCUMENT,
                ActivityAction::DELETE,
                Document::class,
                $contact_id,
                'All documents deleted successfully',
                $oldData,
                [],
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'added_by' => Auth::id()
                ]
            );
            return response()->json([
                'status' => true,
                'message' => 'All documents for this contact deleted successfully'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Document Delete Failed', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Delete failed'
            ], 500);
        }
    }

    protected function initDataTable($request)
    {
        $data = $this->documentRepository->initData($request);
        return DataTables::of($data)
            ->orderColumn('created_at','created_at $1')
            ->addColumn('name', function ($row) {
                return $row->first_name . ' ' . $row->last_name;
            })
            ->addColumn('mobile_no', function ($row) {
                return $row->mobile_no ?? '-';
            })
            ->addColumn('created_at', function ($row) {
                return formateDate($row->created_at);
            })
            ->addColumn('action', function ($row) {
                return view('crm.document.action', compact('row'))->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function download($contactId)
    {
        try {
            $response = $this->documentRepository->downloadDocuments($contactId);
            activityLog(
                'Document Module',
                ActivityType::DOCUMENT,
                ActivityAction::VIEW,
                Document::class,
                $contactId,
                'Documents downloaded successfully',
                [],
                [],
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'added_by' => Auth::id()
                ]
            );
            return $response;
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function getByContact($contactId)
    {
        $docs = Document::where('contact_id', $contactId)->get();
        return response()->json([
            'data' => $docs->map(function ($doc) {
                $filePath = $doc->document;
                $fileName = basename($filePath);
                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                return [
                    'id' => $doc->id,
                    'name' => $fileName,
                    'url' => asset('storage/' . $filePath),
                    'file_type' => $extension
                ];
            })
        ]);
    }

    
}
