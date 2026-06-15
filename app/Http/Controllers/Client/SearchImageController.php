<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DevDojo\GoogleImageSearch\ImageSearch;
class SearchImageController extends Controller
{
    public function index(Request $request)
    {
        try {
            $keyword = $request->string('keyword');
            if (!$keyword) {
                return null;
            }

            $colorType = $request->query('color_type', 'color');
            $page = $request->integer('page', 1);
            $perPage = $request->integer('per_page', 10);
            $postFix = match ($colorType) {
                'trans', 'gray' => ' png',
                default => ''
            };
            $query = $keyword . $postFix; // Default to 'apple' if no query provided
            $parameters = [
                'start' => ($page - 1) * $perPage + 1, // start from the 1st results,
                'num' => $perPage, // number of results to get, 10 is maximum and also default value,
                'imgColorType' => $colorType, // color, gray, mono, trans
            ];
            $apiKey = config('common.google_search_api_key');
            $cx = config('common.google_search_engine_id');
            ImageSearch::config()->apiKey($apiKey);
            ImageSearch::config()->cx($cx);

            $results = ImageSearch::search($query, $parameters);

            return response()->json($results);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function iconSearch(Request $request)
    {
        try {
            $keyword = $request->string('keyword');
            if (!$keyword) {
                return null;
            }

            $colorType = $request->query('color_type', 'color');
            $page = $request->integer('page', 1);
            $perPage = $request->integer('per_page', 10);
            $postFix = match ($colorType) {
                'trans', 'gray' => ' png',
                default => ''
            };
            $query = 'icon'. $keyword . $postFix; // Default to 'apple' if no query provided
            $parameters = [
                'start' => ($page - 1) * $perPage + 1, // start from the 1st results,
                'num' => $perPage, // number of results to get, 10 is maximum and also default value,
                'imgColorType' => $colorType, // color, gray, mono, trans
            ];
            $apiKey = config('common.google_search_api_key');
            $cx = config('common.google_search_engine_id');
            ImageSearch::config()->apiKey($apiKey);
            ImageSearch::config()->cx($cx);

            $results = ImageSearch::search($query, $parameters);

            return response()->json($results);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function searchPixabay(Request $request)
    {
        try {
            $keyword = $request->string('keyword');
            if (!$keyword) {
                return response()->json(['error' => 'Keyword is required'], 400);
            }

            $page = $request->integer('page', 1);
            $perPage = $request->integer('per_page', 10);

            $apiKey = config('common.pixabay_api_key');

            $response = Http::get('https://pixabay.com/api/', [
                'key' => $apiKey,
                'q' => $keyword,
                'page' => $page,
                'per_page' => $perPage,
                'image_type' => 'photo',
            ]);

            $results = $response->json();

            return response()->json($results);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }
}