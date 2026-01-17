<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Track;
use App\Repository\TrackRepository;

final class TrackController extends AbstractController
{
    public function __construct(
        private readonly TrackRepository $trackRepository,
        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route('/api/tracks', name: 'app_tracks', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $tracks = $this->trackRepository->findAll();
        return $this->json($tracks, 200, [], ['groups' => ['track:read']]);
    }

    // POST /api/tracks - Create a new track
    #[Route('/api/tracks', name: 'app_tracks_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $track = new Track();
        $track->setTitle($data['title'] ?? null);
        $track->setArtist($data['artist'] ?? null);
        $track->setDuration($data['duration'] ?? null);
        
        if (isset($data['isrc'])) {
            $track->setIsrc($data['isrc']);
        }
        
        // Validate the track
        $errors = $this->validator->validate($track);
        
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            
            return $this->json([
                'message' => 'Validation failed',
                'errors' => $errorMessages
            ], 422);
        }
        
        // Persist and flush the track
        $this->entityManager->persist($track);
        $this->entityManager->flush();
        
        return $this->json($track, 201, [], ['groups' => ['track:read']]);
    }

    // PUT /api/tracks/{id} - Update a track
    #[Route('/api/tracks/{id}', name: 'app_tracks_update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $track = $this->trackRepository->find($id);
        if (!$track) {
            return $this->json(['message' => 'Track not found'], 404);
        }

        $track->setTitle($data['title'] ?? null);
        $track->setArtist($data['artist'] ?? null);
        $track->setDuration($data['duration'] ?? null);
        
        if (isset($data['isrc'])) {
            $track->setIsrc($data['isrc']);
        }

        // Validate the track
        $errors = $this->validator->validate($track);
        
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            
            return $this->json([
                'message' => 'Validation failed',
                'errors' => $errorMessages
            ], 422);
        }

        // Persist and flush the track
        $this->entityManager->persist($track);
        $this->entityManager->flush();
        
        return $this->json($track, 200, [], ['groups' => ['track:read']]);
    }

    // DELETE /api/tracks/{id} - Delete a track
    #[Route('/api/tracks/{id}', name: 'app_tracks_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $track = $this->trackRepository->find($id);
        $this->trackRepository->remove($track);
        return $this->json(null, 204);
    }

    // GET /api/tracks/{id} - Get a track
    #[Route('/api/tracks/{id}', name: 'app_tracks_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $track = $this->trackRepository->find($id);
        return $this->json($track, 200, [], ['groups' => ['track:read']]);
    }
}
