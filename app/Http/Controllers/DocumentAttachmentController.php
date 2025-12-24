<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;

class DocumentAttachmentController extends Controller
{
    public function store(Request $request, Document $document)
{
    $request->validate([
        'file' => 'required|file|max:10240',
    ]);

    $path = $request->file('file')->store('documents', 'public');

    $document->attachments()->create([
        'file_name' => $request->file('file')->getClientOriginalName(),
        'file_path' => $path,
        'uploaded_by' => auth()->id(),
    ]);

    return back();
}
}
