<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Location;
use App\Entity\Measurement;
use App\Entity\WeatherRecord; // Dodaj ten import
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\LocationRepository;

class WeatherUtil
{
    private $entityManager;
    private $locationRepository;

    public function __construct(EntityManagerInterface $entityManager, LocationRepository $locationRepository)
    {
        $this->entityManager = $entityManager;
        $this->locationRepository = $locationRepository;
    }

    /**
     * @return Measurement[]
     */
    public function getWeatherForLocation(Location $location): array
    {
        // Pobierz dane pogodowe dla podanej lokalizacji
        $weatherRecordRepository = $this->entityManager->getRepository(WeatherRecord::class);
        $weatherRecords = $weatherRecordRepository->findBy(['location' => $location]);

        $measurements = [];
        foreach ($weatherRecords as $weatherRecord) {
            $measurements = array_merge($measurements, $weatherRecord->getMeasurements()->toArray());
        }

        return $measurements;
    }

    /**
     * @return Measurement[]
     */
    public function getWeatherForCountryAndCity(string $countryCode, string $city): array
    {
        // Pobierz lokalizację na podstawie kodu kraju i nazwy miasta
        $location = $this->getLocationByCountryAndCity($countryCode, $city);
        if (!$location) {
            throw new \Exception('Location not found');
        }

        // Wywołaj metodę getWeatherForLocation dla otrzymanej lokalizacji
        return $this->getWeatherForLocation($location);
    }

    private function getLocationByCountryAndCity(string $countryCode, string $city): ?Location
    {
        // Pobierz lokalizację na podstawie kodu kraju i nazwy miasta
        return $this->locationRepository->findOneBy([
            'country' => strtoupper($countryCode),
            'city' => $city,
        ]);
    }
}