<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Preference;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ArticlesController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/articles",
     *     summary="Get a list of articles",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="Search keyword for articles",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="source",
     *         in="query",
     *         description="Filter by source",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Filter by date",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by category ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Success"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'keyword' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return ApiResponse(false, $validator->errors()->first(), $validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $query = Article::with('category');
        //title and content filter
        if ($request->has('keyword')) {
            $query->where('title', 'like', '%' . $request->keyword . '%')
                ->orWhere('content', 'like', '%' . $request->keyword . '%');
        }

        //source filter
        if ($request->has('source')) {
            $query->where('source', 'like', '%' . $request->source . '%');
        }
        //date filter
        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }
        //category filter
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        $cacheKey = 'articles_' . $request->getQueryString();
        $articles = Cache::remember($cacheKey, 60, function () use ($query) {
            return $query->paginate(10);
        });
        return ApiResponse(
            true,
            'Success',
            $articles,
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     summary="Get a specific article",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the article to return",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article retrieved successfully",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found"
     *     )
     * )
     */
    public function show($id)
    {
        //get article
        $article = Article::with('category')->find($id);

        if (!$article) {
            return ApiResponse(false, 'Article not found', 'Article not found', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return ApiResponse(
            true,
            'Success',
            $article,
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *     path="/api/user-personalized-feed/{userId}",
     *     summary="Get personalized news feed for a user",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Personalized news feed retrieved successfully",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User or preferences not found"
     *     )
     * )
     */
    public function personalizedFeed($userId)
    {
        // get user
        $user = User::find($userId);
        if (!$user) {
            return ApiResponse(false, 'User not found.', null, Response::HTTP_NOT_FOUND);
        }

        // retrieve user preferences by id
        $preference = Preference::where('user_id', $userId)->first();
        if (!$preference) {
            return ApiResponse(false, 'User preferences not found.', null, Response::HTTP_NOT_FOUND);
        }

        // decode the preferences
        $categoryIds = json_decode($preference->categories, true);
        $sources = json_decode($preference->sources, true);
        $authors = json_decode($preference->authors, true);

       //article query
        $query = Article::query()->with('category');

        //filter by categories, if any
        if (!empty($categoryIds)) {
            $query->orWhereIn('category_id', $categoryIds);
        }

        //filter by sources, if any
        if (!empty($sources)) {
            $query->orWhereIn('source', $sources);
        }

        //filter by authors, if any
        if (!empty($authors)) {
            $query->orWhereIn('author', $authors);
        }

        //pagination
        $articles = $query->paginate(10);

        return ApiResponse(true, 'Personalized news feed retrieved successfully.', $articles, Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Get all categories",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Categories retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", description="Category ID"),
     *                 @OA\Property(property="name", type="string", description="Category Name"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function getCategory(Request $request){
        //caching
        $cacheKey = 'categories';
        $categories = Cache::remember($cacheKey, 60, function () {
            return Category::all();
        });

        return ApiResponse(true, 'Success', $categories, Response::HTTP_OK);
    }
}
