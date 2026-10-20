<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Manga;
use App\Models\Publisher;
use Illuminate\Http\Request;

class MangaController extends Controller
{
    /**
     * Muestra el catálogo de mangas con filtros, ordenamiento y destacados.
     */
    public function index(Request $request)
    {
        $query = Manga::with(['category', 'publisher'])->inStock();

        // Filtro por búsqueda de texto
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        // Filtro por categoría / demografía
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        // Filtro por editorial
        if ($request->filled('publisher')) {
            $query->whereHas('publisher', function ($q) use ($request) {
                $q->where('slug', $request->input('publisher'));
            });
        }

        // Ordenamiento
        switch ($request->input('sort')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $mangas = $query->paginate(8)->withQueryString();
        $featuredMangas = Manga::with(['category', 'publisher'])->featured()->inStock()->take(3)->get();
        $categories = Category::withCount('mangas')->get();
        $publishers = Publisher::withCount('mangas')->get();

        return view('mangas.index', compact('mangas', 'featuredMangas', 'categories', 'publishers'));
    }

    /**
     * Muestra la vista de detalle de un tomo con su ficha técnica y mangas relacionados.
     */
    public function show(string $slug)
    {
        $manga = Manga::with(['category', 'publisher'])->where('slug', $slug)->firstOrFail();

        $relatedMangas = Manga::with(['category', 'publisher'])
            ->where('category_id', $manga->category_id)
            ->where('id', '!=', $manga->id)
            ->inStock()
            ->take(4)
            ->get();

        return view('mangas.show', compact('manga', 'relatedMangas'));
    }
}
