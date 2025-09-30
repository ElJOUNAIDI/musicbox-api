<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;

class SongController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/songs",
     *     summary="Get all songs",
     *     tags={"Songs"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Songs retrieved successfully")
     * )
     */
    public function index(Request $request) {
        $perPage = $request->get('per_page', 10);
        $songs = Song::with('album','album.artist')->paginate($perPage);

        return response()->json([
            'message' => 'Songs retrieved successfully',
            'data' => $songs
        ]);
    }
/**
     * @OA\Get(
     *     path="/api/songs/{id}",
     *     summary="Get song by ID",
     *     tags={"Songs"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Song ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Song retrieved successfully"),
     *     @OA\Response(response=404, description="Song not found")
     * )
     */
    public function show($id) {
        $song = Song::with('album.artist')->findOrFail($id);

        return response()->json([
            'message' => 'Song retrieved successfully',
            'data' => $song
        ]);
    }
    
    /**
     * @OA\Post(
     *     path="/api/songs",
     *     summary="Create a new song",
     *     tags={"Songs"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","duration","album_id"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="duration", type="integer"),
     *             @OA\Property(property="album_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Song created successfully")
     * )
     */
    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|integer',
            'album_id' => 'required|exists:albums,id'
        ]);

        $song = Song::create([
            'title' => $request->title,
            'duration' => $request->duration,
            'album_id' => $request->album_id,
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'message' => 'Song created successfully',
            'data' => $song
        ], 201);
    }
    
    /**
     * @OA\Put(
     *     path="/api/songs/{id}",
     *     summary="Update a song",
     *     tags={"Songs"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Song ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="duration", type="integer"),
     *             @OA\Property(property="album_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Song updated successfully"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function update(Request $request, $id) {
        $song = Song::findOrFail($id);

        if($song->user_id !== auth()->id()) {
            return response()->json(['message'=>'Unauthorized'],403);
        }

        $song->update($request->only(['title','duration','album_id']));

        return response()->json([
            'message' => 'Song updated successfully',
            'data' => $song
        ]);
    }
    
    /**
     * @OA\Delete(
     *     path="/api/songs/{id}",
     *     summary="Delete a song",
     *     tags={"Songs"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Song ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Song deleted successfully"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function destroy($id) {
        $song = Song::findOrFail($id);

        if($song->user_id !== auth()->id()) {
            return response()->json(['message'=>'Unauthorized'],403);
        }

        $song->delete();

        return response()->json([
            'message' => 'Song deleted successfully'
        ]);
    }
}
