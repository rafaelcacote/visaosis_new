<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $tenant = session('tenant');
        $location = session('location');
        $profiles = AuthHelper::profiles();
        $userLocations = $this->getUserLocations($user->id);

        return view('profile.show', compact('user', 'tenant', 'location', 'profiles', 'userLocations'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $existingUser = User::where('email', $request->email)
            ->where('id', '!=', $user->id)
            ->first();

        if ($existingUser) {
            return back()->withErrors(['email' => 'Este email já está em uso.'])->withInput();
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            return redirect()->route('profile.show')
                ->with('success', 'Perfil atualizado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar perfil: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Erro ao atualizar perfil.'])->withInput();
        }
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'A senha atual é obrigatória.',
            'password.required' => 'A nova senha é obrigatória.',
            'password.min' => 'A nova senha deve ter no mínimo 6 caracteres.',
            'password.confirmed' => 'As senhas não conferem.',
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'A senha atual está incorreta.']);
        }

        try {
            $user->password = Hash::make($request->password);
            $user->save();

            return back()->with('success', 'Senha alterada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao alterar senha do perfil: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Erro ao alterar senha.']);
        }
    }

    private function getUserLocations(int $userId)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocationsSession = session('user_locations', []);

        $locationIds = [];
        if ($tenantId) {
            $locationIds = collect($userLocationsSession)
                ->where('tenant_id', $tenantId)
                ->pluck('location_id')
                ->toArray();
        } elseif ($locationId) {
            $locationIds = [$locationId];
        }

        try {
            return DB::connection('cerberus')
                ->table('seguranca.user_locations')
                ->join('seguranca.locations', 'user_locations.location_id', '=', 'locations.id')
                ->where('user_locations.user_id', $userId)
                ->when(! empty($locationIds), function ($query) use ($locationIds) {
                    return $query->whereIn('user_locations.location_id', $locationIds);
                })
                ->where('user_locations.status', 1)
                ->select('locations.id', 'locations.name', 'locations.short_name')
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }
}
