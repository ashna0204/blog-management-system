<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Blog;
use App\Models\Like;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;


class BlogController extends Controller
{
    /**
     * Display a listing of the blog.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Blog::with('user', 'likes');

        //Add is_liked
         if ($user) {
        $query->withExists(['likes as is_liked' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }]);
        }

        // Search filter
        if($request->has('search') && !empty($request->search)){
            $searchTerm = $request->search;

            $query->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('user',function($query) use ($searchTerm){
                        $query->where('email', '=', $searchTerm );
                    });
        }


        // Ordering filters
        if($request->has('filter')){
            switch($request->filter){
                case 'most_liked':
                    $query->withCount('likes')->orderByDesc('likes_count');
                    break;
                case 'latest':
                    $query->orderByDesc('created_at');
                    break;
                default:
                // Default ordering if no valid filter is provided
                    $query->orderByDesc('created_at');
                    break;
            }
        }else{
             // Default ordering if no filter is specified
            $query->orderByDesc('created_at');
        }

        $blogs = $query->paginate(5);



        return response()->json([
            'message' => 'blogs retreived successfully',
            'data' => $blogs
        ],200);
    }

    /**
     * Store a newly created blog in storage.
     */
     public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255|string',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg|max:2048'
        ]);

        $imagePath= null;
        if($request->hasFile('image')){
            $imagePath =  $request->file('image')->store('blog_images', 'public');
        }

        $blog = Auth::user()->blogs()->create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath
        ]);

        return response()->json([
            'message' => "Blog created successfully",
            'data' =>  $blog
        ],201);
    }


    /**
     * Display the specified blog.
     */
    public function show(Blog $blog)
    {
        $blog->load('user','likes');
        return response()->json([
            'data' => $blog
        ]);

    }

     /**
     * Update the specified blog in storage.
     */
   public function update(Request $request, Blog $blog)
    {

        // Ensure the logged-in user owns the blog
        if ($request->user()->id !== $blog->user_id) { 
            return response()->json(['message' => 'Unauthorized to update this blog.'], 403);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
       

        $imagePath = $blog->image;
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('blog_images', 'public');
        }

        // Prepare an array for update, only including fields that are present in the request
        $updatedData=[];

        if($request->has('title')){
            $updatedData['title'] = $request->title;
        }

        if($request->has('description')){
            $updatedData['description'] = $request->description;
        }

        $updatedData['image'] = $imagePath;
        if(empty($updatedData) && !$request->hasFile('image')){
            return response()->json(['message'=> 'No fields provided for update']);
        }

        $blog->update($updatedData);

        return response()->json([
            'message' => 'Blog updated successfully',
            'blog' => $blog,
        ]);
    }


   
    /**
     * Remove the specified blog from storage.
     */
    public function destroy(Blog $blog)
    {
        if(Auth::user()->id !== $blog->user_id){
            return response()->json(['message' => 'Unauthorized to delete this blog.']);
        }

        if($blog->image && Storage::disk('public')->exists($blog->image)){
            Storage::disk('public')->delete($blog->image);
        }

        $blog->delete();

        return response()->json([
            'message' => 'Blog deleted successfully'
        ]);
    }

    /**
     * Toggle like status for a blog.
     * */
    public function likeToggle(Blog $blog)
    {
        $user = Auth::user();

        $like = $blog->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete(); // Unlike
            return response()->json([
                'message' => 'Blog unliked successfully',
                'liked' => false,
                'likes_count' => $blog->likes()->count(),
            ]);
        } else {
            $blog->likes()->create(['user_id' => $user->id]); // Like
            return response()->json([
                'message' => 'Blog liked successfully',
                'liked' => true,
                'likes_count' => $blog->likes()->count(),
            ]);
        }
    }
}
