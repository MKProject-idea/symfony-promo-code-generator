<?php

namespace App\Command;

use App\Repository\PromoCodeRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GeneratePromoCodeCommand extends Command
{
    protected static $defaultName = 'generate-promo-codes';

    /**
     * @var PromoCodeRepository
     */
    private $promoCodeRepository;

    /**
     * PromoCodeController constructor.
     * @param PromoCodeRepository $promoCodeRepository
     */
    public function __construct(PromoCodeRepository $promoCodeRepository)
    {
        $this->promoCodeRepository = $promoCodeRepository;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate promo codes and save them to file')
            ->addArgument('length', InputArgument::REQUIRED, 'length of one code')
            ->addArgument('amount', InputArgument::REQUIRED, 'amount of code to generate')
            ->addOption('alphanumeric', 'a', InputOption::VALUE_NONE, 'Generate alphanumeric codes');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $length = $input->getArgument('length');
        if (!is_numeric($length) || $length <= 0) {
            $io->error('Argument "length" must be a positive number!');
            return 0;
        }

        $amount = $input->getArgument('amount');
        if (!is_numeric($amount) || $length <= 0) {
            $io->error('Argument "amount" must be a positive number!');
            return 0;
        }

        $alphanumeric = $input->getOption('alphanumeric');

        $promoCodesArray = [];
        for ($i = 0; $i < $amount; $i++) {
            $promoCodesArray[] = $this->promoCodeRepository->generateRandomCodes($alphanumeric, $length);
        }

        $this->promoCodeRepository->saveGeneratedCodes($promoCodesArray);

        $io->success(sprintf('Successfully generated %d %s promo codes of length %d.', $amount, $alphanumeric ? 'alphanumeric' : 'numeric', $length));
        return 0;
    }
}
