<?php
namespace App\Http\Controllers;

use Gate;
use Datatables;
use Illuminate\Support\Facades\Storage;
use Session;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use Ramsey\Uuid\Uuid;

class UsersController extends Controller
{
    protected $users;
    protected $roles;

    public function __construct()
    {
        // $this->middleware('user.create', ['only' => ['create']]);
        $this->middleware('is.demo', ['only' => ['update', 'destroy']]);
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return view('users.index')->withUsers(User::all());
    }

    public function users()
    {
        return User::all();
    }

    public function anyData()
    {
        $users = User::select(['id', 'external_id', 'name', 'email', 'device_id']);
        return Datatables::of($users)
            ->addColumn('namelink', function ($users) {
                return '<a href="/users/' . $users->external_id . '" ">' . $users->name . '</a>';
            })
            ->addColumn('edit', function ($user) {
                return '<a href="' . route("users.edit", $user->external_id) . '" class="btn btn-link">' . __('Edit') .'</a>';
            })
           ->addColumn('delete', function ($user) {
               return '<button type="button" class="btn btn-link" data-client_id="' . $user->external_id . '" onClick="openModal(\'' . $user->external_id . '\')" id="myBtn">' . __('Delete') .'</button>';
           })
            ->rawColumns(['namelink',  'edit', 'delete'])
            ->make(true);
    }
    /**
     * @return mixed
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * @param StoreUserRequest $userRequest
     * @return mixed
     */
    public function store(StoreUserRequest $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->external_id = Uuid::uuid4()->toString();
        $user->email = $request->email;
        $user->social_id = $request->social_id;
        $user->social_site = $request->social_site;
        $user->save();

        Session::flash('flash_message', __('User successfully added'));
        return redirect()->route('users.index');
    }

    /**
     * @param $external_id
     * @return mixed
     */
    public function show($external_id)
    {
        /** @var User $user */
        $user = $this->findByExternalId($external_id);
        return view('users.show')
            ->withUser($user);
    }


    /**
     * @param $external_id
     * @return mixed
     */
    public function edit($external_id)
    {
        return view('users.edit')
            ->withUser($this->findByExternalId($external_id));
    }

    /**
     * @param $external_id
     * @param UpdateUserRequest $request
     * @return mixed
     */
    public function update($external_id, UpdateUserRequest $request)
    {
        $user = $this->findByExternalId($external_id);

        Session()->flash('flash_message', __('User successfully updated'));
        return redirect()->back();
    }

    /**
     * @param $external_id
     * @return mixed
     */
    public function destroy(Request $request, $external_id)
    {
        $user = $this->findByExternalId($external_id);
        try {
            $user->delete();
            Session()->flash('flash_message', __('User successfully deleted'));
        } catch (\Illuminate\Database\QueryException $e) {
            Session()->flash('flash_message_warning', __('User can NOT have, leads, clients, or tasks assigned when deleted'));
        }

        return redirect()->route('users.index');
    }

    /**
    * @param $external_id
    * @return mixed
    */
    public function findByExternalId($external_id)
    {
        return User::whereExternalId($external_id)->first();
    }
}
