<?php

namespace App\Controller;

use App\Service\Minio;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class DeleteController extends AbstractController
{
    public function __construct(Minio $minio)
    {
        $this->minio = $minio;
    }
    /**
     * @Route("/delete", name="delete_delete")
     */
    public function delete(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $path = $data['path'];
        $delete = $this->minio->delete($path);

        return $this->json(['delte' => $delete]);
    }
}
