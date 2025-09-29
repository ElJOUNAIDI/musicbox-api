<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    public function index() {
        return Album::with('artist', 'songs')->paginate(10);
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
            'artist_id' => 'required|exists:artists,id',
        ]);

        return Album::create($request->all());
    }

    public function show($id) {
        return Album::with('artist', 'songs')->findOrFail($id);
    }

    public function update(Request $request, $id) {
        $album = Album::findOrFail($id);
        $album->update($request->all());
        return $album;
    }

    public function destroy($id) {
        $album = Album::findOrFail($id);
        $album->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
