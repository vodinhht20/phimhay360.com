<?php

namespace Backpack\CRUD\app\Http\Controllers\Auth;

use App\Models\User;
use Backpack\CRUD\app\Library\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ophim\Core\Models\CashHistory;
use Validator;

class RegisterController extends Controller
{
    protected $data = []; // the information we send to the view

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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $guard = backpack_guard_name();

        $this->middleware("guest:$guard");

        // Where to redirect users after login / registration.
        $this->redirectTo = property_exists($this, 'redirectTo') ? $this->redirectTo
            : config('backpack.base.route_prefix', 'dashboard');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $user_model_fqn = config('backpack.base.user_model_fqn');
        $user = new $user_model_fqn();
        $users_table = $user->getTable();
        $email_validation = backpack_authentication_column() == 'email' ? 'email|' : '';

        return Validator::make($data, [
            'name'                             => 'required|max:255',
            backpack_authentication_column()   => 'required|'.$email_validation.'max:255|unique:'.$users_table,
            'password'                         => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user_model_fqn = config('backpack.base.user_model_fqn');
        $user = new $user_model_fqn();
        $reference_user_id = 0;
        if (session()->has('ref_user_code')) {
            $reference_user_id = User::query()
                ->where('reference_code', session()->get('ref_user_code'))
                ->first()?->id ?: 0;
        }
        $newUser = $user->create([
            'name'                             => $data['name'],
            backpack_authentication_column()   => $data[backpack_authentication_column()],
            'password'                         => bcrypt($data['password']),
            'reference_code' => uniqid(),
            'reference_user_id' => $reference_user_id,
        ]);

        if ($reference_user_id > 0) {
            $this->giftCash($newUser->reference_user_id, 0, $newUser->id);
        }

        return $newUser;
    }

    function giftCash(int $parrentId, $init = 0, $srcId = 0): void
    {
        if ($init === 4) {
            return;
        }
        $parrent = User::find($parrentId);
        if (empty($parrent)) {
            return;
        }

        $cash = 0;
        switch ($init) {
            case 0:
                $cash = 25;
                break;
            case 1:
                $cash = 5;
                break;
            case 2:
                $cash = 2;
                break;
            case 3:
                $cash = 1;
                break;
        }

        CashHistory::query()->create([
            'cash' => $cash * 1000,
            'user_id' => $parrent->id,
            'cash_from_user_id' => $srcId,
            'desciption' => 'Cộng tiền hoa hồng do F'.$srcId + 1 . ' giới thiệu bạn bè',
            'status' => 1,
            'type' => 1,
            'user_approve_id' => 1
        ]);
        $parrent->cash += $cash;
        $parrent->save();

        if (empty($parrent->reference_user_id)) {
            return;
        }

        $this->giftCash($parrent->reference_user_id, $init + 1, $parrent->id);
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm(Request $request)
    {
        if ($request->has('ref') && $request->input('ref', '') !== '') {
            session()->push('ref_user_code', $request->get('ref'));
        }
        // if registration is closed, deny access
        if (! config('backpack.base.registration_open')) {
            abort(403, trans('backpack::base.registration_closed'));
        }

        $this->data['title'] = trans('backpack::base.register'); // set the page title

        return view(backpack_view('auth.register'), $this->data);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // if registration is closed, deny access
        if (! config('backpack.base.registration_open')) {
            abort(403, trans('backpack::base.registration_closed'));
        }

        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        event(new Registered($user));
        $this->guard()->login($user);

        return redirect('/');
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return backpack_auth();
    }
}
