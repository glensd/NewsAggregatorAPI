<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Preference;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticlesController extends Controller
{
    public function index(Request $request)
    {
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

        $articles = $query->paginate(10);
        return ApiResponse(
            true,
            'Success',
            $articles,
            Response::HTTP_OK
        );
    }

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
        $query = Article::query();

        //filter by categories, if any
        if (!empty($categoryIds)) {
            $query->whereIn('category_id', $categoryIds);
        }

        //filter by sources, if any
        if (!empty($sources)) {
            $query->whereIn('source', $sources);
        }

        //filter by authors, if any
        if (!empty($authors)) {
            $query->whereIn('author', $authors);
        }

        //pagination
        $articles = $query->paginate(10);

        return ApiResponse(true, 'Personalized news feed retrieved successfully.', $articles, Response::HTTP_OK);
    }

}
