<?php

namespace App\Controller;

use App\Entity\Location;
use App\Repository\LocationRepository;
use App\Repository\MeasurementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController
{
    #[Route('/weather/{city}/{country}', name: 'app_weather', requirements: ['city' => '.+', 'country' => '^[A-Z]{2}$'], defaults: ['country' => 'PL'])]
    public function city(string $city, string $country = 'PL', LocationRepository $locationRepository, MeasurementRepository $repository): Response
    {
        // Wyszukiwanie lokalizacji na podstawie miasta i opcjonalnego kodu kraju
        $location = $locationRepository->findOneBy([
            'city' => $city,
            'country' => strtoupper($country), // Upewnij się, że kod kraju jest wielkimi literami
        ]);

        // Sprawdzenie, czy lokalizacja została znaleziona
        if (!$location) {
            throw $this->createNotFoundException('Location not found');
        }

        // Pobieranie prognozy pogody dla danej lokalizacji
        $measurements = $repository->findByLocation($location);

        // Renderowanie widoku z danymi lokalizacji i prognozami pogody
        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'measurements' => $measurements,
        ]);
    }
}