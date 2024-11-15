<?php

namespace App\Controller;

use App\Entity\Location;
use App\Repository\LocationRepository;
use App\Service\WeatherUtil;
use App\Repository\MeasurementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController
{
   #[Route('/weather/{city}/{country}', name: 'app_weather', requirements: ['city' => '.+', 'country' => '^[A-Z]{2}$'], defaults: ['country' => 'PL'])]
    public function city(string $city, string $country = 'PL', LocationRepository $locationRepository, WeatherUtil $util,): Response
    {
        $location = $locationRepository->findOneBy([
            'city' => $city,
            'country' => strtoupper($country),
        ]);

        
        if (!$location) {
            throw $this->createNotFoundException('Location not found');
        }

        
        $measurements = $util->getWeatherForLocation($location);

        
        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'measurements' => $measurements,
        ]);
    }
}