<?php

namespace UserApi\Component\ParamConverter;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use UserApi\Validation\SimpleObjectValidator;
use JMS\Serializer\Exception\UnsupportedFormatException;
use JMS\Serializer\Exception\Exception as JMSSerializerException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;

/**
 * Class QueryParamConverter
 * @package UserApi\Component\ParamConverter
 */
class QueryParamConverter implements ParamConverterInterface
{
    const SUCCESS_RESULT = true;
    const FAILURE_RESULT = false;
    const QUERY_PARAM_CONVERTER = 'resource.query.param_converter';

    private QueryParamObjectManager $objectManager;

    private SimpleObjectValidator $validator;

    private ContextManager $contextManager;

    /**
     * QueryParamConverter constructor.
     *
     * @param QueryParamObjectManager $objectManager
     * @param ContextManager $contextManager
     * @param SimpleObjectValidator $validator
     */
    public function __construct(
        QueryParamObjectManager $objectManager,
        ContextManager $contextManager,
        SimpleObjectValidator $validator
    )
    {
        $this->objectManager = $objectManager;
        $this->contextManager = $contextManager;
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
        $result = self::SUCCESS_RESULT;
        $context = $this->contextManager->configureContext($configuration->getOptions());

        try {
            $object = $this->objectManager->deserializeObject($request, $configuration, $context);
            $this->postDeserializationProcess($request, $configuration, $object);
        } catch (UnsupportedFormatException $e) {
            $result = $this->throwException(new UnsupportedMediaTypeHttpException($e->getMessage(), $e), $configuration);
        } catch (JMSSerializerException $e) {
            $result = $this->throwException(new BadRequestHttpException($e->getMessage(), $e), $configuration);
        }

        return $result;
    }

    /**
     * @param Request $request
     * @param ParamConverter $configuration
     * @param object $object
     */
    private function postDeserializationProcess(
        Request $request,
        ParamConverter $configuration,
        object $object
    ) : void {
        $options = $configuration->getOptions();
        $this->objectManager->processFileProperty($request, $object, $options);
        $request->attributes->set($configuration->getName(), $object);

        if ((bool)@$options['validate']) {
            $this->validator->validate($object);
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
            return self::FAILURE_RESULT;
        }

        throw $exception;
    }
}