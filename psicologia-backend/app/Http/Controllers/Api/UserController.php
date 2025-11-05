<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Listar todos los usuarios
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('rol')) {
            $query->where('rol', $request->rol);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        $users = $query->orderBy('name', 'asc')->get();

        return response()->json([
            'users' => $users
        ], 200);
    }

    /**
     * Crear un nuevo usuario
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'rol' => $validated['rol'],
            'estado' => true,
        ]);

        return response()->json([
            'message' => 'Usuario creado exitosamente',
            'user' => $user
        ], 201);
    }

    /**
     * Mostrar un usuario específico
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'user' => $user
        ], 200);
    }

    /**
     * Actualizar un usuario
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Usuario actualizado exitosamente',
            'user' => $user
        ], 200);
    }

    /**
     * Eliminar un usuario
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // TODO: Activar cuando se implemente autenticación
        // if ($user->id === auth()->user()->id) {
        //     return response()->json([
        //         'message' => 'No puedes eliminar tu propia cuenta'
        //     ], 400);
        // }

        $user->delete();

        return response()->json([
            'message' => 'Usuario eliminado exitosamente'
        ], 200);
    }

    /**
     * Activar/Desactivar usuario
     */
    public function toggleEstado(string $id)
    {
        $user = User::findOrFail($id);

        // TODO: Activar cuando se implemente autenticación
        // if ($user->id === auth()->user()->id) {
        //     return response()->json([
        //         'message' => 'No puedes desactivar tu propia cuenta'
        //     ], 400);
        // }

        $user->update(['estado' => !$user->estado]);

        return response()->json([
            'message' => $user->estado ? 'Usuario activado' : 'Usuario desactivado',
            'user' => $user
        ], 200);
    }

    /**
     * Obtener tutores disponibles
     */
    public function tutores()
    {
        $tutores = User::where('rol', 'TUTOR')
            ->where('estado', true)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'email']);

        return response()->json([
            'tutores' => $tutores
        ], 200);
    }

    /**
     * Obtener psicólogos disponibles
     */
    public function psicologos()
    {
        $psicologos = User::where('rol', 'PSICOLOGO')
            ->where('estado', true)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'email']);

        return response()->json([
            'psicologos' => $psicologos
        ], 200);
    }

    /**
     * Cambiar contraseña del usuario
     */
    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id', // TODO: Eliminar cuando se use auth
            'password_actual' => 'required',
            'password_nuevo' => 'required|min:6|confirmed',
        ], [
            'user_id.required' => 'El ID de usuario es obligatorio.',
            'password_actual.required' => 'La contraseña actual es obligatoria.',
            'password_nuevo.required' => 'La nueva contraseña es obligatoria.',
            'password_nuevo.min' => 'La nueva contraseña debe tener al menos 6 caracteres.',
            'password_nuevo.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        // TODO: Cambiar por $request->user() cuando se use auth
        $user = User::findOrFail($request->user_id);

        if (!Hash::check($request->password_actual, $user->password)) {
            return response()->json([
                'message' => 'La contraseña actual es incorrecta'
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->password_nuevo)
        ]);

        return response()->json([
            'message' => 'Contraseña actualizada exitosamente'
        ], 200);
    }
}
