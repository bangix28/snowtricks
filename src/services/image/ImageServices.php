<?php


namespace App\services\image;


use App\Entity\Post;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ImageServices extends AbstractController
{
    public function ImageUpload( $form,Post $post)
    {
     $images = $form->get('images')->getData();
     $img = [];
     foreach($images as $image){
         $folder = md5(uniqid()).'.' .$image->guessExtension();
         $image->move(
             $this->getParameter('images_directory'),
             $folder
         );
         array_push($img,$folder);
     }
     $post->setImages($img);
    }

    public function pictureUpload($form, $user)
    {
        $image = $form->get('image')->getData();
        $folder = md5(uniqid()).'.'.$image->guessExtension();

        $image->move(
            $this->getParameter('images_directory'),
            $folder
        );
        $user->setImage($folder);
    }
}
