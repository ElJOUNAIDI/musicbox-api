<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;

class ArtistController extends Controller
{

    public function index(Request $request) {
        $perPage = $request->get('per_page', 10); // عدد العناصر في الصفحة
        $artists = Artist::with('albums.songs')->paginate($perPage);

        return response()->json([
            'message' => 'Artists retrieved successfully',
            'data' => $artists
        ]);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'genre' => 'required|string|max:255',
            'country' => 'required|string|max:255'
        ]);

        $artist = Artist::create([
            'name' => $request->name,
            'genre' => $request->genre,
            'country' => $request->country,
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'message' => 'Artist created successfully',
            'data' => $artist
        ], 201);
    }

 
    public function update(Request $request, $id) {
        $artist = Artist::findOrFail($id);

        if($artist->user_id !== auth()->id()) {
            return response()->json(['message'=>'Unauthorized'],403);
        }

        $artist->update($request->only(['name','genre','country']));

        return response()->json([
            'message' => 'Artist updated successfully',
            'data' => $artist
        ]);
    }

    public function destroy($id) {
        $artist = Artist::findOrFail($id);

        if($artist->user_id !== auth()->id()) {
            return response()->json(['message'=>'Unauthorized'],403);
        }

        $artist->delete();

        return response()->json([
            'message' => 'Artist deleted successfully'
        ]);
    }
}
