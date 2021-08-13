<?php


namespace AlifCapital\UserServiceClient\Auth;


use Illuminate\Auth\GenericUser;

class UserGeneric extends GenericUser
{
    public function __construct(array $attributes)
    {
        parent::__construct($attributes);
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->attributes['roles'];
    }

}
