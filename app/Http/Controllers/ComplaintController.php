<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Log;
use App\Models\Tag;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'body' => 'required|string',
            'location' => 'required|string',
            'tag' => 'required',
            'tweettimestamp' => 'required',
        ]);

        $complaint = Complaint::create([
            'username' => $request->username,
            'body' => $request->body,
            'location' => $request->location,
            'status' => 'unfinished',
            'tweettimestamp' => $request->tweettimestamp,
        ]);

        $tags = explode(',', $request->tag);
        foreach ($tags as $key => $tag) {
            $temp_tag = Tag::firstOrCreate([
                'name' => $tag,
            ]);
            $complaint->tags()->attach($temp_tag);
        }

        return response()->json([
            'message' => 'Penambahan Complaint Success',
            'complaint' => $complaint,
        ], 200);
    }

    public function index()
    {
        $complaints = Complaint::with('tags:id,name')->orderBy('status')->get();

        return response()->json([
            'complaints' => $complaints,
        ], 200);
    }

    public function acceptComplaint(Complaint $complaint)
    {
        $admin = auth()->user();
        if ($complaint->admin_id) {
            if ($complaint->status == 'finished') {
                return response()->json([
                    'message' => 'Complaint ini telah selesai. Mohon pilih complaint lain',
                ], 422);
            }

            return response()->json([
                'message' => 'Complaint ini telah diambil oleh admin lain. Coba lagi di lain waktu. ',
            ], 422);
        }
        $complaint->accept($admin);
        Log::create([
            'user_id' => $admin->id,
            'action' => 'Accept',
            'complaint_id' => $complaint->id,
        ]);

        return response()->json([
            'message' => 'Complaint '.$complaint->tittle.' sukses diambil oleh admin '.$admin->name.' .',
        ], 200);
    }

    public function finishComplaint(Complaint $complaint, Request $request)
    {
        $admin = auth()->user();
        $request->validate([
            'photo' => 'required|image|max:5120',
            'note' => 'required|string',
        ]);

        $complaint->status = 'finished';
        $complaint->note = $request->note;
        $complaint->save();

        $file = $request->file('photo');
        $path = '/storage'.substr($file->store('public/complaints'), 6);
        $complaint->photo = $path;
        $complaint->save();

        $admin->exp += 10;
        $admin->checkRank();

        Log::create([
            'user_id' => $admin->id,
            'action' => 'Finish',
            'complaint_id' => $complaint->id,
        ]);

        return response()->json([
            'message' => 'Complaint '.$complaint->tittle.'  telah diselesaikan.',
        ]);
    }
}
