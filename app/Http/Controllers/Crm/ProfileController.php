<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        
        return view('crm.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request)
    {
        try{
            $user = User::findOrFail($request->user_id);
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->phone = $request->phone;
            $user->al_phone = $request->al_phone;

            if ($request->hasFile('profile_image')) {
                if ($user->profile_image && Storage::disk('public')->exists('profileImage/' . $user->profile_image)) {
                    Storage::disk('public')->delete('profileImage/' . $user->profile_image);
                }
                $file = $request->file('profile_image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('profileImage', $filename, 'public');
                $user->profile_image = $filename;
            }
            $user->save();
            session()->flash('success', 'Profile updated successfully');
            return response()->json([
                'status' => true,
                'redirect_url' => route('profile.edit')
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => "Something went wrong"
            ]);
        }
    }


    public function updatePassword(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                "password" => ['required',
                            'confirmed',
                            Password::min(8) // Minimum length
                            ->letters()  // Must contain at least one letter
                            ->mixedCase() // Must contain uppercase AND lowercase letters
                            ->numbers()   // Must contain at least one number
                        ],
            ]);

            if($validator->fails()){
                return response()->json([
                            'status' => 'error',
                            'errors' => $validator->errors()
                        ], 422);

            }
            
            // Check old password
            if (!Hash::check($request->old_password, auth()->user()->password)) {
                return response()->json([
                            'status' => 'error',
                            'errors' => ['old_password' => ['The old password is incorrect.']]
                        ], 422);
            }
            $user = User::findOrFail($request->user_id);
            $user->password = $request->password;
            $user->save();
            
            return response()->json([
                'status' => true,
                'message' => 'Password updated successfully',
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => "Something went wrong"
            ]);
        }
    }

}
