<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json([
            'message' => 'Get all users',
            'data' => $users,
        ],200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:150|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|unique:users,phone',
            'gender' => 'nullable|string|max:30',
            'date_of_birth' => 'nullable|date',
            'image' => 'nullable|file|max:2048|mimes:png,webp,jpg,jpeg,svg',
            'role_id' => 'required|exists:roles,id'
        ]);

        if($request->hasFile('image')) {
            $uploadedFileUrl = $request->file('image')->storeOnCloudinary('users')->getSecureUrl();

            $validated['image'] = $uploadedFileUrl;
        }

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'message'=>'Created user successfully.',
            'data'=>$user,
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json([
            'message' => 'Get user detail successfully',
            'data' => $user,
        ],200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:150|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|unique:users,phone,' . $user->id,
            'gender' => 'nullable|string|max:30',
            'date_of_birth' => 'nullable|date',
            'image' => 'nullable|file|max:2048|mimes:png,webp,jpg,jpeg,svg',
            'role_id' => 'required|exists:roles,id'
        ]);
            
        if($request->hasFile('image')) {
            // code for delete image into cloudinary
            if($user->image) {
                try {
                    $path = parse_url($user->image, PHP_URL_PATH);
                    
                    $pathWithoutExtension = pathinfo($path, PATHINFO_DIRNAME) . '/' . pathinfo($path, PATHINFO_FILENAME);
                    
                    $publicId = ltrim(strstr($pathWithoutExtension, 'users/'), '/');

                    if($publicId) {
                        app('cloudinary')->uploadApi()->destroy($publicId);
                    }

                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
            // add new image into cloudinary
            $uploadedFileUrl = $request->file('image')->storeOnCloudinary('users')->getSecureUrl();
            $validated['image'] = $uploadedFileUrl;
        }
            
        if (!empty($validated['password'])) {
            if (Hash::check($request->password, $user->password)) {
                unset($validated['password']); 
            } else {
                $validated['password'] = Hash::make($validated['password']);
            }
        } else {
            unset($validated['password']);
        }

        $hasChange = false;
        foreach($validated as $key => $value) {
            if($user->{$key} != $value){
                $hasChange = true;
                break;
            }
        }

        if(!$hasChange) {
            return response()->json([
                'message' => 'Nothing user update',
            ]);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Update user successfuly',
            'data' => $user,
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if($user ->image) {
            try {

                $path = parse_url($user->image, PHP_URL_PATH);
                $pathWithoutExtension = pathinfo($path, PATHINFO_BASENAME) . '/' . pathinfo($path, PATHINFO_FILENAME);
                
                $publicId = ltrim(strstr($pathWithoutExtension, 'users/'), '/');

                if($publicId) {
                    app('cloudinary')->uploadApi()->destroy($publicId);
                }

            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        $user->delete();

        return response()->json([
            'message' => 'Delete user and image from Cloudinary successfully'
        ],200);
    }
}
