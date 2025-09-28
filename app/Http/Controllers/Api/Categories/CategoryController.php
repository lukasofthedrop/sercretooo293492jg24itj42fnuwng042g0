<?php

namespace App\Http\Controllers\Api\Categories;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = Category::get();
            if ($categories && $categories->count() > 0) {
                return response()->json(['categories' => $categories]);
            }
        } catch (\Throwable $e) {
            \Log::warning('DB categories unavailable, using fallback.', ['error' => $e->getMessage()]);
        }

        // Fallback: extrair categorias do dataset estático
        $path = resource_path('data/casino-games.json');
        $result = [];
        if (file_exists($path)) {
            $payload = json_decode(file_get_contents($path), true);
            $games = $payload['games']['data'] ?? $payload['games'] ?? [];
            $seen = [];
            foreach ($games as $game) {
                foreach (($game['categories'] ?? []) as $cat) {
                    $key = strtolower(trim(($cat['slug'] ?? $cat['name'] ?? '')));
                    if ($key !== '' && !isset($seen[$key])) {
                        $seen[$key] = true;
                        $result[] = [
                            'id' => $cat['id'] ?? null,
                            'name' => $cat['name'] ?? $key,
                            'slug' => $cat['slug'] ?? $key,
                            'description' => $cat['description'] ?? null,
                            'image' => $cat['image'] ?? null,
                            'url' => $cat['url'] ?? null,
                        ];
                    }
                }
            }
        }

        return response()->json(['categories' => array_values($result)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
