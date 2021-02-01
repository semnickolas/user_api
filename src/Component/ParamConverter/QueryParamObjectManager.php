<?php

namespace UserApi\Component\ParamConverter;

use UserApi\Enum\SerializerEnum;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\DeserializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class QueryParamObjectManager
 * @package UserApi\Component\ParamConverter
 */
class QueryParamObjectManager
{
    private const ATTRIBUTES_INDEX = 'enableRouteAttributes';
    private const PARAMS_INDEX = '_route_params';
    private const FILE_PROPERTY_INDEX = 'filePropertyName';

    private SerializerInterface $serializer;

    private PropertyAccessorInterface $propertyAccessor;

    /**
     * QueryParamObjectManager constructor.
     *
     * @param SerializerInterface $serializer
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(SerializerInterface $serializer, PropertyAccessorInterface $propertyAccessor)
    {
        $this->serializer = $serializer;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @param Request $request
     * @param ParamConverter $configuration
     * @param DeserializationContext $context
     *
     * @return object
     */
    public function deserializeObject(
        Request $request,
        ParamConverter $configuration,
        DeserializationContext $context
    ) : object {
        return $this->serializer->deserialize(
            $this->getRequestAsJson($request, $configuration->getOptions()),
            $configuration->getClass(),
            SerializerEnum::FORMAT_JSON,
            $context
        );
    }


    /**
     * @param Request $request
     * @param object $object
     * @param array $options
     */
    public function processFileProperty(Request $request, object $object, array $options): void
    {
        if (!empty($options[self::FILE_PROPERTY_INDEX])) {
            $this->propertyAccessor->setValue(
                $object,
                $options[self::FILE_PROPERTY_INDEX],
                $request->files
            );
        }
    }

    /**
     * @param Request $request
     * @param array $options
     *
     * @return string
     */
    private function getRequestAsJson(Request $request, array $options): string
    {
        $data = $request->query->all();
        if (!empty($options[self::ATTRIBUTES_INDEX]) && !empty($request->attributes->get(self::PARAMS_INDEX))) {
            $data = array_merge($data, $request->attributes->get(self::PARAMS_INDEX));
        }

        return json_encode($data);
    }
}