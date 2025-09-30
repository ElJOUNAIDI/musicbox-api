<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;

class ArtistController extends Controller
{
/**
 * @OA\Get(
 *     path="/api/artists",
 *     summary="Get all artists (with optional filter by genre)",
 *     tags={"Artists"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="genre",
 *         in="query",
 *         description="Filter by music genre",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Number of results per page",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Artists retrieved successfully")
 * )
 */
    public function index(Request $request) {
        $perPage = $request->get('per_page', 10); 

        $query = Artist::with('albums.songs');

        
        if ($request->has('genre')) {
            $query->where('genre', $request->get('genre'));
        }

        $artists = $query->paginate($perPage);

        return response()->json([
            'message' => 'Artists retrieved successfully',
            'data' => $artists
        ]);
    }

        /**
     * @OA\Get(
     *     path="/api/artists/{id}",
     *     summary="Get artist by ID (with albums and songs)",
     *     tags={"Artists"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Artist ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Artist retrieved successfully"),
     *     @OA\Response(response=404, description="Artist not found")
     * )
     */
    public function show($id) {
        $artist = Artist::with('albums.songs')->findOrFail($id);

        return response()->json([
            'message' => 'Artist retrieved successfully',
            'data' => $artist
        ]);
    }


        /**
     * @OA\Post(
     *     path="/api/artists",
     *     summary="Create a new artist",
     *     tags={"Artists"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","genre","country"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="genre", type="string"),
     *             @OA\Property(property="country", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Artist created successfully")
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/artists/{id}",
     *     summary="Update an artist",
     *     tags={"Artists"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Artist ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="genre", type="string"),
     *             @OA\Property(property="country", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Artist updated successfully"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */

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

    /**
     * @OA\Delete(
     *     path="/api/artists/{id}",
     *     summary="Delete an artist",
     *     tags={"Artists"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Artist ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Artist deleted successfully"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    
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
