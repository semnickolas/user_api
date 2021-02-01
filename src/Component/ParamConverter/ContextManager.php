<?php

namespace UserApi\Component\ParamConverter;

use JMS\Serializer\DeserializationContext;

/**
 * Class ContextManager
 * @package UserApi\Component\ParamConverter
 */
class ContextManager
{
    private const CONTEXT_INDEX = 'deserializationContext';
    private const MAX_DEPT_INDEX = 'enableMaxDepth';
    private const VERSION_INDEX = 'version';
    private const GROUP_INDEX = 'group';

    /**
     * @param array $options
     *
     * @return DeserializationContext
     */
    public function configureContext(array $options): DeserializationContext
    {
        $arrayContext = [];
        $contextObject = new DeserializationContext();
        if (isset($options[self::CONTEXT_INDEX]) && is_array($options[self::CONTEXT_INDEX])) {
            $arrayContext = array_merge($arrayContext, $options[self::CONTEXT_INDEX]);
        }

        $this->fillContextObject($contextObject, $arrayContext);

        return $contextObject;
    }

    /**
     * @param DeserializationContext $context
     * @param array $options
     */
    private function fillContextObject(DeserializationContext $context, array $options) : void
    {
        foreach ($options as $key => $value) {
            if (self::GROUP_INDEX === $key) {
                $context->setGroups($options[self::GROUP_INDEX]);
            } elseif (self::VERSION_INDEX === $key) {
                $context->setVersion($options[self::VERSION_INDEX]);
            } elseif (self::MAX_DEPT_INDEX === $key) {
                $context->enableMaxDepthChecks();
            } else {
                $context->setAttribute($key, $value);
            }
        }
    }
}