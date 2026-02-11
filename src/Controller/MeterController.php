<?php

namespace App\Controller;

use App\Entity\Meter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MeterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class MeterController extends AbstractController
{
    #[Route('/meter', name: 'app_meter')]
    public function index(MeterRepository $meterRepo): JsonResponse
    {
        $all = $meterRepo->findAll();
        return $this->json($all);
    }

    #[Route('/meter/{id}', methods: ['GET'])]
    public function show(int $id, MeterRepository $meterRepo)
    {
        $val = $meterRepo->find($id);
        if ($val === null) {
            return $this->json(['error' => 'La valeur de l\'id ne correspond pas'], 404);
        }
        return $this->json($val);
    }

    #[Route('/api/meters', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $val = $request->toArray();
        if (!isset($val['serialNumber'], $val['location'])) {
            return $this->json(['error' => 'Il faut un serialNumber et une location'], 400);
        }

        $meter = new Meter;
        $meter->setSerialNumber($val['serialNumber']);
        $meter->setLocation($val['location']);

        $errors = $validator->validate($meter);

        if (count($errors) > 0){
            return $this->json(['error' => (string) $errors], 400);
        }

        $em->persist($meter);
        $em->flush();

        return $this->json($meter, 201);
    }
}
