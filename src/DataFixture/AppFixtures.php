<?php

namespace UserApi\DataFixture;

use UserApi\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class AppFixtures
 * @package App\DataFixture
 */
class AppFixtures extends Fixture
{
    private const USERS_FIXTURE = [
        [
            'firstName' => 'Lucy',
            'lastName' => 'Lee',
            'phoneNumbers' => ['937-99-92', '983-83-88-88'],
        ],
        [
            'firstName' => 'Ken',
            'lastName' => 'Kaneky',
            'phoneNumbers' => ['937-99-92', '983-83-88-88'],
        ],
        [
            'firstName' => 'Barbara',
            'lastName' => 'Streisand',
            'phoneNumbers' => ['937-99-92', '983-83-88-88'],
        ],
        [
            'firstName' => 'Kevin',
            'lastName' => 'Hart',
            'phoneNumbers' => ['937-99-92', '983-83-88-88'],
        ],
        [
            'firstName' => 'Susan',
            'lastName' => 'Janet',
            'phoneNumbers' => ['937-99-92', '983-83-88-88'],
        ],
        [
            'firstName' => 'Emma',
            'lastName' => 'Stone',
            'phoneNumbers' => ['937-99-92', '983-83-88-88'],
        ],
    ];

    private DenormalizerInterface $denormalizer;

    /**
     * AppFixtures constructor.
     * @param DenormalizerInterface $denormalizer
     */
    public function __construct(DenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }

    /**
     * @param ObjectManager $manager
     * @throws ExceptionInterface
     */
    public function load(ObjectManager $manager) : void
    {
        foreach (self::USERS_FIXTURE as $userData) {
            /** @var User $astrologer */
            $astrologer = $this->denormalizer->denormalize($userData, User::class);
            $manager->persist($astrologer);
        }

        $manager->flush();
    }
}
