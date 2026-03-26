<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle user login.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $credentials['email'])->first();

            if (!$user) {
                throw ValidationException::withMessages([
                    'email' => 'The provided credentials are invalid.',
                ]);
            }
            
            $passwordValid = Hash::check($credentials['password'], $user->password);
            
            if (!$passwordValid) {
                throw ValidationException::withMessages([
                    'email' => 'The provided credentials are invalid.',
                ]);
            }

            $token = $this->generateJwtToken();

            session(['jwt_token' => $token, 'user_id' => $user->id, 'user_email' => $user->email]);

            return redirect('/');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['email' => 'An unexpected error occurred. Please try again.']);
        }
    }

    /**
     * Handle user registration.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6|confirmed',
            ]);

            $user = User::create([
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $token = $this->generateJwtToken();

            session(['jwt_token' => $token, 'user_id' => $user->id, 'user_email' => $user->email]);

            return redirect('/');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('Registration error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['email' => 'An error occurred during registration. Please try again.']);
        }
    }

    /**
     * Handle user logout.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        try {
            session()->flush();
            return redirect('/');
        } catch (\Exception $e) {
            \Log::error('Logout error: ' . $e->getMessage());
            return redirect('/');
        }
    }

    /**
     * Generate a JWT token for authentication.
     *
     * @return string
     * @throws \RuntimeException
     */
    private function generateJwtToken(): string
    {
        try {
            $key = (string) config('services.quotation.jwt_secret');
            if ($key === '') {
                throw new \RuntimeException('QUOTATION_JWT_SECRET is not configured.');
            }

            $payload = [
                'sub' => 'quotation',
                'iat' => time(),
                'exp' => time() + 3600,
            ];

            return JWT::encode($payload, $key, 'HS256');
        } catch (\Exception $e) {
            \Log::error('JWT generation failed: ' . $e->getMessage());
            throw new \RuntimeException('Failed to generate authentication token. Please try again.');
        }
    }
}
