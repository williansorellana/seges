<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
{
    /**
 * Lista y filtra las categorías de activos.
 */
    public function index(Request $request)
    {
        $query = AssetCategory::query();

        // Filtrar categorías por nombre.
        if ($request->filled('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        // Filtrar por tipo de categoría.
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $categories = $query->orderBy('tipo')->orderBy('nombre')->paginate(10);

        $totalHardware = AssetCategory::where('tipo', 'hardware')->count();
        $totalSoftware = AssetCategory::where('tipo', 'software')->count();

        return view('assets.categories.index', compact(
            'categories',
            'totalHardware',
            'totalSoftware'
        ));
    }

    /**
 * Registra una nueva categoría de activo.
 */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:asset_categories,nombre',
            'tipo' => 'required|in:hardware,software',
            'descripcion' => 'nullable|string',
        ]);

        AssetCategory::create($request->only('nombre', 'tipo', 'descripcion'));

        return redirect()->route('asset-categories.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    /**
 * Actualiza una categoría existente.
 */
    public function update(Request $request, AssetCategory $assetCategory)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:asset_categories,nombre,' . $assetCategory->id,
            'tipo' => 'required|in:hardware,software',
            'descripcion' => 'nullable|string',
        ]);

        $assetCategory->update($request->only('nombre', 'tipo', 'descripcion'));

        return redirect()->route('asset-categories.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    /**
 * Elimina una categoría si no tiene activos asociados.
 */
    public function destroy(AssetCategory $assetCategory)
    {
        if ($assetCategory->assets()->exists()) {
            return redirect()->route('asset-categories.index')
                ->with('error', 'No se puede eliminar una categoría que tiene activos asociados.');
        }

        $assetCategory->delete();

        return redirect()->route('asset-categories.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}