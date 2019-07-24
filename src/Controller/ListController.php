<?php

namespace App\Controller;

use App\Service\{Youtube, Soundcloud};

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ListController extends AbstractController
{
    public function __construct(Youtube $youtube, Soundcloud $soundcloud)
    {
        $this->youtube = $youtube;
        $this->soundcloud = $soundcloud;
    }
    /**
     * @Route("/all", name="listAll")
     */
    public function index()
    {
        return $this->json([
            'youtube' => $this->youtube->fetchAll(),
            'soundcloud' => $this->soundcloud->fetchAll(),
        ]);
    }

    /**
     * @Route("/youtube", name="listYoutube")
     */
    public function youtube(Request $request)
    {
        return $this->json($this->youtube->fetchAll());
    }

    /**
     * @Route("/soundcloud", name="listSoundcloud")
     */
    public function soundcloud(Request $request)
    {
        return $this->json($this->soundcloud->fetchAll());
    }
}
