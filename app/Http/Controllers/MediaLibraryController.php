<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use File;

class MediaLibraryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function getHome()
    {
        
        $this->view = [
            'title' => "Media Library",
            "files" => Auth::user()->getMedia('files')->sortByDesc('created_at')->paginate(5)
        ];
        return view('media.index', $this->view);
    }

    public function addMedia(Request $request)
    {
        $uploadedFile = $request->file("file"); 
        $time = time();
        $fileName = $uploadedFile->getClientOriginalName();
        $uploadedFile->move(public_path("media"), $fileName);

        Auth::user()->addMedia(public_path("media")."/".$fileName)->toMediaCollection('files');

        File::delete(public_path("media")."/".$fileName);
        return redirect()->route('media');
    }

    public function removeMedia(Request $request)
    {
        Auth::user()->deleteMedia($request->media_id);
        return redirect()->route('media');
    }
}