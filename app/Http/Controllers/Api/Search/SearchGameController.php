<?php

namespace App\Http\Controllers\Api\Search;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

class SearchGameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Game::query()->with(['provider']);
    
            if ($request->has('searchTerm') && strlen($request->input('searchTerm')) > 2) {
                $query->where('game_name', 'like', '%'.$request->searchTerm.'%');
            }
    
            $query->where('status', 1);
    
            $games = $query->orderBy('views', 'desc')->paginate(12);
    
            return response()->json(['games' => $games]);
    
        } catch (\Exception $e) {
            \Log::error('Error fetching games: '.$e->getMessage());
            return response()->json(['error' => 'Server Error'], 500);
        }
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Implementação futura
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Implementação futura
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Implementação futura
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Implementação futura
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Implementação futura
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Implementação futura
    }
}
