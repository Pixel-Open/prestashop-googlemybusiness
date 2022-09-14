<?php

namespace Pixel\Module\GoogleMyBusiness\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class GoogleReview
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="place_id", type="string", length=255, nullable=false)
     */
    private $placeId;

    /**
     * @var string
     *
     * @ORM\Column(name="author_name", type="string", length=255, nullable=true)
     */
    private $authorName;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=2, nullable=true)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(name="original_language", type="string", length=2, nullable=true)
     */
    private $originalLanguage;

    /**
     * @var string
     *
     * @ORM\Column(name="profile_photo_url", type="string", length=255, nullable=true)
     */
    private $profilePhotoUrl;

    /**
     * @var int
     *
     * @ORM\Column(name="rating", type="smallint", nullable=true)
     */
    private $rating;

    /**
     * @var string
     *
     * @ORM\Column(name="relative_time_description", type="string", length=255, nullable=true)
     */
    private $relativeTimeDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var int
     *
     * @ORM\Column(name="time", type="integer", nullable=true)
     */
    private $time;

    /**
     * @var bool
     *
     * @ORM\Column(name="translated", type="boolean", nullable=true)
     */
    private $translated;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     */
    private $enabled;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     *
     * @return GoogleReview
     */
    public function setId(?int $id): GoogleReview
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlaceId(): string
    {
        return $this->placeId;
    }

    /**
     * @param string $placeId
     *
     * @return GoogleReview
     */
    public function setPlaceId(string $placeId): GoogleReview
    {
        $this->placeId = $placeId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAuthorName(): ?string
    {
        return $this->authorName;
    }

    /**
     * @param string|null $authorName
     *
     * @return GoogleReview
     */
    public function setAuthorName(?string $authorName): GoogleReview
    {
        $this->authorName = $authorName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * @param string|null $language
     *
     * @return GoogleReview
     */
    public function setLanguage(?string $language): GoogleReview
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOriginalLanguage(): ?string
    {
        return $this->originalLanguage;
    }

    /**
     * @param string|null $originalLanguage
     *
     * @return GoogleReview
     */
    public function setOriginalLanguage(?string $originalLanguage): GoogleReview
    {
        $this->originalLanguage = $originalLanguage;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProfilePhotoUrl(): ?string
    {
        return $this->profilePhotoUrl;
    }

    /**
     * @param string|null $profilePhotoUrl
     *
     * @return GoogleReview
     */
    public function setProfilePhotoUrl(?string $profilePhotoUrl): GoogleReview
    {
        $this->profilePhotoUrl = $profilePhotoUrl;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getRating(): ?int
    {
        return $this->rating;
    }

    /**
     * @param int|null $rating
     *
     * @return GoogleReview
     */
    public function setRating(?int $rating): GoogleReview
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRelativeTimeDescription(): ?string
    {
        return $this->relativeTimeDescription;
    }

    /**
     * @param string|null $relativeTimeDescription
     *
     * @return GoogleReview
     */
    public function setRelativeTimeDescription(?string $relativeTimeDescription): GoogleReview
    {
        $this->relativeTimeDescription = $relativeTimeDescription;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     *
     * @return GoogleReview
     */
    public function setComment(?string $comment): GoogleReview
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTime(): ?int
    {
        return $this->time;
    }

    /**
     * @param int|null $time
     *
     * @return GoogleReview
     */
    public function setTime(?int $time): GoogleReview
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getTranslated(): ?bool
    {
        return $this->translated;
    }

    /**
     * @param bool|null $translated
     *
     * @return GoogleReview
     */
    public function setTranslated(?bool $translated): GoogleReview
    {
        $this->translated = $translated;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @param bool|null $translated
     *
     * @return GoogleReview
     */
    public function setEnabled(?bool $enabled): GoogleReview
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'place_id' => $this->getPlaceId(),
            'author_name' => $this->getAuthorName(),
            'language' => $this->getLanguage(),
            'original_language' => $this->getOriginalLanguage(),
            'profile_photo_url' => $this->getProfilePhotoUrl(),
            'relative_time_description' => $this->getRelativeTimeDescription(),
            'comment' => $this->getComment(),
            'time' => $this->getTime(),
            'translated' => $this->getTranslated(),
            'enabled' => $this->getEnabled(),
        ];
    }
}
