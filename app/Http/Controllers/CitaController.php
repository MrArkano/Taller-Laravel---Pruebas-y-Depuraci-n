<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCitaRequest;
use App\Models\Cita;
use App\Models\User;
use App\Services\CitaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CitaController extends Controller
{
    public function index(Request $request): View
    {
        $citas = Cita::with(['patient', 'doctor']) // eager loading: evita N+1
            ->visiblePara($request->user())
            ->latest('fecha_hora')
            ->get();

        return view('citas.index', ['citas' => $citas]);
    }

    public function create(): View
    {
        Gate::authorize('create', Cita::class);

        return view('citas.create', [
            'medicos' => User::where('rol', User::ROL_MEDICO)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreCitaRequest $request, CitaService $service): RedirectResponse
    {
        $service->agendar($request->user(), $request->validated());

        return redirect()
            ->route('citas.index')
            ->with('success', 'Cita registrada correctamente. Se notificó al médico por correo.');
    }
}
