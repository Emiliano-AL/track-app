<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Track;
use App\Repository\TrackRepository;

final class TrackController extends AbstractController
{
    public function __construct(
        private readonly TrackRepository $trackRepository,
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
        $track->setTitle($data['title']);
        $track->setArtist($data['artist']);
        $track->setDuration($data['duration']);
    }

    // PUT /api/tracks/{id} - Update a track
    #[Route('/api/tracks/{id}', name: 'app_tracks_update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $track = $this->trackRepository->find($id);
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
