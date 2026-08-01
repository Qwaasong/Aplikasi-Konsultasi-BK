<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\Passport;

class OAuthClientSeeder extends Seeder
{
    /**
     * Buat OAuth password-grant client secara deterministik.
     * Lewat model Client agar kolom secret otomatis di-bcrypt
     * (raw insert plaintext akan membuat semua grant gagal).
     */
    public function run(): void
    {
        $model = Passport::client();

        if ($model->whereKey(config('oauth.client_id'))->exists()) {
            return; // idempotent
        }

        $model->forceCreate([
            'id' => config('oauth.client_id'),
            'name' => config('oauth.client_name'),
            'secret' => config('oauth.client_secret'),
            'provider' => 'users',
            'redirect_uris' => [],
            'grant_types' => ['password', 'refresh_token'],
            'revoked' => false,
        ]);
    }
}
