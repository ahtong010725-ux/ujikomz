<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GroupMessage;
use App\Models\User;

class GroupChatController extends Controller
{
    public function index()
    {
        $kelas = auth()->user()->kelas;

        if (!$kelas) {
            return redirect('/inbox')->with('error', 'Kelas kamu belum terdaftar.');
        }

        $messages = GroupMessage::with('sender')
            ->where('kelas', $kelas)
            ->orderBy('created_at', 'asc')
            ->get();

        // Get members of this class
        $members = User::where('kelas', $kelas)
            ->where('id', '!=', auth()->id())
            ->get();

        return view('group-chat', compact('messages', 'kelas', 'members'));
    }

    public function fetchMessages()
    {
        $kelas = auth()->user()->kelas;

        $messages = GroupMessage::with('sender')
            ->where('kelas', $kelas)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('partials.group-chat-messages', compact('messages'));
    }

    public function sendMessage(Request $request)
    {
        // Soft ban check
        if (auth()->user()->isSoftBanned()) {
            return response()->json(['error' => 'Akun kamu sedang di-soft ban.'], 403);
        }

        $request->validate([
            'message' => 'nullable|required_without:image',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120'
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('group_photos', 'public');
        }

        GroupMessage::create([
            'kelas' => auth()->user()->kelas,
            'sender_id' => auth()->id(),
            'message' => $request->message ?? '',
            'image' => $imagePath,
        ]);

        return response()->json(['success' => true]);
    }
}
