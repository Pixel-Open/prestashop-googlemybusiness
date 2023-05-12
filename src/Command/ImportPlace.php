<?php

namespace Pixel\Module\GoogleMyBusiness\Command;

use Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Pixel\Module\GoogleMyBusiness\Entity\GooglePlace;
use Pixel\Module\GoogleMyBusiness\Entity\GoogleReview;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportPlace extends Command
{
    /**
     * Command configuration
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('google_my_business:import_place')
            ->addArgument('language', InputArgument::REQUIRED, 'Language');
    }

    /**
     * Import the place
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     * @throws ORMException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $kernel;

        $apiUrl  = 'https://maps.googleapis.com/maps/api/place/details/json';
        $placeIds = Configuration::get('GOOGLE_MY_BUSINESS_PLACE_IDS');
        if (!$placeIds) {
            $output->write('No places to import' . "\n");
            return;
        }
        $placeIds = preg_split('/\r\n|[\r\n]/', $placeIds);
        $language = substr($input->getArgument('language'), 0, 2);

        foreach ($placeIds as $placeId) {
            $params = [
                'fields'       => 'name,international_phone_number,rating,opening_hours,user_ratings_total,reviews,price_level',
                'reviews_sort' => 'newest',
                'key'          => Configuration::get('GOOGLE_MY_BUSINESS_API_KEY'),
                'placeid'      => $placeId,
                'language'     => $language,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl . '?' . http_build_query($params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $response = json_decode(curl_exec($ch), true);

            if (($status = $response['status'] ?? 'CANNOT REACH API') !== 'OK') {
                $output->write($placeId . ' - ' . $status . "\n");
                continue;
            }

            $result = $response['result'];

            /** @var EntityManager $entityManager */
            $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

            /* Add place */
            $repository = $entityManager->getRepository(GooglePlace::class);

            /** @var GooglePlace|null $place */
            $googlePlace = $repository->findOneBy(['placeId' => $placeId, 'language' => $language]) ?: new GooglePlace();
            $googlePlace
                ->setPlaceId($placeId)
                ->setLanguage($language)
                ->setName($result['name'])
                ->setPhone($result['international_phone_number'] ?? null)
                ->setOpeningHoursPeriods(json_encode($result['opening_hours']['periods'] ?? []))
                ->setOpeningHoursWeekdayText(json_encode($result['opening_hours']['weekday_text'] ?? []))
                ->setRating((float)($result['rating'] ?? 5))
                ->setUserRatingsTotal((int)($result['user_ratings_total'] ?? 0))
                ->setPriceLevel((int)($result['price_level'] ?? 0));

            $entityManager->persist($googlePlace);
            $entityManager->flush();

            /* Add last reviews */
            $repository = $entityManager->getRepository(GoogleReview::class);

            foreach (($result['reviews'] ?? []) as $review) {
                $exists = $repository->findOneBy(
                    [
                        'placeId' => $placeId,
                        'time'    => $review['time'],
                    ]
                );
                if ($exists) {
                    continue;
                }

                $googleReview = new GoogleReview();
                $googleReview
                    ->setPlaceId($placeId)
                    ->setAuthorName($review['author_name'])
                    ->setAuthorUrl($review['author_url'] ?? '')
                    ->setLanguage($review['language'] ?? null)
                    ->setOriginalLanguage($review['original_language'] ?? null)
                    ->setProfilePhotoUrl($review['profile_photo_url'])
                    ->setRating((int)$review['rating'])
                    ->setComment($this->removeEmoji($review['text']))
                    ->setTime((int)$review['time'])
                    ->setTranslated((bool)$review['translated'])
                    ->setEnabled(1);
                $entityManager->persist($googleReview);
            }

            $entityManager->flush();

            $output->write($placeId . ' - OK' . "\n");
        }

        $output->write('Import done!' . "\n");
    }

    /**
     * @param string $text
     *
     * @return string
     */
    protected function removeEmoji(string $text)
    {
        $symbols = "\x{1F100}-\x{1F1FF}"
            . "\x{1F300}-\x{1F5FF}"
            . "\x{1F600}-\x{1F64F}"
            . "\x{1F680}-\x{1F6FF}"
            . "\x{1F900}-\x{1F9FF}"
            . "\x{2600}-\x{26FF}"
            . "\x{2700}-\x{27BF}";

        return (string)preg_replace('/['. $symbols . ']+/u', '', $text);
    }
}
