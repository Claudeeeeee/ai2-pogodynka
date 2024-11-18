<?php

namespace App\Controller;

use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WeatherApiController extends AbstractController
{
    private $weatherUtil;

    public function __construct(WeatherUtil $weatherUtil)
    {
        $this->weatherUtil = $weatherUtil;
    }

    #[Route('/api/v1/weather', name: 'app_weather_api', methods: ['GET'])]
    public function index(
        #[MapQueryParameter] string $country,
        #[MapQueryParameter] string $city,
        #[MapQueryParameter] string $format = 'json',
        #[MapQueryParameter('twig')] bool $twig = false
    ): Response {
        try {
            $measurements = $this->weatherUtil->getWeatherForCountryAndCity($country, $city);
            $measurementsArray = array_map(fn($m) => [
                'date' => $m->getWeatherRecord()->getDate()->format('Y-m-d'),
                'celsius' => $m->getCelsius(),
                'fahrenheit' => $m->getFahrenheit(),
            ], $measurements);

            if ($twig) {
                if ($format === 'csv') {
                    return $this->render('weather_api/index.csv.twig', [
                        'city' => $city,
                        'country' => $country,
                        'measurements' => $measurementsArray,
                    ]);
                }

                return $this->render('weather_api/index.json.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurementsArray,
                ]);
            }

            if ($format === 'csv') {
                $csvData = [];
                foreach ($measurementsArray as $measurement) {
                    $csvData[] = sprintf('%s,%s,%s,%s,%s', $city, $country, $measurement['date'], $measurement['celsius'], $measurement['fahrenheit']);
                }
                $csvContent = implode("\n", $csvData);
                return new Response($csvContent, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="weather.csv"',
                ]);
            }

            return $this->json([
                'city' => $city,
                'country' => $country,
                'measurements' => $measurementsArray,
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}