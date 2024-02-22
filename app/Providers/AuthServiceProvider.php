<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('auth-post', function (User $user, Post $post) {
            return $user->id === $post->user_id;
        });

        Gate::define('comment-update',function(User $user,Comment $comment){
            return $user->id === $comment->user_id;
        });

        Gate::define('comment-delete',function(User $user,Comment $comment){

            if($user->id === $comment->user_id || $user->id === $comment->post->user_id){
                return true;
            }

            return false;
        });

    }
}
