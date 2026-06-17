<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeerahClass;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Log;

class SeerahController extends Controller
{
    protected $cloudinary;

    // CloudinaryService-কে ইনজেক্ট করা হলো
    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    // সব ক্লাসের লিস্ট দেখানোর জন্য
    public function index()
    {
        $classes = SeerahClass::latest()->get();
        return view('admin.seerah.index', compact('classes'));
    }

    // আপলোড পেজ দেখানোর জন্য
    public function create()
    {
        return view('admin.seerah.create');
    }

    // আপলোড প্রসেস করার জন্য
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'video' => 'required|file|mimes:mp4,mov,avi,wmv|max:102400', // max 100MB
            'doc' => 'nullable|file|mimes:pdf,doc,docx|max:10240',    // max 10MB
        ]);

        try {
            // ১. ভিডিও আপলোড
            $videoUrl = $this->cloudinary->uploadImage($request->file('video'));

            // ২. ডকুমেন্ট আপলোড (যদি থাকে)
            $docUrl = null;
            if ($request->hasFile('doc')) {
                $docUrl = $this->cloudinary->uploadImage($request->file('doc'));
            }

            // ৩. ডাটাবেজে সেভ
            SeerahClass::create([
                'title' => $request->title,
                'video_url' => $videoUrl,
                'doc_url' => $docUrl,
            ]);

            return redirect()->back()->with('success', 'Seerah Masterclass uploaded successfully!');

        } catch (\Exception $e) {
            Log::error('Upload Error: ' . $e->getMessage());
            return redirect()->back()->withErrors('Failed to upload file. Please try again.');
        }
    }
}