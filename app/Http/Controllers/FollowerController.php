<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use Illuminate\Http\Request;

class FollowerController extends Controller
{
    /**
     * Follow an user.
     */
    public function follow(Request $request, $id)
    {
        $user = User::find($id);

        $user->followers()->attach(auth()->user()->id);

        return back();
    }

    /**
     * Unfollow an user.
     */
    public function unfollow(Request $request, $id)
    {
        $user = User::find($id);

        $user->followers()->detach(auth()->user()->id);

        return back();
    }
}
