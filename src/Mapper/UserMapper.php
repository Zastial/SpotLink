<?php 


namespace App\Mapper;

use App\Dto\UserDTO;
use App\Entity\User;

class UserMapper
{
    public function mapDtoToEntity(UserDTO $dto, User $user): User
    {
        $user->setEmail($dto->email);
        $user->setFirstName($dto->firstName);
        $user->setLastName($dto->lastName);
        $user->setUsername($dto->username);
        $user->setIsVerify($dto->isVerify);

        return $user;
    }
}
