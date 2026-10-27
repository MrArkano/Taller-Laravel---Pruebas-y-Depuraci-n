<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexCitaRequest;
use App\Http\Requests\StoreCitaRequest;
use App\Http\Requests\UpdateCitaEstadoRequest;
use App\Http\Resources\CitaResource;
use App\Models\Cita;
use App\Services\CitaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class CitaController extends Controller
{
    /** GET /api/citas?estado= — solo las citas del usuario autenticado. */
    public function index(IndexCitaRequest $request): AnonymousResourceCollection
    {
        $citas = Cita::with(['patient', 'doctor']) // eager loading: evita N+1
            ->visiblePara($request->user())
            ->when($request->validated('estado'), fn ($q, $estado) => $q->where('estado', $estado))
            ->orderBy('fecha_hora')
            ->orderBy('id')
            ->get();

        return CitaResource::collection($citas);
    }

    /** POST /api/citas */
    public function store(StoreCitaRequest $request, CitaService $service): JsonResponse
    {
        $cita = $service->agendar($request->user(), $request->validated());

        return (new CitaResource($cita))->response()->setStatusCode(201);
    }

    /** GET /api/citas/{cita} */
    public function show(Cita $cita): CitaResource
    {
        Gate::authorize('view', $cita);

        return new CitaResource($cita->load(['patient', 'doctor']));
    }

    /** PATCH /api/citas/{cita} */
    public function update(UpdateCitaEstadoRequest $request, Cita $cita): CitaResource
    {
        $estado = $request->validated('estado');

        Gate::authorize('cambiarEstado', [$cita, $estado]);

        $cita->update(['estado' => $estado]);

        return new CitaResource($cita->load(['patient', 'doctor']));
    }
}
