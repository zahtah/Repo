<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

use function PHPUnit\Framework\isNull;

class UserController extends Controller
{
     public function allUsers() {
        $users = User::with('roles')->get();
        $roles = Role::all(); // برای dropdown تغییر نقش
        return view('admin.users.all-users',compact('users', 'roles')) ;
    }
    public function createUser()
    {
        return view('admin.users.create-user');
    }
    public function storeUser(Request $request){
        $data = $request -> validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','string','email','max:255','unique:users'],
            'phone' => ['string','max:255'],
            'address' => ['string','max:255'],
            'password' => ['required','string','min:8','confirmed'],
        ]);
        $user = User::create($data);
        if($request->has('verify')){
            $user->markEmailAsVerified();
        }
        return redirect(route('all-users'));
    }
    public function editUser($id){
        $user = User::find($id);
        return view('admin.users.edit-user')->with('user',$user);
    }
    public function updateUser(Request $request , $id){
        $user = User::find($id);
        $data =$request -> validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','string','email','max:255',Rule::unique('users')->ignore($user->id)],
            'phone' => ['string','max:255'],
            'address' => ['string','max:255'],
        ]);
        if (!is_null($request->password)){
            $request -> validate([
            'password' => ['required','string','min:8','confirmed'],
            ]);
            $data['password']=$request->password;
        }
        $user->update($data);
        if($request->has('verify')){
            $user->markEmailAsVerified();
        }
        return redirect(route('all-users'));
    }
    public function changeRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name'
        ]);
        if (auth()->id() === $user->id) {
            return back()->withErrors('نمی‌توانید نقش خودتان را تغییر دهید');
        }
        if ($user->hasRole('admin') && auth()->id() !== 1) {
            abort(403);
        }



        // حذف نقش‌های قبلی
        $user->syncRoles([$request->role]);

        return redirect()->back()->with('success', 'نقش کاربر تغییر کرد');
    }
}

