<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Events\UserFollowed;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class FollowerController extends Controller
{
    // /**
    //  * Follow an user.
    //  */
    // public function follow(Request $request, $id)
    // {
    //     $user = User::find($id);

    //     $user->followers()->attach(auth()->user()->id);

    //     return back();
    // }

    // /**
    //  * Unfollow an user.
    //  */
    // public function unfollow(Request $request, $id)
    // {
    //     $user = User::find($id);

    //     $user->followers()->detach(auth()->user()->id);

    //     return back();
    // }

    public function store(Request $request):  RedirectResponse
    {
        $validated = $request->validate([
               'id' => [
               'required',
               'integer',
               'numeric',
               Rule::notIn([Auth()->id()]),
               'exists:users,id'
           ]
       ]);

        auth()->user()->following()->attach($validated['id']);

        $following = User::findOrFail($validated['id']);
        UserFollowed::dispatch($following, Auth()->user());

        return back();
   }

   public function destroy(int $id): RedirectResponse
   {
        auth()->user()->following()->detach($id);

        return back();
   }
}
