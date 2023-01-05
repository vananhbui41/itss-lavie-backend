<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Exception\ClientException;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    use HttpResponses;

    public function register(StoreUserRequest $request)
    {
        $request->validated($request->all());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API Token of' . $user->name)->plainTextToken
        ]);
    }

    public function login(LoginUserRequest $request)
    {
        $request->validated($request->true);

        
        if (!Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
            ])) 
        {
            return $this->error('', 'Credentials do not match', 401);
        }

        $user = User::where('email', $request->email)->first();
        
        return $this->success([
            'user' => $user,
            'token' => $user->createToken('Api Token of' . $user->name)->plainTextToken,
        ]);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();

        return $this->success([
            'message' => 'Logout successful.'
        ]);
    }

    /**
     * Redirect the user to the Provider authentication page.
     *
     * @param $provider
     * @return JsonResponse
     */
    public function redirectToProvider($provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }

        return response()->json([
            'url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl(),
        ]);
    }

    /**
     * Obtain the user information from Provider.
     *
     * @param $provider
     * @return JsonResponse
     */
    public function handleProviderCallback($provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }
        try {
            $user = Socialite::driver($provider)->stateless()->user();
        } catch (ClientException $exception) {
            return response()->json(['error' => $exception->getMessage()], 422);
        }

        $userCreated = User::firstOrCreate(
            [
                'email' => $user->getEmail()
            ],
            [
                'email_verified_at' => now(),
                'name' => $user->getName(),
                'status' => true,
            ]
        );
        $userCreated->providers()->updateOrCreate(
            [
                'provider' => $provider,
                'provider_id' => $user->getId(),
            ],
            [
                'avatar' => $user->getAvatar()
            ]
        );
        
        $token = $userCreated->createToken('token-name')->plainTextToken;

        return response()->json($userCreated, 200, ['Access-Token' => $token]);
    }

    /**
     * @param $provider
     * @return JsonResponse
     */
    protected function validateProvider($provider)
    {
        if (!in_array($provider, ['google'])) {
            return response()->json(['error' => 'Please login using google'], 422);
        }
    }
}
