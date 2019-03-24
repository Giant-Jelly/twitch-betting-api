<?php

namespace App\Controller;

use App\Helper\ResponseHelper;
use App\Repository\ShopItemRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/shop", name="Shop", methods={"GET", "OPTIONS"})
 *
 * Class ShopController
 * @package App\Controller
 */
class ShopController
{
    /**
     * @Route("/items", name="Items", methods={"GET"})
     *
     * @param Request $request
     * @param ShopItemRepository $shopItemRepository
     * @return Response
     */
    public function items(Request $request, ShopItemRepository $shopItemRepository): Response
    {
        $allItems = $shopItemRepository->findAll();
        shuffle($allItems);

        $items = [];
        foreach (array_slice($allItems, 0, 8) as $item) {
            $items[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'price' => $item->getPrice(),
                'asset' => $item->getAsset(),
                'type' => $item->getType()
            ];
        }

        $response = [
            'items' => $items
        ];
        return ResponseHelper::getApiResponse($request, $response);
    }
}
