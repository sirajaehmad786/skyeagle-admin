<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Models\Document;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DocumentRepository extends BaseRepository
{

    public function __construct(Document $document)
    {
        parent::__construct($document);
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            if (!$request->hasFile('documents')) {
                throw new \Exception('No documents uploaded.');
            }
            $files = $request->file('documents');
            foreach ($files as $file) {
                $path = $file->store('documents', 'public');
                $this->model->create([
                    'user_id'    => Auth::id(),
                    'contact_id' => $request->contact_id,
                    'document'   => $path
                ]);
            }
            DB::commit();
            return true;
        } catch (\Throwable $e) {

            DB::rollBack();
            throw $e;
        }
    }



    public function initData($request)
    {
        $query = Document::query()
            ->join('contacts', 'documents.contact_id', '=', 'contacts.id')
            ->select(
                'contacts.id as contact_id',
                'contacts.first_name',
                'contacts.last_name',
                'contacts.mobile_no',
                DB::raw('MAX(documents.created_at) as created_at')
            )
            ->groupBy(
                'contacts.id',
                'contacts.first_name',
                'contacts.last_name',
                'contacts.mobile_no'
            );

        if ($request->filled('search_text')) {
            $search = trim($request->search_text);
            $query->where(function ($q) use ($search) {
                $q->where('contacts.first_name', 'like', "%{$search}%")
                ->orWhere('contacts.last_name', 'like', "%{$search}%")
                ->orWhere(DB::raw("CONCAT(contacts.first_name,' ',contacts.last_name)"), 'like', "%{$search}%")
                ->orWhere('contacts.mobile_no', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function getContactWithDocuments(int $contactId)
    {
    $contact = Contact::with('documents')
        ->findOrFail($contactId);

        return [
            'contact'   => $contact,
            'documents' => $contact->documents
        ];
    }

    public function updateDocuments($request, $contactId)
    {
        DB::beginTransaction();
        try {
            $contact = Contact::findOrFail($contactId);
            $existingDocIds = $request->existing_docs ?? [];     
            
            $contact->documents()
                ->whereNotIn('id', $existingDocIds)
                ->get()
                ->each(function ($doc) {
                    if ($doc->document && Storage::disk('public')->exists($doc->document)) {
                        Storage::disk('public')->delete($doc->document);
                    }
                    $doc->delete();
                });
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $path = $file->store('documents', 'public');
                    Document::create([
                        'user_id' => Auth::id(),
                        'contact_id' => $contactId,
                        'booking_id' => $request->booking_id,
                        'document' => $path
                    ]);
                }
            }
            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Document Update Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; 
        }
    }

    public function downloadDocuments(int $contactId)
    {
        $contact = Contact::findOrFail($contactId);
        $documents = Document::where('contact_id', $contactId)->get();

        if ($documents->isEmpty()) {
            throw new Exception('No documents found for this contact.');
        }
        $zipFileName = 'documents_' . $contact->first_name . '_' . time() . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($documents as $document) {
                $filePath = storage_path('app/public/' . $document->document);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, basename($filePath));
                }
            }
            $zip->close();
        }
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

     
}
