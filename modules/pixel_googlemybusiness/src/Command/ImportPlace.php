<?php

namespace Pixel\Module\GoogleMyBusiness\Command;

use Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Pixel\Module\GoogleMyBusiness\Entity\GooglePlace;
use Symfony\Component\Console\Command\Command;
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
        $this->setName('google_my_business:import_place');
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
        $placeId = Configuration::get('GOOGLE_MY_BUSINESS_PLACE_ID');
        $params  = [
            'fields'  => 'rating,opening_hours,user_ratings_total',
            'key'     => Configuration::get('GOOGLE_MY_BUSINESS_API_KEY'),
            'placeid' => $placeId,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = json_decode(curl_exec($ch), true);

        if (($status = $response['status'] ?? 'CANNOT REACH API') !== 'OK') {
            $output->write($status);
            return;
        }

        $result = $response['result'];

        /** @var EntityManager $entityManager */
        $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $repository = $entityManager->getRepository(GooglePlace::class);

        /** @var GooglePlace|null $place */
        $googlePlace = $repository->findOneBy(['placeId' => $placeId]) ?: new GooglePlace();
        $googlePlace
            ->setPlaceId($placeId)
            ->setOpeningHoursPeriods(json_encode($result['opening_hours']['periods'] ?? []))
            ->setOpeningHoursWeekdayText(json_encode($result['opening_hours']['weekday_text'] ?? []))
            ->setRating((float)($result['rating'] ?? 5))
            ->setUserRatingsTotal((int)($result['user_ratings_total'] ?? 0));

        $entityManager->persist($googlePlace);
        $entityManager->flush();

        $output->write('Import done!');
    }
}
