<?php


namespace App\services\image;


use App\Entity\Post;
use App\services\post\PostServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImageServices extends AbstractController
{
    private $postServices;

    public function __construct(PostServices $postServices)
    {
        $this->postServices = $postServices;
    }

    public function ImageUpload($form, Post $post, $img)
    {
        $images = $form->get('images')->getData();
        foreach ($images as $image) {
            $folder = md5(uniqid()) . '.' . $image->guessExtension();
            $image->move(
                $this->getParameter('images_directory'),
                $folder
            );
            array_push($img, $folder);
        }
        $post->setImages($img);
    }

    public function pictureUpload($form, $user)
    {
        $image = $form->get('image')->getData();
        $folder = md5(uniqid()) . '.' . $image->guessExtension();

        $image->move(
            $this->getParameter('images_directory'),
            $folder
        );
        $user->setImage($folder);
    }

    public function videoAdd($form, $post, $url)
    {
        foreach ($data = $form->get('video')->getData() as $video) {
            $a = $this->postServices->verifyURL($video);
            array_push($url, $a);

        }
        $post->setVideo($url);
    }
}
