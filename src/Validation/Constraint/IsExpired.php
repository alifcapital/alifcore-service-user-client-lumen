<?php
declare(strict_types=1);

namespace AlifCapital\UserServiceClient\Validation\Constraint;

use Carbon\Carbon;
use DateInterval;
use DateTimeInterface;
use Lcobucci\Clock\Clock;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\ConstraintViolation;

final class IsExpired implements Constraint
{

    public function assert(Token $token): void
    {
        $exp = Carbon::parse($token->claims()->get('exp'));

        if (Carbon::now()->diffInSeconds($exp, false) <= 0){
            throw new ConstraintViolation('The token is expired');
        }

    }

}
