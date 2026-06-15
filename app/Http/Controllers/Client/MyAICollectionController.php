<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MyAICollection;
use App\Models\MyAIImageCollection;
use App\Models\MyAIImageTemplateCategory;
use Inertia\Inertia;
use Inertia\Response;

class MyAICollectionController extends Controller
{
    public function __construct()
    {

    }

    public function getListCollection(Request $request)
    {
        $per_page = 3;

        $collections = MyAIImageCollection::query()
            ->with('categories')
            ->paginate($per_page);

        return response()->json(['listCollections' => $collections]);
    }

    public function getListTemplateByCategory(Request $request)
    {
        $per_page = 12;

        $categoryId = $request->input('id');

        if (!$categoryId) {
            return response()->json(['error' => 'my_ai_collection_id is required'], 400);
        }

        $category = MyAIImageTemplateCategory::with('templates')
            ->find($categoryId);

        if (!$category) {
            return response()->json(['error' => 'Collection not found'], 404);
        }

        $templates = $category->templates()->paginate($per_page);

        return response()->json(['templates' => $templates]);
    }

}
