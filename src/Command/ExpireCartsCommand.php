<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Cart;
use App\Enum\CartStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use DateTimeImmutable;

#[AsCommand(
    name: 'app:carts:expire',
    description: 'Finds and expires carts that have passed their expiration time.',
)]
class ExpireCartsCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $now = new DateTimeImmutable();
        $io->writeln("Running cart expiration check at: " . $now->format('Y-m-d H:i:s'));

        $expiredCarts = $this->em->getRepository(Cart::class)->createQueryBuilder('c')
            ->where('c.status = :status')
            ->andWhere('c.expiresAt <= :now')
            ->setParameter('status', CartStatus::OPEN)
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();

        if (empty($expiredCarts)) {
            $io->success('No expired carts to process.');
            return Command::SUCCESS;
        }

        foreach ($expiredCarts as $cart) {
            // Ao expirar o carrinho, os itens (agendamentos) precisam ser "liberados".
            // Aqui você adicionaria a lógica para reverter a reserva do horário.
            // Por exemplo, deletar o cartItem ou mudar o status de um schedule associado.

            $cart->setStatus(CartStatus::EXPIRED);
        }

        $this->em->flush(); // Salva todas as alterações no banco de uma vez

        $io->success(sprintf('Successfully expired %d carts.', count($expiredCarts)));

        return Command::SUCCESS;
    }
}