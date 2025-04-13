<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of the blog posts.
     */
    public function index()
    {
        $posts = BlogPost::with(['author', 'category'])
                        ->published()
                        ->orderBy('published_at', 'desc')
                        ->paginate(10);
        
        $categories = BlogCategory::withCount('posts')->get();
        
        return view('blog.index', compact('posts', 'categories'));
    }
    
    /**
     * Display the specified blog post.
     */
    public function show($slug)
    {
        $post = BlogPost::with(['author', 'category'])
                        ->published()
                        ->where('slug', $slug)
                        ->firstOrFail();
        
        $relatedPosts = BlogPost::with(['author', 'category'])
                                ->published()
                                ->where('blog_category_id', $post->blog_category_id)
                                ->where('id', '!=', $post->id)
                                ->limit(3)
                                ->get();
        
        return view('blog.show', compact('post', 'relatedPosts'));
    }
    
    /**
     * Display posts by category.
     */
    public function category($slug)
    {
        $category = BlogCategory::where('slug', $slug)->firstOrFail();
        
        $posts = BlogPost::with(['author', 'category'])
                        ->published()
                        ->where('blog_category_id', $category->id)
                        ->orderBy('published_at', 'desc')
                        ->paginate(10);
        
        return view('blog.category', compact('category', 'posts'));
    }
}
