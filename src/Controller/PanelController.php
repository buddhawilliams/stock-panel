<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Provider\StockPriceProvider;
use App\Repository\StockRepository;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PanelController extends AbstractController
{
    public function __construct(
        private readonly StockPriceProvider $stockPriceProvider,
        private readonly StockRepository $stockRepo
    ) {
    }

    private const RANGE_INTERVAL_MAP = [
        '1d' => '2m',
        '5d' => '15m',
        '1mo' => '1h',
        '6mo' => '1d',
        'ytd' => '1wk',
        '1y' => '1wk',
        '5y' => '1mo',
        'max' => '1mo',
    ];

    /**
     * Show the stock panel
     * @throws Exception
     */
    #[Route(path: '/', name: 'stock_table')]
    public function tableAction(): Response
    {
        $stocks = $this->stockPriceProvider->getStocksAndUpdate();
        return $this->render("Panel/table.html.twig", [
            'stocks' => $stocks,
        ]);
    }

    /**
     * Show the stock panel
     * @throws Exception
     */
    #[Route(path: '/charts', name: 'stock_charts')]
    public function chartsAction(): Response
    {
        $stocks = $this->stockPriceProvider->getStocksAndUpdate();
        return $this->render("Panel/charts.html.twig", [
            'stocks' => $stocks,
        ]);
    }

    /**
     * Get JSON data for the chart
     * @param int $id
     * @param string $range
     * @param Request $request
     * @return Response
     * @throws NotFoundHttpException;

     */
    #[Route(path: '/charts/{id}/{range}.json', name: 'stock_charts_data')]
    public function getChartData(int $id, string $range, Request $request): Response
    {
        $stock = $this->getStock($id);
        if (!$stock) {
            throw $this->createNotFoundException('Stock not found');
        }
        if (!array_key_exists($range, self::RANGE_INTERVAL_MAP)) {
            throw $this->createNotFoundException('Invalid range');
        }

        $symbol = $stock->getSymbol();
        $interval = self::RANGE_INTERVAL_MAP[$range];

        $url = "https://query1.finance.yahoo.com/v8/finance/chart/" . $symbol . "?range=" . $range . "&includePrePost=false&interval=" . $interval;
        $client = new Client();
        try {
            $response = $client->get($url);
        } catch (ClientException $e) {
            throw $this->createNotFoundException('Client error: ' . $e->getMessage(), $e);
        } catch (GuzzleException $e) {
            throw $this->createNotFoundException('Guzzle Error: ' . $e->getMessage(), $e);
        }

        $json = json_decode($response->getBody(), true);
        if (!isset($json['chart']['result'][0]['timestamp'])) {
            throw $this->createNotFoundException('Timestamps not found');
        }
        if (!isset($json['chart']['result'][0]['indicators']['quote'][0]['close'])) {
            throw $this->createNotFoundException('Closing prices not found');
        }

        $dataPrice = [];
        $dataVolume = [];
        $timestamps = $json['chart']['result'][0]['timestamp'];
        $openPrices = $json['chart']['result'][0]['indicators']['quote'][0]['open'];
        $highPrices = $json['chart']['result'][0]['indicators']['quote'][0]['high'];
        $lowPrices = $json['chart']['result'][0]['indicators']['quote'][0]['low'];
        $closingPrices = $json['chart']['result'][0]['indicators']['quote'][0]['close'];
        $volumes = $json['chart']['result'][0]['indicators']['quote'][0]['volume'];
        foreach ($timestamps as $index => $timestamp) {
            $timestampMs = $timestamp * 1000;
            $openPrice = $openPrices[$index] ?? null;
            $highPrice = $highPrices[$index] ?? null;
            $lowPrice = $lowPrices[$index] ?? null;
            $closingPrice = $closingPrices[$index] ?? null;
            $volume = $volumes[$index] ?? null;
            $dataPrice[] = [$timestampMs, $openPrice, $highPrice, $lowPrice, $closingPrice];
            $dataVolume[] = [$timestampMs, $volume];
        }

        return new JsonResponse([
            'price'=> $dataPrice,
            'volume' => $dataVolume,
        ]);
    }

    /**
     * Force stock update
     */
    #[Route(path: '/update', name: 'stock_update')]
    public function updateAction(): Response
    {
        $this->stockPriceProvider->updateStocks();
        $stocks = $this->stockPriceProvider->getStocks();
        return $this->render("Panel/tableContent.html.twig", [
            'stocks' => $stocks,
        ]);
    }

    private function getStock(int $id): ?Stock
    {
        // This uses built in methods of ServiceEntityRepository. You don't define the method.
        return $this->stockRepo->findOneById($id);
    }
}
