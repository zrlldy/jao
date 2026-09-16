<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Google authentication failed.',
            ], 401);
        }


        $peopleResponse = Http::withToken($googleUser->token)->get('https://people.googleapis.com/v1/people/me', [
            'personFields' => implode(',', [
                'names',
                'emailAddresses',
                'photos',
                'phoneNumbers',
            ]),
        ]);

        // Split Google's full name into first / last (+ optional middle)
        $nameParts = explode(' ', trim($googleUser->getName()));
        $firstName = $nameParts[0] ?? '';
        $lastName = count($nameParts) > 1 ? end($nameParts) : '';
        $middleName = count($nameParts) > 2
            ? implode(' ', array_slice($nameParts, 1, -1))
            : null;

        $applicant = Applicant::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'id' => (string)Str::uuid(),
                'google_id' => $googleUser->getId(),
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'birth_date' => $peopleResponse->json('birthdays.0.date') ?? null,
                'last_name' => $lastName,
                'avatar_url' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
            ]
        );

        $token = $applicant->createToken('auth_token')->plainTextToken;

        return response()->json([
            'applicant' => $applicant,
            'token' => $token,
        ]);
    }

    public function logout(Applicant $applicant)
    {
        $applicant->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}
