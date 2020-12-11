<?php

namespace App\Http\Controllers\API;

use App\Actions\Fortify\PasswordValidationRules;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    // Library Password Validation for use in Register
    use PasswordValidationRules;


    // API Login User
    public function login(Request $request)
    {
        // Validation User
        try {
            // Validation Input User
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            // Validation Data User
            $credentials = request(['email', 'password']);
            if (!Auth::attempt([$credentials])) {
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ], 'Authentication Failed', 500);
            }

            // If Data User InCorrect -> Response Error
            $user = User::where('email', $request->email)->first(); // Check Email
            if (!Hash::check($request->password, $user->password, [])) { // Check Password
                throw new \Exception('Invalid Credentials');
            }

            // If Data User Correct -> Login User
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Login Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Login Failed', 500);
        }
    }

    // API Register User
    public function register(Request $request)
    {
        try {
            // Validation Input User
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => $this->passwordRules()
            ]);
            // Create Data User
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'houseNumber' => $request->houseNumber,
                'phoneNumber' => $request->phoneNumber,
                'city' => $request->city,
                'password' => Hash::make($request->password),
            ]);

            // Get Data User
            $user = User::where('email', $request->email)->first();

            // Get Token User
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            // Return Data Token & User for Login
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ]);
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Register Failed', 500);
        }
    }
}
