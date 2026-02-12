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
    public function show(int $id, MeterRepository $meterRepo): JsonResponse
    {
        $val = $meterRepo->find($id);
        if ($val === null) {
            return $this->json(['error' => 'La valeur de l\'id ne correspond pas'], 404);
        }
        return $this->json($val);
    }

    #[Route('/api/meters', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
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

        try {
            $em->persist($meter);
            $em->flush();
        } catch (\Exception){
            return $this->json(['error' => 'Une erreur s\'est produite pendant l\'ajout à la BD'], 500);
        }

        return $this->json($meter, 201);
    }

    #[Route('/api/meter/{id}', methods:['DELETE'])]
    public function delete(EntityManagerInterface $em, int $id, MeterRepository $meterRepo): JsonResponse{
        $val = $meterRepo->find($id);
        if ($val === null){
            return $this->json(['error' => 'l\'id ne correspond pas'], 404);
        }

        try {
            $em->remove($val);
            $em->flush();
        } catch (\Exception){
            return $this->json(['error' => 'Une erreur s\'est produite lors de la suppression dans la BD'], 500);
        }

        return $this->json(['OK' => 'La suppression s\'est bien passée']);
    }
}
