<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreItemController extends Controller
{
    /**
     * Mostrar la lista de items de la tienda
     */
    public function index()
    {
        $items = StoreItem::orderBy('category')->orderBy('name')->get();
        
        return view('admin.store-items.index', [
            'items' => $items
        ]);
    }
    
    /**
     * Mostrar el formulario para crear un nuevo item
     */
    public function create()
    {
        return view('admin.store-items.create');
    }
    
    /**
     * Almacenar un nuevo item en la base de datos
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:avatar,badge,rank,skin,special',
            'type' => 'required|string|max:50',
            'price' => 'required|integer|min:0',
            'level_required' => 'required|integer|min:1',
            'is_limited' => 'boolean',
            'stock' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
            'effects' => 'nullable|json',
        ]);
        
        // Generar slug único
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        
        while (StoreItem::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Procesar la imagen si se ha subido
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('store-items', 'public');
            $imagePath = 'storage/' . $imagePath;
        }
        
        // Crear el item
        $item = new StoreItem();
        $item->name = $validated['name'];
        $item->slug = $slug;
        $item->description = $validated['description'];
        $item->image_path = $imagePath;
        $item->category = $validated['category'];
        $item->type = $validated['type']; 
        $item->price = $validated['price'];
        $item->level_required = $validated['level_required'];
        $item->is_limited = $request->has('is_limited');
        $item->stock = $request->has('is_limited') ? $validated['stock'] : null;
        $item->effects = $validated['effects'] ?? null;
        $item->is_active = $request->has('is_active');
        $item->save();
        
        return redirect()->route('admin.store-items.index')
            ->with('success', 'Item creado correctamente');
    }
    
    /**
     * Mostrar el formulario para editar un item existente
     */
    public function edit(StoreItem $item)
    {
        return view('admin.store-items.edit', [
            'item' => $item
        ]);
    }
    
    /**
     * Actualizar un item existente en la base de datos
     */
    public function update(Request $request, StoreItem $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:avatar,badge,rank,skin,special',
            'type' => 'required|string|max:50',
            'price' => 'required|integer|min:0',
            'level_required' => 'required|integer|min:1',
            'is_limited' => 'boolean',
            'stock' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
            'effects' => 'nullable|json',
        ]);
        
        // Procesar la imagen si se ha subido una nueva
        if ($request->hasFile('image')) {
            // Eliminar la imagen anterior si existe
            if ($item->image_path && file_exists(public_path($item->image_path))) {
                unlink(public_path($item->image_path));
            }
            
            $imagePath = $request->file('image')->store('store-items', 'public');
            $item->image_path = 'storage/' . $imagePath;
        }
        
        // Actualizar el item
        $item->name = $validated['name'];
        // No actualizamos el slug para mantener URLs consistentes
        $item->description = $validated['description'];
        $item->category = $validated['category'];
        $item->type = $validated['type'];
        $item->price = $validated['price'];
        $item->level_required = $validated['level_required'];
        $item->is_limited = $request->has('is_limited');
        $item->stock = $request->has('is_limited') ? $validated['stock'] : null;
        $item->effects = $validated['effects'] ?? null;
        $item->is_active = $request->has('is_active');
        $item->save();
        
        return redirect()->route('admin.store-items.index')
            ->with('success', 'Item actualizado correctamente');
    }
    
    /**
     * Eliminar un item de la tienda
     */
    public function destroy(StoreItem $item)
    {
        // Verificar si el item está siendo utilizado antes de eliminarlo
        // ... (código para verificar si hay usuarios que han comprado el item)
        
        // Eliminar la imagen si existe
        if ($item->image_path && file_exists(public_path($item->image_path))) {
            unlink(public_path($item->image_path));
        }
        
        $item->delete();
        
        return redirect()->route('admin.store-items.index')
            ->with('success', 'Item eliminado correctamente');
    }
} 