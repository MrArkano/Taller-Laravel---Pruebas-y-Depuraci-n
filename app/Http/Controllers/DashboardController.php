<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function reporteFinanciero() {
        Gate::authorize('ver-finanzas'); // Lanza HTTP 403 de forma automática si no es admin

        return response()->json(['ingresos_totales' => 12500000, 'moneda' => 'COP']);
    }
}
