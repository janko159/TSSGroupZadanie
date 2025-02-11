<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

use function Laravel\Prompts\error;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json([
            'message' => 'nacitanie vsetkych uzivatel zo zoznamu',
            'users' => $users
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'meno' => 'required|string|max:255',
            'vek' => 'required|integer',
            'pohlavie' => 'required|string|max:255'
        ]);

        User::create($data);

        return response()->json([
            'message' => 'Uzivatel bol uspesne pridany do databazy'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'meno' => 'required|string|max:255',
            'vek' => 'required|integer',
            'pohlavie' => 'required|string|max:255'
        ]);

        $user->update($data);

        return response()->json([
            'message' => 'data boli aktualizovane'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return response()->json([
                'message' => 'uzivatel bol odstraneny z databazy'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e
            ]);
        }
    }

    public function filterUser(Request $request)
    {
        $search = $request->query('search');

        if ($search) {
            $users = User::where('meno', 'like', "%{$search}%")
                ->orWhere('vek', 'like', "%{$search}%")
                ->orWhere('pohlavie', 'like', "%{$search}%")
                ->get();
        } else {
            $users = User::all();
        }

        return response()->json($users);
    }
}
