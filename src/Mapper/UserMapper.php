<?php 


namespace App\Mapper;

use App\Dto\UserDTO;
use App\Entity\User;

class UserMapper
{
    public function mapDtoToEntity(UserDTO $dto, User $user): User
    {
        $user->setEmail($dto->email);
        $user->setFirstName($dto->first_name);
        $user->setLastName($dto->last_name);
        $user->setUsername($dto->username);
        $user->setIsVerify($dto->is_verify);

        return $user;
    }
}
