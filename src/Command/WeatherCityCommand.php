<?php

namespace App\Command;

use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:city',
    description: 'Get weather forecast for a specific city and country code',
)]
class WeatherCityCommand extends Command
{
    private $weatherUtil;

    public function __construct(WeatherUtil $weatherUtil)
    {
        $this->weatherUtil = $weatherUtil;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('countryCode', InputArgument::REQUIRED, 'Country code')
            ->addArgument('city', InputArgument::REQUIRED, 'City name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $countryCode = $input->getArgument('countryCode');
        $city = $input->getArgument('city');

        try {
            $measurements = $this->weatherUtil->getWeatherForCountryAndCity($countryCode, $city);
            $io->writeln(sprintf('Location: %s, %s', $city, strtoupper($countryCode)));
            foreach ($measurements as $measurement) {
                $weatherRecord = $measurement->getWeatherRecord();
                $io->writeln(sprintf("\t%s: %s",
                    $weatherRecord->getDate()->format('Y-m-d'),
                    $measurement->getCelsius()
                ));
            }
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}