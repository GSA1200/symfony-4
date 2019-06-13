<?php


namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;

class StatsService{
    private $manager;

    public function __construct(ObjectManager $manager){
        $this->manager = $manager;
    }

    public function getUserCount(){
        return $this->manager->createQuery('SELECT count(u) FROM App\Entity\User u')->getSingleScalarResult();
    }
    public function getAdCount(){
        return $this->manager->createQuery('SELECT count(a) FROM App\Entity\Ad a')->getSingleScalarResult();
    }
    public function getCommentCount(){
        return $this->manager->createQuery('SELECT count(c) FROM App\Entity\Comment c')->getSingleScalarResult();
    }
    public function getBookingCount(){
        return $this->manager->createQuery('SELECT count(b) FROM App\Entity\booking b')->getSingleScalarResult();
    }
    public function getAdStats($direction){
        return $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
        FROM App\Entity\Comment c
        JOIN c.ad a
        JOIN a.author u
        GROUP BY a
        ORDER BY note ' .$direction
        )->setMaxResults(5)->getResult();
    }

}