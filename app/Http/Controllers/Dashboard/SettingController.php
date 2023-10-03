<?php

namespace App\Http\Controllers\Dashboard;

use Hash;
use App\Models\User;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    use ImageTrait;

    public function profile()
    {
        return view('dashboard.users.profile');
    }



    public function editProfile()
    {
        return view('dashboard.users.editProfile');
    }



    public function updateProfile(Request $request)
    {
        try {
            $id   = Auth::user()->id;
            $user = User::findOrFail($id);

            $this->validate($request, [
                'name'     => 'required',
                'email'    => 'required|email|unique:users,email,'.$id,
                'mobile'   => 'required|unique:users,mobile,'.$id,
            ]);

            //upload image
            if ($request->avatar) {
                $photo_name = $this->uploadImage($request->avatar, 'attachments/user');
            }

            $user->update([
                'name'   => $request['name'],
                'email'  => $request['email'],
                'mobile' => $request['mobile'],
                'avatar' => $request->avatar ? $photo_name : $user->avatar,
            ]);

            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function changePassword()
    {
        return view('dashboard.users.changePassword');
    }


    
    public function updatePassword(Request $request)
    {
        try {
            $id   = Auth::user()->id;
            $user = User::findOrFail($id);

            $this->validate($request, [
                'old_password' => 'required|string|min:8',
                'password'     => 'required|string|min:8',
            ]);

            if (Hash::check($request->old_password, $user->password)) 
            {
                $user->update([
                    'password' => bcrypt($request->password),
                ]);

                session()->flash('success');
                return redirect()->back();
            } else {
                session()->flash('errorOldPassword');
                return redirect()->back();
            }

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

}
