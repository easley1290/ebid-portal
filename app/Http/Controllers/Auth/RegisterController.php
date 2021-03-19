<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\Personas;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'per_nombres' => ['required', 'string', 'max:50'],
            'per_paterno' => ['required', 'string', 'max:50'],
            'per_materno' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:50', 'unique:personas'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'per_subd_extension' => ['required', 'string'],
            'per_num_documentacion' => ['required', 'max:11'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return Personas::create([
            'per_ua_id' =>'UA-EA0001',
            'per_nombres' => $data['per_nombres'],
            'per_paterno' => $data['per_paterno'],
            'per_materno' => $data['per_materno'],
            'name' => $data['per_nombres'].' '.$data['per_paterno'].' '.$data['per_materno'],
            'email' => $data['email'],
            'per_correo_personal' => $data['email'],
            'password' => Hash::make($data['password']),
            'per_subd_estado' => '2',
            'per_subd_extension' => $data['per_subd_extension'],
            'per_num_documentacion' => $data['per_num_documentacion'].$data['per_alfanumerico'],
        ]);
    }
}
