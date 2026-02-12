<?php

namespace App\Controller;

use App\Entity\Reading;
use App\Repository\MeterRepository;
use App\Repository\ReadingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ReadingController extends AbstractController
{
    #[Route('/reading', name: 'app_reading')]
    public function index(ReadingRepository $readingRepo): JsonResponse
    {
        $all = $readingRepo->findAll();
        return $this->json($all);
    }

    #[Route('/api/meter/{id}/reading', methods: ['GET'])]
    public function show(MeterRepository $meterRepo, int $id): JsonResponse
    {
        $meter = $meterRepo->find($id);

        if ($meter === null) {
            return $this->json(['error' => 'Aucun meter trouvé pour l\'id correspondant'], 404);
        }

        $readings = $meter->getReadings();

        if ($readings->isEmpty()) {
            return $this->json(['error' => 'Aucun readings trouvé dans le meter'], 404);
        }

        return $this->json($readings, 200);
    }

    #[Route('/api/reading', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $em, MeterRepository $meterRepo, ValidatorInterface $validator): JsonResponse
    {
        $val = $request->toArray();
        if (!isset($val['meter_id'], $val['value'])) {
            return $this->json(['error' => 'meter_id et value sont requis'], 400);
        }
        $meter = $meterRepo->find($val['meter_id']);

        if ($meter === null) {
            return $this->json(['error' => 'Problème au niveau du meter'], 404);
        }

        $reading = new Reading;
        $reading->setMeter($meter);
        $reading->setReadAt(new \DateTimeImmutable());
        $reading->setValue($val['value']);

        $errors = $validator->validate($reading);

        if (count($errors) > 0) {
            return $this->json(['error' => (string) $errors], 400);
        }

        $em->persist($reading);
        $em->flush();

        return $this->json($reading, 201);
    }
}
