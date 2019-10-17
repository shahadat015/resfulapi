<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Mail\UserCreated;
use App\Transformers\UserTransformer;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends ApiController
{

    public function __construct()
    {        
        $this->middleware('client.credentials')->only(['store', 'resend']);    
        $this->middleware('auth:api')->except(['store', 'verify', 'resend']);    
        $this->middleware('transform.input:' . UserTransformer::class)->only(['store', 'update']);
        $this->middleware('scope:manage-account')->only(['show', 'update']);
        $this->middleware('can:view,user')->only('show');
        $this->middleware('can:update,user')->only('update');
        $this->middleware('can:delete,user')->only('destroy');
    }

    public function index()
    {
        $this->allowedAdminAction();

        $users = User::all();
        return $this->showAll($users);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:60',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        $data['verification_token'] = User::generateVerificationCode();
        $data['verified'] = User::UNVERIFIED_USER;
        $data['admin'] = User::REGULAR_USER;

        $user = User::create($data);
        return $this->showOne($user, 201);
    }

    public function show(User $user)
    {
        return $this->showOne($user);
    }

    public function update(Request $request, User $user)
    {
        
        $request->validate([
            'email'    => 'email|unique:users,email,'.$user->id,
            'password' => 'min:8|confirmed',
            'admin'    => 'in:' . User::ADMIN_USER . ',' . User::REGULAR_USER,
        ]);

        if($request->has('name')){
            $user->name = $request->name;
        }

        if($request->has('email') && $user->email != $request->email){
            $user->verification_token = User::generateVerificationCode();
            $user->email_verified_at = NULL;
            $user->verified = User::UNVERIFIED_USER;
            $user->email = $request->email;
        }

        if($request->has('password')){
            $user->password = Hash::make($request->password);
        }

        if($request->has('admin')){
            $this->allowedAdminAction();
            
            if(!$user->isVerified()) {
                return $this->errorResponse('Only verified user can modify admin field', 409);
            }
            $user->admin = $request->admin;
        }

        if(!$user->isDirty()) {
            return $this->errorResponse("You need to specify a diffrent value to update", 422);
        }

        $user->save();
        return $this->showOne($user);

    }

    public function destroy(User $user)
    {
        $user->delete();
        return $this->showOne($user);
    }

    public function verify($token)
    {
        $user = User::where('verification_token', $token)->firstOrFail();
        $user->verification_token = NULL;
        $user->email_verified_at = Carbon::now();
        $user->verified = User::VERIFIED_USER;
        $user->save();
        return $this->showMessage('The account has been verified successfuly');
    }

    public function resend(User $user)
    {
        if($user->isVerified()){
            return $this->errorResponse('The user is already verifyed', 409);
        }

        retry(5, function() use ($user) { 
                Mail::to($user)->send(new UserCreated($user));
            }, 10);
        return $this->showMessage('The verification email has been resend');
    }

}
