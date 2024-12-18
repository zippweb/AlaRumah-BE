<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        return $data = Post::all();
        if ($data->count() > 0) {
            return response()->json([
                'status' => 200,
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "Data empty"
            ], 404);
        }
    }

    public function show($id)
    {
        $data = Post::where('id', $id)->first();
        if (!$data) {
            return response()->json([
                'status' => 404,
                'message' => "Data not found"
            ], 404);
        } else {
            return response()->json([
                'status' => 200,
                'data' => $data
            ], 200);
        }
    }

    public function addPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:1000',
            'description' => 'required|max:15000',
            'file_path' => 'file|mimes:jpeg,png,jpg,gif,svg'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->messages()
            ], 400);
        }

        $post = new Post();
        $post->title = $request->input('title');
        $post->description = $request->input('description');

        if ($request->hasFile('file_path') && $request->file('file_path')->isValid()) {
            $post->file_path = $request->file('file_path')->store('public');
        }

        $post->save();

        return response()->json([
            'status' => 200,
            'message' => 'New recipe has been created!',
            'data' => $post
        ], 200);
    }

    public function update(Request $request, int $id)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|max:1000',
            'description' => 'required|string|max:15000',
            'file_path' => 'file|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->messages()
            ], 400);
        }

        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'status' => 404,
                'message' => 'Post not found'
            ], 404);
        }

        $post->title = $request->input('title');
        $post->description = $request->input('description');

        if ($request->hasFile('file_path')) {
            $post->file_path = $request->file('file_path')->store('public');
        }

        $post->save();

        return response()->json([
            'status' => 200,
            'message' => 'Recipe has been updated',
            'data' => $post
        ], 200);
    }

    public function destroy($id)
    {
        $data = Post::find($id);

        if ($data) {
            $data->delete();

            return  response()->json([
                'status' => 200,
                'message' => 'Data has been deleted'
            ], 200);
        } else {
            return  response()->json([
                'status' => 404,
                'message' => 'Data failed to delete'
            ], 404);
        }
    }

    // public function destroyAll(){
    //     return $data = Post::deleteAll();

    // }

    public function search($key)
    {
        return Post::where('title', 'LIKE', "%$key%")->get();
    }
}
