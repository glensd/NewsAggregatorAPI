<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:fetch';
    protected $description = 'Fetch articles from news APIs';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        function getCategoryId($categoryName)
        {
            if ($categoryName) {
                // Find the category or create if no exist
                $category = Category::firstOrCreate(['name' => $categoryName]);
                return $category->id;
            }
            return null;
        }

        // Fetch and cache News api
        $newsApiKey = config('news-api-keys.NEWS_API_KEY');
        $newsApiResponse = Cache::remember('newsapi_articles', 3600, function () use ($newsApiKey) {
            return Http::get("https://newsapi.org/v2/top-headlines?country=us&apiKey=$newsApiKey")->json()['articles'];
        });
        foreach ($newsApiResponse as $article) {
            if (!empty($article['url'])) {
                $uniqueIdentifier = md5($article['title'] . $article['publishedAt'] . $article['source']['name']);
                $publishedAt = isset($article['publishedAt']) ? Carbon::parse($article['publishedAt'])->format('Y-m-d H:i:s') : now();

            Article::updateOrCreate(
                    ['unique_identifier' => $uniqueIdentifier],
                    [
                        'title' => $article['title'] ?? null,
                        'content' => $article['description'] ?? null,
                        'author' => $article['author'] ?? null,
                        'source' => $article['source']['name'] ?? 'NewsAPI',
                        'published_at' => $publishedAt,
                        'category_id' => 1,
                    ]
            );
            } else {
                \Log::warning('Skipping news api article as there is no valid URL', $article);
            }
        }
        // Fetch and cache Guardian api
        $GuardianApiKey = config('news-api-keys.Guardian_API_KEY');
        $guardianResponse = Cache::remember('guardian_articles', 3600, function () use ($GuardianApiKey) {
            return Http::get("https://content.guardianapis.com/search?api-key=$GuardianApiKey")->json()['response']['results'];
        });

        foreach ($guardianResponse as $guardianArticle) {
            if (!empty($guardianArticle['webUrl'])) {
            $uniqueIdentifier = $guardianArticle['id'];
            $publishedAt = isset($guardianArticle['webPublicationDate']) ? Carbon::parse($guardianArticle['webPublicationDate'])->format('Y-m-d H:i:s') : now();
            $categoryName = $guardianArticle['sectionName'] ?? null;
            $categoryId = getCategoryId($categoryName);

                Article::updateOrCreate(
                ['unique_identifier' => $uniqueIdentifier],
                [
                    'title' => $guardianArticle['webTitle'] ?? null,
                    'content' => $guardianArticle['webUrl'] ,
                    'author' => null,
                    'source' => 'The Guardian',
                    'published_at' => $publishedAt ??  null,
                    'category_id' => $categoryId,
                ]
            );
            } else {
                \Log::warning('Skipping Guardian article as there is no valid URL', $guardianArticle);
            }
        }
        $nytApiKey = config('news-api-keys.NYT_API_KEY');
        $nytApiResponse = Cache::remember('nyt_articles', 3600, function () use ($nytApiKey) {
            return Http::get("https://api.nytimes.com/svc/topstories/v2/arts.json?api-key=$nytApiKey")->json()['results'];
        });
            foreach ($nytApiResponse as $nytArticle) {
                if (!empty($nytArticle['url'])) {
                    $uniqueIdentifier = $nytArticle['uri'];
                    $publishedAt = isset($nytArticle['published_date']) ? Carbon::parse($nytArticle['published_date'])->format('Y-m-d H:i:s') : now();
                    $categoryName = $nytArticle['section'] ?? null;
                    $categoryId = getCategoryId($categoryName);
                    Article::updateOrCreate(
                        ['unique_identifier' => $uniqueIdentifier],
                        [
                            'title' => $nytArticle['title'] ?? null,
                            'content' => $nytArticle['abstract'] ?? null,
                            'author' => $nytArticle['byline'] ? str_replace('By ', '', $nytArticle['byline']) : null,
                            'source' => 'The New York Times',
                            'published_at' => $publishedAt ??  null,
                            'category_id' => $categoryId,
                        ]
                    );
                } else {
                    \Log::warning('Skipping NY Times article without a valid URL', $nytArticle);
                }
            }

        $this->info('Articles fetched and stored successfully!');
    }
}
