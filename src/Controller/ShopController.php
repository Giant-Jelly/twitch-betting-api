<?php

namespace App\Controller;

use App\Entity\User;
use App\Helper\BetHelper;
use App\Helper\RequestHelper;
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
class ShopController extends BaseController
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

    /**
     * @Route("/buy", name="Buy", methods={"GET"})
     *
     * @param Request $request
     * @param ShopItemRepository $shopItemRepository
     * @return Response
     */
    public function buy(Request $request, ShopItemRepository $shopItemRepository): Response
    {
        $item = $shopItemRepository->find($request->get('item'));

        if (!$item) {
            $response = [
                'message' => 'That shop item doesn\'t exist :/'
            ];
            return ResponseHelper::getApiResponse($request, $response);
        }

        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy([
            'username' => RequestHelper::getUsernameFromRequest($request)
        ]);

        $user->setFlair($item);
        BetHelper::adjustCredits($user, -$item->getPrice());
        $this->getDoctrine()->getManager()->flush();

        $response = [
            'message' => 'Flair updated to ' . $item->getName() . ' for ' . $item->getPrice()
        ];
        return ResponseHelper::getApiResponse($request, $response);
    }
}
