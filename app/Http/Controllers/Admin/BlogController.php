<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    /**
     * Display a listing of the posts.
     */
    public function index()
    {
        $posts = BlogPost::with(['author', 'category'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);
        
        return view('admin.blog.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        $categories = BlogCategory::all();
        return view('admin.blog.create', compact('categories'));
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'excerpt' => 'nullable|string',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($request->title);
        
        // Si se marca como publicado, establecer la fecha de publicación
        if ($request->has('is_published') && $request->is_published) {
            $data['published_at'] = now();
        }

        // Procesar imagen destacada si se proporciona
        if ($request->hasFile('featured_image')) {
            $imagePath = $request->file('featured_image')->store('blog', 'public');
            $data['featured_image'] = $imagePath;
        }

        BlogPost::create($data);

        return redirect()->route('admin.blog.index')
                         ->with('success', 'El artículo ha sido creado exitosamente.');
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(BlogPost $post)
    {
        $categories = BlogCategory::all();
        return view('admin.blog.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified post in storage.
     */
    public function update(Request $request, BlogPost $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'excerpt' => 'nullable|string',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean',
        ]);

        $data = $request->all();
        
        // Actualizar slug solo si el título cambió
        if ($post->title != $request->title) {
            $data['slug'] = Str::slug($request->title);
        }
        
        // Gestionar el estado de publicación
        if ($request->has('is_published')) {
            if ($request->is_published && !$post->is_published) {
                // Si se está publicando por primera vez
                $data['published_at'] = now();
            } elseif (!$request->is_published) {
                // Si se está despublicando
                $data['published_at'] = null;
            }
        }

        // Procesar imagen destacada si se proporciona
        if ($request->hasFile('featured_image')) {
            // Eliminar la imagen anterior si existe
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            
            $imagePath = $request->file('featured_image')->store('blog', 'public');
            $data['featured_image'] = $imagePath;
        }

        $post->update($data);

        return redirect()->route('admin.blog.index')
                         ->with('success', 'El artículo ha sido actualizado exitosamente.');
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(BlogPost $post)
    {
        // Eliminar la imagen destacada si existe
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        
        $post->delete();

        return redirect()->route('admin.blog.index')
                         ->with('success', 'El artículo ha sido eliminado exitosamente.');
    }

    /**
     * Display categories management page.
     */
    public function categories()
    {
        $categories = BlogCategory::withCount('posts')->get();
        return view('admin.blog.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function createCategory()
    {
        return view('admin.blog.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        BlogCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()->route('admin.blog.categories')
                         ->with('success', 'La categoría ha sido creada exitosamente.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function editCategory(BlogCategory $category)
    {
        return view('admin.blog.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function updateCategory(Request $request, BlogCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $data = [
            'name' => $request->name,
            'description' => $request->description,
        ];
        
        // Actualizar slug solo si el nombre cambió
        if ($category->name != $request->name) {
            $data['slug'] = Str::slug($request->name);
        }

        $category->update($data);

        return redirect()->route('admin.blog.categories')
                         ->with('success', 'La categoría ha sido actualizada exitosamente.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroyCategory(BlogCategory $category)
    {
        // Verificar si la categoría tiene posts asociados
        if ($category->posts()->count() > 0) {
            return redirect()->route('admin.blog.categories')
                             ->with('error', 'No se puede eliminar esta categoría porque tiene artículos asociados.');
        }
        
        $category->delete();

        return redirect()->route('admin.blog.categories')
                         ->with('success', 'La categoría ha sido eliminada exitosamente.');
    }
}
