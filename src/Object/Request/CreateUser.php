<?php

namespace UserApi\Object\Request;

use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CreateUser
 * @package UserApi\Object\Request
 */
class CreateUser
{
    /**
     * @var string
     *
     * @JMS\Type("string")
     * @JMS\SerializedName("firstName")
     * @Assert\Type(
     *     type="string",
     *     message = "You cant create user with firstName {{ value }}. It is not a valid {{ type }}."
     * )
     * @Assert\NotBlank(message="You cant create user with empty firstName.")
     */
    private string $firstName;

    /**
     * @var string
     *
     * @JMS\Type("string")
     * @JMS\SerializedName("lastName")
     * @Assert\Type(
     *     type="string",
     *     message = "You cant create user with lastName {{ value }}. It is not a valid {{ type }}."
     * )
     * @Assert\NotBlank(message="You cant create user with empty lastName.")
     */
    private string $lastName;

    /**
     * @var ArrayCollection
     *
     * @JMS\Type("ArrayCollection<string>")
     * @JMS\SerializedName("phoneNumbers")
     * @Assert\Count(
     *      min = 1,
     *      minMessage = "You must specify at least one phone number",
     * )
     */
    private ArrayCollection $phoneNumbers;

    /**
     * @return string
     */
    public function getFirstName() : string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return CreateUser
     */
    public function setFirstName(string $firstName) : CreateUser
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName() : string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return CreateUser
     */
    public function setLastName(string $lastName) : CreateUser
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPhoneNumbers() : ArrayCollection
    {
        return $this->phoneNumbers;
    }

    /**
     * @param ArrayCollection $phoneNumbers
     *
     * @return CreateUser
     */
    public function setPhoneNumbers(ArrayCollection $phoneNumbers) : CreateUser
    {
        $this->phoneNumbers = $phoneNumbers;

        return $this;
    }
}