<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $user)
    {
        /** @var User $user */
        if ($user->getIsVerified() !== true) {
            throw new CustomUserMessageAuthenticationException("You're account is not verified yet.");
        }
    }

    public function checkPostAuth(UserInterface $user)
    {

    }
}