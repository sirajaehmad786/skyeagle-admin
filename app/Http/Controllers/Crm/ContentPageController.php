<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Repositories\ContentPageRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContentPageController extends Controller
{
    public function __construct(
        protected ContentPageRepository $contentPageRepository,
    ) {
    }

    public function index()
    {
        $pages = $this->contentPageRepository->getManagedPages();

        return view('crm.content-pages.index', compact('pages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pages' => ['required', 'array'],
            'pages.privacy-policy.title' => ['required', 'string', 'max:255'],
            'pages.privacy-policy.content' => ['nullable', 'string'],
            'pages.privacy-policy.is_active' => ['nullable', 'boolean'],
            'pages.terms-conditions.title' => ['required', 'string', 'max:255'],
            'pages.terms-conditions.content' => ['nullable', 'string'],
            'pages.terms-conditions.is_active' => ['nullable', 'boolean'],
            'pages.refund-policy.title' => ['required', 'string', 'max:255'],
            'pages.refund-policy.content' => ['nullable', 'string'],
            'pages.refund-policy.is_active' => ['nullable', 'boolean'],
        ]);

        try {
            $pages = $this->contentPageRepository->saveManagedPages($validated['pages']);

            return response()->json([
                'status' => true,
                'message' => 'Page settings saved successfully.',
                'pages' => $pages->map(fn ($page) => [
                    'title' => $page->title,
                    'content' => $page->content,
                    'is_active' => $page->is_active,
                ]),
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}
