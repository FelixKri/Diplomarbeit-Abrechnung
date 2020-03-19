<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }

    public function login(Request $request)
    {
        if(!auth()->attempt(request(['username','password']))){

            return redirect('/login')->withErrors([

                'message' => 'Please check your credentials and try again!'

            ]);

        }

        return redirect('/');
    }

    protected function authenticated(Request $request, $user)
    {
        if (strpos($user->roles, "ROLE_ADMIN") == -1) {
            
            Auth::logout();

            return redirect('/login')->withErrors([

                'message' => 'Please check your credentials and try again!'

            ]);
        }
    }

}
