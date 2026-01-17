<?php

namespace App\Entity;

use App\Repository\TrackRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TrackRepository::class)]
class Track
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['track:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['track:read'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['track:read'])]
    private ?string $artist = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['track:read'])]
    private ?int $duration = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['track:read'])]
    private ?string $isrc = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getArtist(): ?string
    {
        return $this->artist;
    }

    public function setArtist(string $artist): static
    {
        $this->artist = $artist;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getIsrc(): ?string
    {
        return $this->isrc;
    }

    public function setIsrc(?string $isrc): static
    {
        $this->isrc = $isrc;

        return $this;
    }
}
