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

    public function getPreferences($userId)
    {
        //get user
        $user = User::find($userId);
        if (!$user) {
            return ApiResponse(false, 'User not found.', null, Response::HTTP_NOT_FOUND);
        }

        //get preference based on user id
        $preference = Preference::where('user_id', $userId)->first();

        if (!$preference) {
            return ApiResponse(false, 'Preferences not found for the given user.', null, Response::HTTP_NOT_FOUND);
        }

        $preference->sources = json_decode($preference->sources, true);
        $preference->authors = json_decode($preference->authors, true);
        $response = [
            'user_id' => $preference->user->id,
            'user_name' => $preference->user->name,
            'categories' => $preference->category_names,
            'sources' => $preference->sources,
            'authors' => $preference->authors,
        ];
        return ApiResponse(
            true,
            'User preferences retrieved successfully.',
            $response,
            Response::HTTP_OK);
    }


}
