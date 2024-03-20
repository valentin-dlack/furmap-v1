<?php

namespace App\Entity;

use App\Repository\SocialNetworksRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SocialNetworksRepository::class)]
class SocialNetworks
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'socialNetworks', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\Column(length: 38, nullable: true)]
    private ?string $discord = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $skype = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $deviantart = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $soundcloud = null;

    #[ORM\Column(length: 21, nullable: true)]
    private ?string $reddit = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $furaffinity = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $twitter = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $facebook = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $youtube = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $instagram = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $telegram = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $steam = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $twitch = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $vk = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $github = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gitlab = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tiktok = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tumblr = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDiscord(): ?string
    {
        return $this->discord;
    }

    public function setDiscord(?string $discord): self
    {
        $this->discord = $discord;

        return $this;
    }

    public function getSkype(): ?string
    {
        return $this->skype;
    }

    public function setSkype(?string $skype): self
    {
        $this->skype = $skype;

        return $this;
    }

    public function getDeviantart(): ?string
    {
        return $this->deviantart;
    }

    public function setDeviantart(?string $deviantart): self
    {
        $this->deviantart = $deviantart;

        return $this;
    }

    public function getSoundcloud(): ?string
    {
        return $this->soundcloud;
    }

    public function setSoundcloud(?string $soundcloud): self
    {
        $this->soundcloud = $soundcloud;

        return $this;
    }

    public function getReddit(): ?string
    {
        return $this->reddit;
    }

    public function setReddit(?string $reddit): self
    {
        $this->reddit = $reddit;

        return $this;
    }

    public function getFuraffinity(): ?string
    {
        return $this->furaffinity;
    }

    public function setFuraffinity(?string $furaffinity): self
    {
        $this->furaffinity = $furaffinity;

        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getYoutube(): ?string
    {
        return $this->youtube;
    }

    public function setYoutube(?string $youtube): self
    {
        $this->youtube = $youtube;

        return $this;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(?string $instagram): self
    {
        $this->instagram = $instagram;

        return $this;
    }

    public function getTelegram(): ?string
    {
        return $this->telegram;
    }

    public function setTelegram(?string $telegram): self
    {
        $this->telegram = $telegram;

        return $this;
    }

    public function getSteam(): ?string
    {
        return $this->steam;
    }

    public function setSteam(?string $steam): self
    {
        $this->steam = $steam;

        return $this;
    }

    public function getTwitch(): ?string
    {
        return $this->twitch;
    }

    public function setTwitch(?string $twitch): self
    {
        $this->twitch = $twitch;

        return $this;
    }

    public function getVk(): ?string
    {
        return $this->vk;
    }

    public function setVk(?string $vk): self
    {
        $this->vk = $vk;

        return $this;
    }

    public function getGithub(): ?string
    {
        return $this->github;
    }

    public function setGithub(?string $github): self
    {
        $this->github = $github;

        return $this;
    }

    public function getGitlab(): ?string
    {
        return $this->gitlab;
    }

    public function setGitlab(?string $gitlab): self
    {
        $this->gitlab = $gitlab;

        return $this;
    }

    public function getTiktok(): ?string
    {
        return $this->tiktok;
    }

    public function setTiktok(?string $tiktok): self
    {
        $this->tiktok = $tiktok;

        return $this;
    }

    public function getTumblr(): ?string
    {
        return $this->tumblr;
    }

    public function setTumblr(?string $tumblr): self
    {
        $this->tumblr = $tumblr;

        return $this;
    }

    public function getAllSocials(): array
    {
        return [
            'discord' => $this->getDiscord(),
            'skype' => $this->getSkype(),
            'deviantart' => $this->getDeviantart(),
            'soundcloud' => $this->getSoundcloud(),
            'reddit' => $this->getReddit(),
            'furaffinity' => $this->getFuraffinity(),
            'twitter' => $this->getTwitter(),
            'facebook' => $this->getFacebook(),
            'youtube' => $this->getYoutube(),
            'instagram' => $this->getInstagram(),
            'telegram' => $this->getTelegram(),
            'steam' => $this->getSteam(),
            'twitch' => $this->getTwitch(),
            'vk' => $this->getVk(),
            'github' => $this->getGithub(),
            'gitlab' => $this->getGitlab(),
            'tiktok' => $this->getTiktok(),
            'tumblr' => $this->getTumblr(),
        ];
    }
}
