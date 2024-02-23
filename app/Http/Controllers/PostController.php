<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{

    protected $model;

    public function __construct(Post $model)
    {
        $this->model = $model;
    }

    //create posts
    public function store(PostRequest $request){
        $postData = $this->changePostDataToArray($request);

        $postData = $this->model->create($postData);
        $responseData = [
            'postData' => $postData,
            'message' => 'Post Created Successfully'
        ];

        return response()->json($responseData,200);
    }

    //get posts
    public function index(){
        $posts = $this->model->with('owner:id,name')->with('comments')->get();

        return response()->json($posts, 200);
    }

    //get detail post
    public function show($id){
        $post = $this->model->with('comments')->find($id);

        if($post){
            return response()->json($post, 200);
        }

        return response()->json([
            'message' => 'Post not found'
        ]);
    }

    //delete post
    public function destroy($id){
        $post = $this->model->find($id);

        if(!$post) {
            return  response()->json([
                'message' => 'Post not found'
            ]);
        }

        if (Gate::denies('auth-post', $post)) {
            return response()->json([
                'message' => 'not allowed'
            ]);
        }

        $post->delete();

        return response()->json([
            'message' => 'Post has been deleted'
        ]);
    }

    //update post
    public function update(PostRequest $request){

        $post = $this->model->find($request->id);

        if(!$post){
            return response()->json([
                'message' => 'Post not found'
            ]);
        }

        if (Gate::denies('auth-post', $post)) {
            return response()->json([
                'message' => 'not allowed'
            ]);
        }

            $updateData = $this->changeUpdatePostDataToArray($request);

            $post->update($updateData);
            return response()->json([
                'message' => 'Post has been updated'
            ]);
    }

    //change update postdata to array
    private function changeUpdatePostDataToArray($request){
        return [
            'title' => $request->title,
            'body' => $request->body
        ];
    }

    //change postdata to array
    private function changePostDataToArray($request){
        return [
            'user_id' => Auth::user()->id,
            'title' => $request->title,
            'body' => $request->body,
        ];
    }

}
