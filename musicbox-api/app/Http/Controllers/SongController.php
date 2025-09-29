<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;

class SongController extends Controller
{
    public function index() {
        return Song::with('album.artist')->paginate(10);
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'album_id' => 'required|exists:albums,id',
        ]);

        return Song::create($request->all());
    }

    public function show($id) {
        return Song::with('album.artist')->findOrFail($id);
    }

    public function update(Request $request, $id) {
        $song = Song::findOrFail($id);
        $song->update($request->all());
        return $song;
    }

    public function destroy($id) {
        $song = Song::findOrFail($id);
        $song->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
