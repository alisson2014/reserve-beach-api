<?php

declare(strict_types=1);

namespace App\Repository\UserRepository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 * 
 * @method User|null getByEmail(string $email)
 * @method User add(User $user, bool $flush = false): User
 * @method User update(User $user, bool $flush = false): User
 * @method void remove(User $entity, bool $flush = false): void
 */
class UserRepository extends ServiceEntityRepository implements IUserRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getByEmail(string $email): ?User
    {
        return $this->findOneBy(compact('email'));
    }

    public function add(User $user, bool $flush = false): User
    {
        $this->getEntityManager()->persist($user);

        if($flush) {
            $this->getEntityManager()->flush();
        }

        return $user;
    }

    public function update(User $user, bool $flush = false): User
    {
        $this->getEntityManager()->persist($user);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $user;
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if (!$flush) return;

        $this->getEntityManager()->flush();
    }
}