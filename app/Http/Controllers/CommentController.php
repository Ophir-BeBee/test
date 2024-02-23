<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\CommentRequest;

class CommentController extends Controller
{

    protected $model;

    public function __construct(Comment $model)
    {
        $this->model = $model;
    }

    //create comments
    public function store(CommentRequest $request){
        $commentData = $this->changeCommentDataToArray($request);

        $post = Post::find($request->post_id);

        if(!$post){
            return response()->json([
                'message' => 'Post not found'
            ]);
        }

        $commentData = $this->model->create($commentData);

        $responseData = [
            'commentData' => $commentData,
            'message' => 'Comment uploaded'
        ];
        return response()->json($responseData,200);
    }

    //delete commets
    public function destroy($id){
        $comment = $this->model->with('post')->find($id);

        if(!$comment){
            return response()->json([
                'message' => 'Comment not found'
            ]);
        }


        if(Gate::denies('comment-delete',$comment)){
            return response()->json([
                'message' => 'Not allowed'
            ]);
        }

        $comment->delete();
        return response()->json([
            'message' => 'Comment deleted'
        ]);
    }

    //update comment
    public function update(CommentRequest $request){
        $comment = $this->model->find($request->comment_id);

        if(!$comment){
            return response()->json([
                'message' => 'Comment not found'
            ]);
        }

        if(Gate::denies('comment-update',$comment)){
            return response()->json([
                'message' => 'Not allowed'
            ]);
        }

        $updateCommentData = $this->changeUpdateCommentDataToArray($request);

        $comment->update($updateCommentData);
        return response()->json([
            "data" => $comment,
            'message' => 'Comment updated'
        ]);
    }



    //change update commentdata to array
    private function changeUpdateCommentDataToArray($request){
        return [
            'text' => $request->text
        ];
    }

    //change create commentdata to array
    private function changeCommentDataToArray($request){
        return [
            'user_id' => Auth::user()->id,
            'post_id' => $request->post_id,
            'text' => $request->text
        ];
    }
}
