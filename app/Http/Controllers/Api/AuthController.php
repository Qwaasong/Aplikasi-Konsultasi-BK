<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use GuzzleHttp\Psr7\Response as PsrResponse;
use GuzzleHttp\Psr7\ServerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use League\OAuth2\Server\AuthorizationServer;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'jenis_kelamin' => 'required|in:L,P',
            'no_hp' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password, // cast 'hashed' menangani bcrypt
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp' => $request->no_hp,
            'role' => 'siswa', // self-registration selalu siswa
            'status' => 'aktif',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Register berhasil!',
            'data' => $user,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string', // username ATAU email
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        return $this->issueToken($request->input('username'), $request->input('password'));
    }

    /**
     * Proxy password grant OAuth2 lewat AuthorizationServer (tanpa HTTP round-trip),
     * sehingga client mobile tidak pernah memegang client secret.
     */
    protected function issueToken(string $username, string $password): JsonResponse
    {
        $psr = (new ServerRequest('POST', url('/oauth/token')))
            ->withParsedBody([
                'grant_type' => 'password',
                'client_id' => config('oauth.client_id'),
                'client_secret' => config('oauth.client_secret'),
                'username' => $username,
                'password' => $password,
                'scope' => '',
            ]);

        $response = app(AuthorizationServer::class)->respondToAccessTokenRequest($psr, new PsrResponse);
        $payload = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() !== 200 || isset($payload['error'])) {
            return response()->json([
                'status' => false,
                'message' => $payload['error_description'] ?? 'Username atau password salah',
            ], 401);
        }

        return response()->json([
            'status' => true,
            'message' => 'Login berhasil!',
            'data' => User::where('username', $username)->orWhere('email', $username)->first(),
            'token' => $payload,
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $accessToken = $request->user()->token();
        $accessToken->refreshToken?->revoke();
        $accessToken->revoke();

        return response()->json([
            'status' => true,
            'message' => 'Logout berhasil!',
        ], 200);
    }

    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => $request->user(),
        ], 200);
    }
}
