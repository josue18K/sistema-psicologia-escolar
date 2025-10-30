<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use App\Http\Requests\StoreNotificacionRequest;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    /**
     * Listar notificaciones del usuario
     */
    public function index(Request $request)
    {
        // TODO: Filtrar por usuario autenticado cuando se implemente auth
        // ->where('destinatario_id', $request->user()->id)

        $notificaciones = Notificacion::with('remitente:id,name')
            ->orderBy('fecha_envio', 'desc')
            ->get();

        return response()->json([
            'notificaciones' => $notificaciones
        ], 200);
    }

    /**
     * Crear una nueva notificación
     */
    public function store(StoreNotificacionRequest $request)
    {
        $validated = $request->validated();

        // TODO: Cambiar por auth()->id() cuando se implemente auth
        $remitenteId = 1;

        $notificacion = Notificacion::create([
            'remitente_id' => $remitenteId,
            'destinatario_id' => $validated['destinatario_id'],
            'mensaje' => $validated['mensaje'],
            'fecha_envio' => now(),
            'leida' => false,
        ]);

        return response()->json([
            'message' => 'Notificación enviada exitosamente',
            'notificacion' => $notificacion->load('remitente', 'destinatario')
        ], 201);
    }

    /**
     * Mostrar una notificación específica
     */
    public function show(string $id)
    {
        $notificacion = Notificacion::with(['remitente:id,name', 'destinatario:id,name'])
            ->findOrFail($id);

        return response()->json([
            'notificacion' => $notificacion
        ], 200);
    }

    /**
     * Marcar notificación como leída
     */
    public function marcarLeida(string $id)
    {
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->update(['leida' => true]);

        return response()->json([
            'message' => 'Notificación marcada como leída',
            'notificacion' => $notificacion
        ], 200);
    }

    /**
     * Marcar todas como leídas
     */
    public function marcarTodasLeidas(Request $request)
    {
        // TODO: Filtrar por usuario autenticado cuando se implemente auth
        // ->where('destinatario_id', $request->user()->id)

        Notificacion::where('leida', false)
            ->update(['leida' => true]);

        return response()->json([
            'message' => 'Todas las notificaciones han sido marcadas como leídas'
        ], 200);
    }

    /**
     * Eliminar una notificación
     */
    public function destroy(string $id)
    {
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->delete();

        return response()->json([
            'message' => 'Notificación eliminada exitosamente'
        ], 200);
    }

    /**
     * Obtener notificaciones no leídas
     */
    public function noLeidas(Request $request)
    {
        // TODO: Filtrar por usuario autenticado cuando se implemente auth
        // ->where('destinatario_id', $request->user()->id)

        $notificaciones = Notificacion::with('remitente:id,name')
            ->where('leida', false)
            ->orderBy('fecha_envio', 'desc')
            ->get();

        return response()->json([
            'notificaciones' => $notificaciones,
            'total' => $notificaciones->count()
        ], 200);
    }

    /**
     * Notificaciones enviadas por el usuario
     */
    public function enviadas(Request $request)
    {
        // TODO: Filtrar por usuario autenticado cuando se implemente auth
        // ->where('remitente_id', $request->user()->id)

        $notificaciones = Notificacion::with('destinatario:id,name')
            ->orderBy('fecha_envio', 'desc')
            ->get();

        return response()->json([
            'notificaciones' => $notificaciones
        ], 200);
    }
}
