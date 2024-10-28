<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Preference;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserPreferenceController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/preferences",
     *     summary="Set user preferences",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id","categories"},
     *             @OA\Property(property="user_id", type="integer", description="ID of the user"),
     *             @OA\Property(property="categories", type="array", @OA\Items(type="integer"), description="Array of category IDs"),
     *             @OA\Property(property="sources", type="array", @OA\Items(type="string"), description="Array of source names"),
     *             @OA\Property(property="authors", type="array", @OA\Items(type="string"), description="Array of author names")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User preferences set successfully",
     *      ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function setPreferences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'categories' => 'required|array',
            'sources' => 'nullable|array',
            'authors' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return ApiResponse(false, $validator->errors()->first(), $validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedData = $validator->validated();

        $userId = $validatedData['user_id'];

        //update or create based on user id
        $preference = Preference::updateOrCreate(
            ['user_id' => $userId],
            [
                'categories' => json_encode($validatedData['categories']),
                'sources' => json_encode($validatedData['sources'] ?? []),
                'authors' => json_encode($validatedData['authors'] ?? []),
            ]
        );

        return ApiResponse(
            true,
            'Success',
            $preference,
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *     path="/api/preferences/{userId}",
     *     summary="Get user preferences",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID of the user to get preferences for",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User preferences retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user_id", type="integer", description="User ID"),
     *             @OA\Property(property="user_name", type="string", description="User Name"),
     *             @OA\Property(property="categories", type="array", @OA\Items(type="string"), description="List of category names"),
     *             @OA\Property(property="sources", type="array", @OA\Items(type="string"), description="List of sources"),
     *             @OA\Property(property="authors", type="array", @OA\Items(type="string"), description="List of authors")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User or preferences not found"
     *     )
     * )
     */
    public function getPreferences($userId)
    {
        //get user
        $user = User::find($userId);
        if (!$user) {
            return ApiResponse(false, 'User not found.', null, Response::HTTP_NOT_FOUND);
        }

        //get preference based on user id
        $preference = Preference::where('user_id', $userId)->first();
//        $preference = Preference::with('user')->where('user_id', $userId)->first();
        if (!$preference) {
            return ApiResponse(false, 'Preferences not found for the given user.', null, Response::HTTP_NOT_FOUND);
        }

        $sources = json_decode($preference->sources, true) ?? [];
        $authors = json_decode($preference->authors, true) ?? [];
        $response = [
            'user_id' => $preference->user->id,
            'user_name' => $preference->user->name,
            'categories' => $preference->category_names,
            'sources' => $sources,
            'authors' => $authors,
        ];
        return ApiResponse(
            true,
            'User preferences retrieved successfully.',
            $response,
            Response::HTTP_OK);
    }


}
