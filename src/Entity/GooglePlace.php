<?php

namespace Pixel\Module\GoogleMyBusiness\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class GooglePlace
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
     * @ORM\Column(name="language", type="string", length=2, nullable=true)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="text", nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="opening_hours_periods", type="text", nullable=true)
     */
    private $openingHoursPeriods;

    /**
     * @var string
     *
     * @ORM\Column(name="opening_hours_weekday_text", type="text", nullable=true)
     */
    private $openingHoursWeekdayText;

    /**
     * @var float
     *
     * @ORM\Column(name="rating", type="decimal", precision=4, scale=2, nullable=true)
     */
    private $rating;

    /**
     * @var int
     *
     * @ORM\Column(name="user_ratings_total", type="integer", nullable=true)
     */
    private $userRatingsTotal;

    /**
     * @var int
     *
     * @ORM\Column(name="price_level", type="integer", nullable=true)
     */
    private $priceLevel;

    /**
     * @var GoogleReview[]
     */
    private $reviews = [];

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
     * @return GooglePlace
     */
    public function setId(?int $id): GooglePlace
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
     * @return GooglePlace
     */
    public function setPlaceId(string $placeId): GooglePlace
    {
        $this->placeId = $placeId;

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
    public function setLanguage(?string $language): GooglePlace
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return GooglePlace
     */
    public function setName(string $name): GooglePlace
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return GooglePlace
     */
    public function setPhone(string $phone): GooglePlace
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOpeningHoursPeriods(): ?string
    {
        return $this->openingHoursPeriods;
    }

    /**
     * @param string|null $openingHoursPeriods
     *
     * @return GooglePlace
     */
    public function setOpeningHoursPeriods(?string $openingHoursPeriods): GooglePlace
    {
        $this->openingHoursPeriods = $openingHoursPeriods;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOpeningHoursWeekdayText(): ?string
    {
        return $this->openingHoursWeekdayText;
    }

    /**
     * @param string|null $openingHoursWeekdayText
     *
     * @return GooglePlace
     */
    public function setOpeningHoursWeekdayText(?string $openingHoursWeekdayText): GooglePlace
    {
        $this->openingHoursWeekdayText = $openingHoursWeekdayText;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getRating(): ?float
    {
        return $this->rating;
    }

    /**
     * @param float|null $rating
     *
     * @return GooglePlace
     */
    public function setRating(?float $rating): GooglePlace
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getUserRatingsTotal(): ?int
    {
        return $this->userRatingsTotal;
    }

    /**
     * @param int|null $userRatingsTotal
     *
     * @return GooglePlace
     */
    public function setUserRatingsTotal(?int $userRatingsTotal): GooglePlace
    {
        $this->userRatingsTotal = $userRatingsTotal;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPriceLevel(): ?int
    {
        return $this->priceLevel;
    }

    /**
     * @param int|null $priceLevel
     *
     * @return GooglePlace
     */
    public function setPriceLevel(?int $priceLevel): GooglePlace
    {
        $this->priceLevel = $priceLevel;

        return $this;
    }

    /**
     * @return GoogleReview[]
     */
    public function getReviews(): array
    {
        return $this->reviews;
    }

    /**
     * @param GoogleReview[] $reviews
     *
     * @return GooglePlace
     */
    public function setReviews(array $reviews): GooglePlace
    {
        $this->reviews = $reviews;

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
            'opening_hours_periods' => $this->getOpeningHoursPeriods(),
            'opening_hours_weekday_text' => $this->getOpeningHoursWeekdayText(),
            'rating' => $this->getRating(),
            'user_ratings_total' => $this->getUserRatingsTotal(),
            'reviews' => $this->getReviews(),
        ];
    }
}
