<?php

namespace UserApi\Entity;

use DateTimeImmutable;
use Symfony\Component\Uid\UuidV4;
use UserApi\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="users",indexes={@ORM\Index(name="user_name", columns={"first_name"})})
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidV4Generator::class)
     */
    private UuidV4 $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\SerializedName("firstName")
     * @Assert\Type(
     *     type="string",
     *     message = "You cant create user with firstName {{ value }}. It is not a valid {{ type }}."
     * )
     * @Assert\NotBlank(message="You cant create user with empty firstName.")
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\SerializedName("lastName")
     * @Assert\Type(
     *     type="string",
     *     message = "You cant create user with lastName {{ value }}. It is not a valid {{ type }}."
     * )
     * @Assert\NotBlank(message="You cant create user with empty lastName.")
     */
    private string $lastName;

    /**
     * @ORM\Column(type="json")
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("phoneNumbers")
     * @Assert\Count(
     *      min = 1,
     *      minMessage = "You must specify at least one phone number",
     * )
     */
    private array $phoneNumbers = [];

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Gedmo\Timestampable(on="create")
     */
    private DateTimeImmutable $createdAt;

    public function getId(): ?UuidV4
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumbers(): ?array
    {
        return $this->phoneNumbers;
    }

    public function setPhoneNumbers(array $phoneNumbers): self
    {
        $this->phoneNumbers = $phoneNumbers;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUserPhones() : string
    {
        return implode(', ', $this->phoneNumbers);
    }
}
