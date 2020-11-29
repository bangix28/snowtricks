<?php


namespace App\services\post;


use App\services\videoServices\videoLinkServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostServices extends AbstractController
{
    private  $videoEncode;

    private $entityManager;

    public function __construct(videoLinkServices $videoEncode, EntityManagerInterface $entityManager)
    {
        $this->videoEncode = $videoEncode;
        $this->entityManager = $entityManager;
    }


    public function verifyURL($url)
    {
        $video = $this->videoEncode->extractPlatformFromURL($url);
        if ($video !== false) {
           return $video;
        } else {
            $this->addFlash('danger','Only Youtube or dalymotion video are allowed');
            return false;
        }
    }

}
