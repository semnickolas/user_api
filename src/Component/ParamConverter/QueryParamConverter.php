<?php

namespace UserApi\Component\ParamConverter;

use Exception;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\DeserializationContext;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\Exception\UnsupportedFormatException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use JMS\Serializer\Exception\Exception as JMSSerializerException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;

/**
 * Class QueryParamConverter
 * @package UserApi\Component\ParamConverter
 * //TODO Custom query param converter for fosrest, need refactoring to small components
 */
class QueryParamConverter implements ParamConverterInterface
{
    const QUERY_PARAM_CONVERTER = 'resource.query.param_converter';

    private PropertyAccessorInterface $propertyAccessor;

    private SerializerInterface $serializer;

    private ValidatorInterface $validator;

    private array $context = [];

    /**
     * QueryParamConverter constructor.
     *
     * @param PropertyAccessorInterface $propertyAccessor
     * @param SerializerInterface $jmsSerializer
     * @param ValidatorInterface $validator
     */
    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        SerializerInterface $jmsSerializer,
        ValidatorInterface $validator
    )
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->serializer = $jmsSerializer;
        $this->validator = $validator;
    }

    /**
     * @param ParamConverter $configuration
     *
     * @return bool
     */
    public function supports(ParamConverter $configuration): bool
    {
        return $configuration->getConverter() === self::QUERY_PARAM_CONVERTER;
    }

    /**
     * @param Request $request
     * @param ParamConverter $configuration
     * @return bool
     * @throws Exception
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $options = (array) $configuration->getOptions();

        if (isset($options['deserializationContext']) && is_array($options['deserializationContext'])) {
            $arrayContext = array_merge($this->context, $options['deserializationContext']);
        } else {
            $arrayContext = $this->context;
        }
        $this->configureContext($context = new DeserializationContext(), $arrayContext);

        $format = 'json';

        try {
            $object = $this->serializer->deserialize(
                $this->getRequestAsJson($request, $options),
                $configuration->getClass(),
                $format,
                $context
            );
        } catch (UnsupportedFormatException $e) {
            return $this->throwException(new UnsupportedMediaTypeHttpException($e->getMessage(), $e), $configuration);
        } catch (JMSSerializerException $e) {
            return $this->throwException(new BadRequestHttpException($e->getMessage(), $e), $configuration);
        }

        $this->postDeserializationProcess($request, $object, $options);

        $request->attributes->set($configuration->getName(), $object);

        if ((bool)@$options['validate']) {
            $errors = $this->validator->validate($object);

            if (count($errors) > 0) {
                throw new BadRequestHttpException($this->getErrorMessage($errors));
            }
        }

        return true;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function fetchRequestData(Request $request): array
    {
        return $request->query->all();
    }

    /**
     * @param Request $request
     * @param object $object
     * @param array $options
     */
    protected function postDeserializationProcess(Request $request, object $object, array $options): void
    {
        if (!empty($options['filePropertyName'])) {
            $this->propertyAccessor->setValue($object, $options['filePropertyName'], $request->files);
        }
    }

    /**
     * @param array $options
     * @return string
     */
    protected function getRequestAsJson(Request $request, array $options): string
    {
        $data = $this->fetchRequestData($request);

        if (!empty($options['enableRouteAttributes']) && !empty($request->attributes->get('_route_params'))) {
            $data = array_merge($data, $request->attributes->get('_route_params'));
        }

        return json_encode($data);
    }

    /**
     * @param DeserializationContext $context
     * @param array $options
     */
    protected function configureContext(DeserializationContext $context, array $options): void
    {
        foreach ($options as $key => $value) {
            if ('groups' === $key) {
                $context->setGroups($options['groups']);
            } elseif ('version' === $key) {
                $context->setVersion($options['version']);
            } elseif ('enableMaxDepth' === $key) {
                $context->enableMaxDepthChecks();
            } else {
                $context->setAttribute($key, $value);
            }
        }
    }

    /**
     * @param Exception $exception
     * @param ParamConverter $configuration
     * @return bool
     * @throws Exception
     */
    private function throwException(Exception $exception, ParamConverter $configuration) : bool
    {
        if ($configuration->isOptional()) {
            return false;
        }

        throw $exception;
    }
}