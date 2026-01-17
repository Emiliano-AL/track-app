<?php

namespace App\Controller;

use App\Service\TrackService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class TrackController extends AbstractController
{
    public function __construct(
        private readonly TrackService $trackService,
    ) {}

    #[Route('/api/tracks', name: 'app_tracks', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $tracks = $this->trackService->getAllTracks();
        return $this->json($tracks, 200, [], ['groups' => ['track:read']]);
    }

    // POST /api/tracks - Create a new track
    #[Route('/api/tracks', name: 'app_tracks_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $result = $this->trackService->createTrack($data);
        
        if (!$result['success']) {
            return $this->json([
                'message' => 'Validation failed',
                'errors' => $result['errors']
            ], 422);
        }
        
        return $this->json($result['track'], 201, [], ['groups' => ['track:read']]);
    }

    // PUT /api/tracks/{id} - Update a track
    #[Route('/api/tracks/{id}', name: 'app_tracks_update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $result = $this->trackService->updateTrack($id, $data);
        
        if (!$result['success']) {
            if (isset($result['notFound']) && $result['notFound']) {
                return $this->json(['message' => 'Track not found'], 404);
            }
            
            return $this->json([
                'message' => 'Validation failed',
                'errors' => $result['errors']
            ], 422);
        }
        
        return $this->json($result['track'], 200, [], ['groups' => ['track:read']]);
    }

    // DELETE /api/tracks/{id} - Delete a track
    #[Route('/api/tracks/{id}', name: 'app_tracks_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $deleted = $this->trackService->deleteTrack($id);
        
        if (!$deleted) {
            return $this->json(['message' => 'Track not found'], 404);
        }
        
        return $this->json(null, 204);
    }

    // GET /api/tracks/{id} - Get a track
    #[Route('/api/tracks/{id}', name: 'app_tracks_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $track = $this->trackService->getTrackById($id);
        
        if (!$track) {
            return $this->json(['message' => 'Track not found'], 404);
        }
        
        return $this->json($track, 200, [], ['groups' => ['track:read']]);
    }
}
