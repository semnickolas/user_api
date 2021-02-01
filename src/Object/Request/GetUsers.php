<?php

namespace UserApi\Object\Request;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class GetUsers
 * @package UserApi\Object\Request
 */
class GetUsers
{
    /**
     * @var string
     *
     * @JMS\Type("string")
     * @JMS\SerializedName("filter")
     * @Assert\Type(
     *     type="string",
     *     message = "You cant create user with firstName {{ value }}. It is not a valid {{ type }}."
     * )
     */
    private string $filter = '';

    /**
     * @return string
     */
    public function getFilter() : string
    {
        return $this->filter;
    }

    /**
     * @param string $filter
     *
     * @return GetUsers
     */
    public function setFilter(string $filter) : GetUsers
    {
        $this->filter = $filter;

        return $this;
    }
}