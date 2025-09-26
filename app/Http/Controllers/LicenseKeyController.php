<?php

namespace App\Http\Controllers;

use App\Models\LicenseKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LicenseKeyController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user() || !Auth::user()->hasRole('admin')) {
                abort(403, 'Acesso negado');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $keys = LicenseKey::all();
        return view('filament.pages.license-key-page', compact('keys'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:191',
            'client_id' => 'required|string|max:191',
            'endpoint_base' => 'required|string|max:191',
            'timeout' => 'required|integer|min:1',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        LicenseKey::create($request->all());
        return redirect()->route('license.index')->with('success', 'Chave adicionada com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:191',
            'client_id' => 'required|string|max:191',
            'endpoint_base' => 'required|string|max:191',
            'timeout' => 'required|integer|min:1',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $key = LicenseKey::findOrFail($id);
        $key->update($request->all());
        return redirect()->route('license.index')->with('success', 'Chave atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $key = LicenseKey::findOrFail($id);
        $key->delete();
        return redirect()->route('license.index')->with('success', 'Chave removida com sucesso!');
    }
} 