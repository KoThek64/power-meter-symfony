<?php

namespace App\Controller;

use App\Entity\Meter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MeterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class MeterController extends AbstractController
{
    #[Route('/meter', name: 'app_meter')]
    public function index(MeterRepository $meterRepo): JsonResponse
    {
        $all = $meterRepo->findAll();
        return $this->json($all);
    }

    #[Route('/api/meters', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $em) {
        $val = $request->toArray();
        $meter = new Meter;
        $meter->setSerialNumber($val['serialNumber']);
        $meter->setLocation($val['location']);

        $em->persist($meter);
        $em->flush();

        return $this->json($meter, 201);
    }
}
