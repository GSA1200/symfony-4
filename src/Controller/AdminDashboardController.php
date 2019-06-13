<?php

namespace App\Controller;

use App\Service\StatsService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(ObjectManager $manager, StatsService $statsService){                
        $users=$statsService->getUserCount();
        $ads=$statsService->getAdCount();
        $comments=$statsService->getCommentCount();
        $bookings=$statsService->getBookingCount();
        $bestAds=$statsService->getAdStats('DESC');
        $worstAds=$statsService->getAdStats('ASC');

        return $this->render('admin/dashboard/index.html.twig', [
            'users' => $users,
            'ads'   =>$ads,
            'comments' =>$comments,
            'bookings' =>$bookings,
            'bestAds'   =>$bestAds,
            'worstAds'   =>$worstAds
        ]);
    }
}
