<?php

namespace App\Http\Controllers\Api\Games;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryGame;
use App\Models\Game;
use App\Models\GameFavorite;
use App\Models\GameLike;
use App\Models\GamesKey;
use App\Models\Gateway;
use App\Models\Provider;
use App\Models\Wallet;
use App\Traits\Providers\PlayFiverTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GameController extends Controller
{
    use PlayFiverTrait;

    /**
     * @dev  
     * Display a listing of the resource.
     */
    public function index()
    {
        $providers = Provider::with(['games', 'games.provider'])
            ->whereHas('games')
            ->orderBy('name', 'desc')
            ->where('status', 1)
            ->get();

        return response()->json(['providers' =>$providers]);
    }
    public function gamesCategories()
    {
        $categories = Category::all();
        $games = [];
    
        foreach ($categories as $category) {
            $categoryGames = CategoryGame::where("category_id", $category->id)->get();
    
            if ($categoryGames->isNotEmpty()) {
                $numIterations = min(12, $categoryGames->count());
    
                for ($i = 0; $i < $numIterations; $i++) {
                    $game = Game::where("id", $categoryGames[$i]->game_id)->where("status", 1)->first();
    
                    if ($game != null) {
                       
                        $games[$category->name]['games'][$game->game_name] = $game;
                    }
                }
                
                $games[$category->name]["quantidade"] = CategoryGame::where("category_id", $category->id)->whereHas("game",function (Builder $query) {
                    $query->where("status",1);
                })->count();
                $games[$category->name]["pagina"] = 1;
                if($games[$category->name]["quantidade"] <= 12){
                    $games[$category->name]["UPagina"] = 1;
                }
                $games[$category->name]["quantidadeA"] = count($games[$category->name]['games']);

            }
        }
    
        return response()->json(['games' => $games]);
    }
    /**
     * @dev  
     * @return \Illuminate\Http\JsonResponse
     */
    public function featured()
    {
        $featured_games = Game::with(['provider'])->where('is_featured', 1)->get();
        return response()->json(['featured_games' => $featured_games]);
    }

    /**
     * Source Provider
     *
     * @dev  
     * @param Request $request
     * @param $token
     * @param $action
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function sourceProvider(Request $request, $token, $action)
    {
        $tokenOpen = \Helper::DecToken($token);
        $validEndpoints = ['session', 'icons', 'spin', 'freenum'];

        if (in_array($action, $validEndpoints)) {
            if(isset($tokenOpen['status']) && $tokenOpen['status'])
            {
                $game = Game::whereStatus(1)->where('game_code', $tokenOpen['game'])->first();
                if(!empty($game)) {
                    $controller = \Helper::createController($game->game_code);

                    switch ($action) {
                        case 'session':
                            return $controller->session($token);
                        case 'spin':
                            return $controller->spin($request, $token);
                        case 'freenum':
                            return $controller->freenum($request, $token);
                        case 'icons':
                            return $controller->icons();
                    }
                }
            }
        } else {
            return response()->json([], 500);
        }
    }

    /**
     * @dev  
     * Store a newly created resource in storage.
     */
    public function toggleFavorite($id)
    {
        if(auth('api')->check()) {
            $checkExist = GameFavorite::where('user_id', auth('api')->id())->where('game_id', $id)->first();
            if(!empty($checkExist)) {
                if($checkExist->delete()) {
                    return response()->json(['status' => true, 'message' => 'Removido com sucesso']);
                }
            }else{
                $gameFavoriteCreate = GameFavorite::create([
                    'user_id' => auth('api')->id(),
                    'game_id' => $id
                ]);

                if($gameFavoriteCreate) {
                    return response()->json(['status' => true, 'message' => 'Criado com sucesso']);
                }
            }
        }
    }

    /**
     * @dev  
     * Store a newly created resource in storage.
     */
    public function toggleLike($id)
    {
        if(auth('api')->check()) {
            $checkExist = GameLike::where('user_id', auth('api')->id())->where('game_id', $id)->first();
            if(!empty($checkExist)) {
                if($checkExist->delete()) {
                    return response()->json(['status' => true, 'message' => 'Removido com sucesso']);
                }
            }else{
                $gameLikeCreate = GameLike::create([
                    'user_id' => auth('api')->id(),
                    'game_id' => $id
                ]);

                if($gameLikeCreate) {
                    return response()->json(['status' => true, 'message' => 'Criado com sucesso']);
                }
            }
        }
    }

    /**
     * @dev  
     * Display the specified resource.
     */
    public function show(string $id)
    {
       
        $game = Game::with(['categories', 'provider'])->whereStatus(1)->find($id);
        if(!empty($game)) {

            if(Auth::guard("api")->check()) {

                $wallet = Wallet::where('user_id', auth('api')->id())->first();
                if($wallet->total_balance > 0) {
                    $game->increment('views');

                    $token = \Helper::MakeToken([
                        'id' => auth('api')->id(),
                        'game' => $game->game_code
                    ]);

                    switch ($game->distribution) {
                        
                        case 'play_fiver':
                            $playfiver = self::playFiverLaunch($game->game_id, $game->only_demo);
                            
                            if(isset($playfiver['launch_url'])) {
                                return response()->json([
                                    'game' => $game,
                                    'gameUrl' => $playfiver['launch_url'],
                                    'token' => $token
                                ]);
                            }
                        
                            return response()->json(['error' => $playfiver, 'status' => false ], 400);

                    }
                }
                return response()->json(['error' => 'Você precisa ter saldo para jogar', 'status' => false, 'action' => 'deposit' ], 200);
            }else{
                return response()->json(['error' => 'Você precisa tá autenticado para jogar', 'status' => false ], 400);

            }
        }
        return response()->json(['error' => '', 'status' => false ], 500);
    }

    /**
     * @dev  
     * Show the form for editing the specified resource.
     */
    public function allGames(Request $request)
    {
        // Always return a flat array of games for the frontend
        // The built frontend expects an array, not a paginator object
        try {
            $paginator = $this->buildGamePaginator($request);
            $items = method_exists($paginator, 'items') ? $paginator->items() : [];

            if (!empty($items)) {
                return response()->json($items);
            }

            Log::info('Game catalog empty, falling back to static dataset.');
        } catch (\Throwable $throwable) {
            Log::warning('Database game catalog unavailable, using fallback dataset.', [
                'exception' => $throwable->getMessage(),
            ]);
        }

        $fallback = $this->buildFallbackGames($request);
        $games = $fallback['games'] ?? [];

        if ($games instanceof LengthAwarePaginator) {
            return response()->json($games->items());
        }

        if (is_array($games)) {
            return response()->json($games);
        }

        return response()->json([]);
    }

    private function buildGamePaginator(Request $request): LengthAwarePaginator
    {
        $query = Game::query()->with(['provider', 'categories']);

        if ($request->filled('provider') && $request->provider !== 'all') {
            $query->where('provider_id', $request->provider);
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->whereHas('categories', function (Builder $categoryQuery) use ($request) {
                $categoryQuery->where('slug', $request->category);
            });
        }

        if ($request->filled('searchTerm') && strlen($request->searchTerm) > 2) {
            $query->whereLike(['game_code', 'game_name', 'distribution', 'provider.name'], $request->searchTerm);
        } else {
            $query->orderByDesc('views');
        }

        $perPage = max((int) $request->get('per_page', 12), 1);

        return $query
            ->where('status', 1)
            ->paginate($perPage)
            ->appends($request->query());
    }

    private function buildFallbackGames(Request $request): array
    {
        $path = resource_path('data/casino-games.json');

        if (!File::exists($path)) {
            return [
                'games' => new LengthAwarePaginator([], 0, 12, 1),
                'fallback' => true,
            ];
        }

        $payload = json_decode(File::get($path), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid fallback casino-games.json', [
                'error' => json_last_error_msg(),
            ]);

            return [
                'games' => new LengthAwarePaginator([], 0, 12, 1),
                'fallback' => true,
            ];
        }

        $games = collect($payload['games']['data'] ?? $payload['games'] ?? []);

        $games = $this->filterFallbackGames($games, $request);

        $perPage = max((int) $request->get('per_page', 12), 1);
        $currentPage = max((int) $request->get('page', 1), 1);
        $total = $games->count();
        $slice = $games->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $slice,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
                'query' => $request->query(),
            ]
        );

        return [
            'games' => $paginator,
            'fallback' => true,
            'total' => $total,
        ];
    }

    private function filterFallbackGames(Collection $games, Request $request): Collection
    {
        if ($request->filled('provider') && $request->provider !== 'all') {
            $providerFilter = strtolower((string) $request->provider);

            $games = $games->filter(function (array $game) use ($providerFilter) {
                $provider = $game['provider'] ?? [];

                return strtolower((string) ($game['provider_id'] ?? '')) === $providerFilter
                    || strtolower((string) ($provider['code'] ?? '')) === $providerFilter
                    || strtolower((string) ($provider['name'] ?? '')) === $providerFilter;
            });
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $categoryFilter = Str::slug($request->category);

            $games = $games->filter(function (array $game) use ($categoryFilter) {
                $categories = collect($game['categories'] ?? []);

                return $categories->contains(function ($category) use ($categoryFilter) {
                    $slug = Str::slug($category['slug'] ?? $category['name'] ?? '');
                    return $slug === $categoryFilter;
                });
            });
        }

        if ($request->filled('searchTerm') && strlen($request->searchTerm) > 2) {
            $term = mb_strtolower($request->searchTerm);

            $games = $games->filter(function (array $game) use ($term) {
                $fields = [
                    $game['game_code'] ?? '',
                    $game['game_name'] ?? '',
                    $game['distribution'] ?? '',
                    $game['provider']['name'] ?? '',
                ];

                foreach ($fields as $field) {
                    if ($field && str_contains(mb_strtolower((string) $field), $term)) {
                        return true;
                    }
                }

                return false;
            });
        }

        return $games->values();
    }

    
    /**
     * @dev isaacroque5
     * Integrando com a API do PlayFiver
     */
    public function webhookPlayFiver(Request $request)
    {
        return self::webhookPlayFiverAPI($request);
    }


    /**
     * @dev  
     * Update the specified resource in storage.
     */
    public function webhookMoneyCallbackMethod(Request $request)
    {
       
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
