<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;

class ArtistController extends Controller
{
    public function index() {
        return Artist::with('albums.songs')->paginate(10);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'genre' => 'required|string|max:255',
            'country' => 'required|string|max:255',
        ]);

        return Artist::create($request->all());
    }

    public function show($id) {
        return Artist::with('albums.songs')->findOrFail($id);
    }

    public function update(Request $request, $id) {
        $artist = Artist::findOrFail($id);
        $artist->update($request->all());
        return $artist;
    }

    public function destroy($id) {
        $artist = Artist::findOrFail($id);
        $artist->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
