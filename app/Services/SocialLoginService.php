<?php

namespace App\Services;

use App\Models\User;
use App\Models\SocialAccount;
use Laravel\Socialite\Two\User as ProviderUser;


class SocialLoginService {

    /**
     * Find or create user instance by provider user instance and provider name.
     *
     * @param ProviderUser $providerUser
     * @param string $provider
     *
     * @return User
     */
    public function findOrCreate(ProviderUser $providerUser, string $provider): User
    {
        $linkedSocialAccount = SocialAccount::where('provider_name', $provider)->where('provider_id', $providerUser->getId())->first();

        if ($linkedSocialAccount) {
            return $linkedSocialAccount->user;
        }

        if($email = $providerUser->getEmail())
            $user = User::where('email', $email)->first();

        if (!$user)
            $user = User::create([
                'name'  =>  $providerUser->getName(),
                'email' =>  $email,
            ]);

        $user->linkedSocialAccounts()->create([
            'provider_id'   =>  $providerUser->getId(),
            'provider_name' =>  $provider,
        ]);

        return $user;
    }
}
