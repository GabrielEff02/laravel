<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function loginCustom(Request $request)
    {
        // $query = DB::table('users')->select('USERNAME','PASSWORD')->where('USERNAME', $request->username)->where('PASSWORD', md5($request->password))->limit(1)->get();

        //  if ($query != '[]') {
        $user = User::where([
            'username' => $request->username,
            'password' => md5($request->password)
        ])->first();

        if ($user && $user->privilege === 'superadmin') {
            Auth::login($user);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $periode = array(
            'tahun'    => substr(date('Y-m-d'), 0, 4),
            'bulan'    => substr(date('Y-m-d'), 5, 2)
        );

        // $request->authenticate();

        if (! $this->loginCustom($request)) {
            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        // Set Periode ketika Login 
        $request->session()->put('periode', $periode);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
