<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Events\UserFollowed;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Inertia\Response;
use Inertia\Inertia;

class FollowerController extends Controller
{
    public function index(User $user): Response
    {

        // $following = $user
        // ->following()
        // ->orderByPivot('created_at','desc')
        // ->select('user_id as id', 'name')
        // ->addSelect(['following' => function ($query) {
        // $query->select('id as following')
        //     ->from('followers')
        //     ->whereColumn('follower_id', Auth()->id())
        //     ->whereColumn('user_id', 'users.id');
        // }])
        // ->simplePaginate(25, []);
        // $following->map(fn ($user) => $user->makeHidden('pivot'));

        $following = $user
                ->following()
                ->orderByPivot('created_at','desc')
                ->get();


        //error_log($following);
        return Inertia::render('Follow/Index', [
            'user' => $user->only(['id', 'name']),
            'users' => fn() => $following,
        ]);
    }

    public function store(Request $request):  RedirectResponse
    {
        $validated = $request->validate([
               'id' => [
               'required',
               'integer',
               'numeric',
               Rule::notIn([auth()->id()]),
               'exists:users,id'
           ]
       ]);

        auth()->user()->following()->attach($validated['id']);

        $following = User::findOrFail($validated['id']);
        UserFollowed::dispatch($following, auth()->user());

        return redirect()->back();
   }

   public function destroy(int $id): RedirectResponse
   {
        auth()->user()->following()->detach($id);

        return redirect()->back();
   }

   public function list(User $user): Response
   {
        // $followers = $user
        // ->followers()
        // ->orderByPivot('created_at','desc')
        // ->select('follower_id as id', 'name')
        // ->addSelect(['following' => function ($query) {
        //     $query->select('id as following')
        //         ->from('followers')
        //         ->whereColumn('follower_id', Auth()->id())
        //         ->whereColumn('user_id', 'users.id');
        // }])
        //         ->simplePaginate(25, []);
        //         $followers->map(fn ($user) => $user->makeHidden('pivot'));


        $followers = $user
            ->followers()
            ->orderByPivot('created_at','desc')
            ->get();


        //$loggedUserFollowers = auth()->user()->followers()->orderByPivot('created_at','desc')->get();


        return Inertia::render('Follow/Index', [
            'user' => $user->only(['id', 'name']),
            'users' => fn() => $followers,
        ]);
   }
}
