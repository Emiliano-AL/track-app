<?php

namespace App\Service;

use App\Entity\Track;
use App\Repository\TrackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final readonly class TrackService
{
    public function __construct(
        private TrackRepository $trackRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * Get all tracks
     *
     * @return Track[]
     */
    public function getAllTracks(): array
    {
        return $this->trackRepository->findAll();
    }

    /**
     * Get a track by ID
     *
     * @param int $id
     * @return Track|null
     */
    public function getTrackById(int $id): ?Track
    {
        return $this->trackRepository->find($id);
    }

    /**
     * Create a new track from data
     *
     * @param array<string, mixed> $data
     * @return array{success: bool, track?: Track, errors?: array<string, string>}
     */
    public function createTrack(array $data): array
    {
        $track = new Track();
        $track->setTitle($data['title'] ?? null);
        $track->setArtist($data['artist'] ?? null);
        $track->setDuration($data['duration'] ?? null);

        if (isset($data['isrc'])) {
            $track->setIsrc($data['isrc']);
        }

        $errors = $this->validateTrack($track);
        if (count($errors) > 0) {
            return [
                'success' => false,
                'errors' => $this->formatValidationErrors($errors),
            ];
        }

        $this->entityManager->persist($track);
        $this->entityManager->flush();

        return [
            'success' => true,
            'track' => $track,
        ];
    }

    /**
     * Update an existing track with new data
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @return array{success: bool, track?: Track, errors?: array<string, string>, notFound?: bool}
     */
    public function updateTrack(int $id, array $data): array
    {
        $track = $this->trackRepository->find($id);
        if (!$track) {
            return [
                'success' => false,
                'notFound' => true,
            ];
        }

        $track->setTitle($data['title'] ?? $track->getTitle());
        $track->setArtist($data['artist'] ?? $track->getArtist());
        $track->setDuration($data['duration'] ?? $track->getDuration());

        if (isset($data['isrc'])) {
            $track->setIsrc($data['isrc']);
        }

        $errors = $this->validateTrack($track);
        if (count($errors) > 0) {
            return [
                'success' => false,
                'errors' => $this->formatValidationErrors($errors),
            ];
        }

        $this->entityManager->persist($track);
        $this->entityManager->flush();

        return [
            'success' => true,
            'track' => $track,
        ];
    }

    /**
     * Delete a track by ID
     *
     * @param int $id
     * @return bool Returns true if track was deleted, false if not found
     */
    public function deleteTrack(int $id): bool
    {
        $track = $this->trackRepository->find($id);
        if (!$track) {
            return false;
        }

        $this->entityManager->remove($track);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Validate a track entity
     *
     * @param Track $track
     * @return ConstraintViolationListInterface
     */
    private function validateTrack(Track $track): ConstraintViolationListInterface
    {
        return $this->validator->validate($track);
    }

    /**
     * Format validation errors into an associative array
     *
     * @param ConstraintViolationListInterface $errors
     * @return array<string, string>
     */
    private function formatValidationErrors(ConstraintViolationListInterface $errors): array
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[$error->getPropertyPath()] = $error->getMessage();
        }

        return $errorMessages;
    }
}
