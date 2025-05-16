<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 *
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function getByEmail(string $email): ?Client
    {
        return $this->findOneBy(compact('email'));
    }

    public function add(Client $client, bool $flush = false): Client
    {
        $this->getEntityManager()->persist($client);

        if($flush) {
            $this->getEntityManager()->flush();
        }

        return $client;
    }

    public function update(Client $client, bool $flush = false): Client
    {
        $this->getEntityManager()->persist($client);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $client;
    }

    public function remove(Client $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if (!$flush) return;

        $this->getEntityManager()->flush();
    }
}