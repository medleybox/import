<?php

namespace App\Controller;

use App\Service\{Youtube, Soundcloud};

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ImportController extends AbstractController
{
    public function __construct(Youtube $youtube, Soundcloud $soundcloud)
    {
        $this->youtube = $youtube;
        $this->soundcloud = $soundcloud;
    }
    /**
     * @Route("/", name="import_index")
     */
    public function index()
    {
        dump('here');

        return $this->json([
            'youtube' => $this->youtube->fetchAll(),
            'soundcloud' => $this->soundcloud->fetchAll(),
        ]);
    }

    /**
     * @Route("/youtube", name="import_youtube")
     */
    public function youtube(Request $request)
    {
        $url = $request->request->get('url');

        $import = $this->youtube->setUrl($url)->import();

        return $this->json(['import' => $import]);
    }

    /**
     * @Route("/soundcloud", name="import_soundcloud")
     */
    public function soundcloud(Request $request)
    {
        return $this->json($this->soundcloud->fetchAll());
    }
}
