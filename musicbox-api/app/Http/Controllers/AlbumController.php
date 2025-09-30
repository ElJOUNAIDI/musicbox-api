<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    /**
 * @OA\Get(
 *     path="/api/albums",
 *     summary="Get all albums",
 *     tags={"Albums"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(response=200, description="Albums retrieved successfully")
 * )
 */
    public function index(Request $request) {
        $perPage = $request->get('per_page', 10);
        $albums = Album::with('songs','artist')->paginate($perPage);

        return response()->json([
            'message' => 'Albums retrieved successfully',
            'data' => $albums
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/albums",
     *     summary="Create a new album",
     *     tags={"Albums"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","year","artist_id"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="year", type="integer"),
     *             @OA\Property(property="artist_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Album created successfully")
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/albums/{id}",
     *     summary="Update an album",
     *     tags={"Albums"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Album ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="year", type="integer"),
     *             @OA\Property(property="artist_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Album updated successfully"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
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
    
    /**
     * @OA\Delete(
     *     path="/api/albums/{id}",
     *     summary="Delete an album",
     *     tags={"Albums"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Album ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Album deleted successfully"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
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
