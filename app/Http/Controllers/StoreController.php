<?php

namespace App\Http\Controllers;

use App\Models\StoreItem;
use App\Models\UserInventory;
use App\Models\UserAvatar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    /**
     * Mostrar la página principal de la tienda.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $studentProfile = $user->studentProfile;
        $xpPoints = $studentProfile ? $studentProfile->xp_points : 0;
        
        // Recuperar categorías e items destacados
        $featuredItems = StoreItem::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();
        
        // Recuperar accesorios de avatar para la sección de novedades
        $avatarAccessories = StoreItem::where('is_active', true)
            ->where('category', 'avatar')
            ->where('type', 'accessory')
            ->get();
        
        // Recuperar categorías disponibles
        $categories = [
            'avatar' => 'Avatares',
            'badge' => 'Insignias',
            'rank' => 'Rangos',
            'skin' => 'Temas',
            'special' => 'Especiales',
        ];
        
        // Recuperar inventario del usuario
        $userInventory = UserInventory::where('user_id', $user->user_id)
            ->with('item')
            ->get()
            ->keyBy('item_id');
        
        return view('store.index', [
            'user' => $user,
            'xpPoints' => $xpPoints,
            'featuredItems' => $featuredItems,
            'avatarAccessories' => $avatarAccessories,
            'categories' => $categories,
            'userInventory' => $userInventory,
        ]);
    }
    
    /**
     * Mostrar los items de una categoría específica.
     *
     * @param string $category
     * @return \Illuminate\View\View
     */
    public function category($category)
    {
        // Verificar que la categoría es válida
        $validCategories = ['avatar', 'badge', 'rank', 'skin', 'special'];
        if (!in_array($category, $validCategories)) {
            return redirect()->route('store.index')->with('error', 'Categoría no válida');
        }
        
        $user = Auth::user();
        $studentProfile = $user->studentProfile;
        $xpPoints = $studentProfile ? $studentProfile->xp_points : 0;
        
        // Recuperar los items de esta categoría
        $items = StoreItem::where('category', $category)
            ->where('is_active', true)
            ->orderBy('level_required')
            ->orderBy('price')
            ->get();
        
        // Recuperar inventario del usuario
        $userInventory = UserInventory::where('user_id', $user->user_id)
            ->with('item')
            ->get()
            ->keyBy('item_id');
        
        $categoryNames = [
            'avatar' => 'Avatares',
            'badge' => 'Insignias',
            'rank' => 'Rangos',
            'skin' => 'Temas',
            'special' => 'Especiales',
        ];
        
        return view('store.category', [
            'user' => $user,
            'xpPoints' => $xpPoints,
            'items' => $items,
            'category' => $category,
            'categoryName' => $categoryNames[$category],
            'userInventory' => $userInventory,
        ]);
    }
    
    /**
     * Mostrar detalles de un item específico.
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        $user = Auth::user();
        $studentProfile = $user->studentProfile;
        $xpPoints = $studentProfile ? $studentProfile->xp_points : 0;
        
        // Buscar el item por su slug
        $item = StoreItem::where('slug', $slug)->firstOrFail();
        
        // Verificar si el usuario ya posee este item
        $hasItem = UserInventory::where('user_id', $user->user_id)
            ->where('item_id', $item->item_id)
            ->exists();
        
        // Verificar si el usuario puede comprar el item
        $canPurchase = $item->canBePurchasedBy($user);
        
        return view('store.show', [
            'user' => $user,
            'xpPoints' => $xpPoints,
            'item' => $item,
            'hasItem' => $hasItem,
            'canPurchase' => $canPurchase,
        ]);
    }
    
    /**
     * Procesar la compra de un item.
     *
     * @param Request $request
     * @param int $itemId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function purchase(Request $request, $itemId)
    {
        $user = Auth::user();
        
        // Buscar el item
        $item = StoreItem::findOrFail($itemId);
        
        // Verificar si el usuario puede comprar el item
        if (!$item->canBePurchasedBy($user)) {
            return redirect()->back()->with('error', 'No se pudo completar la compra. Verifica que tienes suficientes puntos XP y el nivel requerido.');
        }
        
        // Reducir los puntos XP del usuario
        if (!$user->studentProfile) {
            return redirect()->back()->with('error', 'No se encontró el perfil del estudiante.');
        }
        
        // Iniciar transacción para asegurar consistencia
        DB::beginTransaction();
        
        try {
            // Reducir los puntos XP del usuario
            $user->studentProfile->xp_points -= $item->price;
            $user->studentProfile->save();
            
            // Reducir el stock si es limitado
            if ($item->is_limited) {
                $item->stock -= 1;
            }
            
            // Incrementar el contador de compras
            $item->purchases = ($item->purchases ?? 0) + 1;
            $item->save();
            
            // Agregar el item al inventario del usuario
            $inventory = UserInventory::create([
                'user_id' => $user->user_id,
                'item_id' => $item->item_id,
                'is_equipped' => false,
                'is_used' => false,
                'acquired_at' => now(),
                'expires_at' => isset($item->duration) ? now()->addDays($item->duration) : null,
            ]);
            
            DB::commit();
            
            return redirect()->route('store.inventory')->with('success', '¡Has adquirido ' . $item->name . ' con éxito!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al procesar la compra: ' . $e->getMessage());
        }
    }
    
    /**
     * Mostrar el inventario del usuario.
     *
     * @return \Illuminate\View\View
     */
    public function inventory()
    {
        $user = Auth::user();
        
        // Recuperar todo el inventario del usuario, agrupado por categoría
        $inventory = UserInventory::where('user_id', $user->user_id)
            ->with('item')
            ->get()
            ->groupBy(function ($item) {
                return $item->item->category;
            });
        
        // Recuperar el avatar del usuario
        $avatar = UserAvatar::getOrCreate($user->user_id);
        
        return view('store.inventory', [
            'user' => $user,
            'inventory' => $inventory,
            'avatar' => $avatar,
            'equippedItems' => UserInventory::getEquippedByUser($user->user_id),
        ]);
    }
    
    /**
     * Equipar un item del inventario.
     *
     * @param Request $request
     * @param int $inventoryId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function equip(Request $request, $inventoryId)
    {
        $user = Auth::user();
        
        // Buscar el item en el inventario
        $inventoryItem = UserInventory::where('inventory_id', $inventoryId)
            ->where('user_id', $user->user_id)
            ->firstOrFail();
        
        // Intentar equipar el item
        if ($inventoryItem->equip()) {
            // Si se trata de un rango, actualizar el rango en el avatar
            if ($inventoryItem->item->category === 'rank') {
                $avatar = UserAvatar::getOrCreate($user->user_id);
                $avatar->updateRank($inventoryItem->item->name);
            }
            
            return redirect()->back()->with('success', 'Item equipado con éxito');
        }
        
        return redirect()->back()->with('error', 'No se pudo equipar el item');
    }
    
    /**
     * Desequipar un item del inventario.
     *
     * @param Request $request
     * @param int $inventoryId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unequip(Request $request, $inventoryId)
    {
        $user = Auth::user();
        
        // Buscar el item en el inventario
        $inventoryItem = UserInventory::where('inventory_id', $inventoryId)
            ->where('user_id', $user->user_id)
            ->firstOrFail();
        
        // Intentar desequipar el item
        if ($inventoryItem->unequip()) {
            return redirect()->back()->with('success', 'Item desequipado con éxito');
        }
        
        return redirect()->back()->with('error', 'No se pudo desequipar el item');
    }
    
    /**
     * Personalizar el avatar del usuario.
     *
     * @return \Illuminate\View\View
     */
    public function avatar()
    {
        $user = Auth::user();
        
        // Recuperar o crear el avatar del usuario
        $avatar = UserAvatar::getOrCreate($user->user_id);
        
        // Recuperar items de avatar disponibles para el usuario
        $avatarItems = UserInventory::where('user_id', $user->user_id)
            ->whereHas('item', function ($query) {
                $query->where('category', 'avatar');
            })
            ->with('item')
            ->get()
            ->groupBy(function ($item) {
                // Agrupar por tipo de elemento
                return $item->item->type;
            });
        
        return view('store.avatar', [
            'user' => $user,
            'avatar' => $avatar,
            'avatarItems' => $avatarItems,
        ]);
    }
    
    /**
     * Actualizar el avatar del usuario.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAvatar(Request $request)
    {
        $user = Auth::user();
        $avatar = UserAvatar::getOrCreate($user->user_id);
        
        // Validar los datos de entrada
        $validated = $request->validate([
            'base_avatar' => 'sometimes|string',
            'skin_color' => 'sometimes|string',
            'hair_style' => 'sometimes|string',
            'hair_color' => 'sometimes|string',
            'eye_type' => 'sometimes|string',
            'eye_color' => 'sometimes|string',
            'mouth_type' => 'sometimes|string',
            'outfit' => 'sometimes|string',
            'accessory' => 'sometimes|nullable|string',
            'background' => 'sometimes|string',
            'frame' => 'sometimes|nullable|string',
        ]);
        
        // Actualizar solo los campos proporcionados
        foreach ($validated as $key => $value) {
            if ($request->has($key)) {
                $avatar->$key = $value;
            }
        }
        
        $avatar->save();
        
        return redirect()->back()->with('success', 'Avatar actualizado con éxito');
    }
}
