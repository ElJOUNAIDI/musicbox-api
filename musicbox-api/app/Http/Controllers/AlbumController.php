<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    public function index(Request $request) {
        $perPage = $request->get('per_page', 10);
        $albums = Album::with('songs','artist')->paginate($perPage);

        return response()->json([
            'message' => 'Albums retrieved successfully',
            'data' => $albums
        ]);
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'year' => 'required|integer',
            'artist_id' => 'required|exists:artists,id'
        ]);

        $album = Album::create([
            'title' => $request->title,
            'year' => $request->year,
            'artist_id' => $request->artist_id,
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'message' => 'Album created successfully',
            'data' => $album
        ], 201);
    }

    public function update(Request $request, $id) {
        $album = Album::findOrFail($id);

        if($album->user_id !== auth()->id()) {
            return response()->json(['message'=>'Unauthorized'],403);
        }

        $album->update($request->only(['title','year','artist_id']));

        return response()->json([
            'message' => 'Album updated successfully',
            'data' => $album
        ]);
    }

    public function destroy($id) {
        $album = Album::findOrFail($id);

        if($album->user_id !== auth()->id()) {
            return response()->json(['message'=>'Unauthorized'],403);
        }

        $album->delete();

        return response()->json([
            'message' => 'Album deleted successfully'
        ]);
    }
}
