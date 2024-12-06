<?php

declare(strict_types=1);

namespace Brightspace\Api\User;

use Brightspace\Api\Core\Client\ApiClient;
use Brightspace\Api\User\Message\CreateUser;
use Brightspace\Api\User\Message\DeleteUser;
use Brightspace\Api\User\Message\GetUser;
use Brightspace\Api\User\Message\GetWhoAmI;
use Brightspace\Api\User\Message\ListUsers;
use Brightspace\Api\User\Message\UpdateUser;
use Brightspace\Api\User\Model\User;
use Brightspace\Api\User\Model\WhoAmI;

final class UserApi extends ApiClient
{
    /**
     * @return WhoAmI
     */
    public function whoAmI(): WhoAmI
    {
        return $this->invoke(new GetWhoAmI());
    }


    /**
     * @param string|null $orgDefinedId
     * @param string|null $userName
     * @param string|null $externalEmail
     * @param string|null $bookmark
     * @return iterable<int,User>
     */
    public function list(
        string|null $orgDefinedId = null,
        string|null $userName = null,
        string|null $externalEmail = null,
        string|null $bookmark = null
    ): iterable {
        return $this->invoke(new ListUsers(
            $orgDefinedId,
            $userName,
            $externalEmail,
            $bookmark
        ));
    }


    /**
     * @param int|string $userIdOrName
     * @return User|null
     */
    public function get(int|string $userIdOrName): User|null
    {
        if (is_int($userIdOrName)) {
            return $this->invoke(new GetUser($userIdOrName));
        }
        foreach ($this->list(userName: $userIdOrName) as $user) {
            return $user;
        }
        return null;
    }


    /**
     * @param User $newUser
     * @return User
     */
    public function create(User $newUser): User
    {
        return $this->invoke(new CreateUser($newUser));
    }


    /**
     * @param User $updateUser
     * @return User
     */
    public function update(User $updateUser): User
    {
        return $this->invoke(new UpdateUser($updateUser));
    }


    /**
     * @param int $userId
     * @return bool
     */
    public function delete(int $userId): bool
    {
        return $this->invoke(new DeleteUser($userId));
    }
}
