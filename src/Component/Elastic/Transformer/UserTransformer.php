<?php

namespace UserApi\Component\Elastic\Transformer;

use Doctrine\ORM\Query;
use Symfony\Component\Uid\UuidV4;
use Doctrine\Persistence\ManagerRegistry;
use FOS\ElasticaBundle\Doctrine\ORM\ElasticaToModelTransformer;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Class UserTransformer
 * @package UserApi\Component\Elastic\Transformer
 */
class UserTransformer extends ElasticaToModelTransformer
{
    /**
     * UserTransformer constructor.
     *
     * @param ManagerRegistry $registry
     * @param string $objectClass
     * @param array $options
     * @param PropertyAccessorInterface|null $propertyAccessor
     */
    public function __construct(
        ManagerRegistry $registry,
        string $objectClass,
        array $options = [],
        ?PropertyAccessorInterface $propertyAccessor = null
    ) {
        parent::__construct($registry, $objectClass, $options);

        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * Fetch objects for theses identifier values.
     *
     * @param array $identifierValues ids values
     * @param bool  $hydrate          whether or not to hydrate the objects, false returns arrays
     *
     * @return array of objects or arrays
     */
    protected function findByIdentifiers(array $identifierValues, $hydrate)
    {
        if (empty($identifierValues)) {
            return [];
        }
        $hydrationMode = $hydrate ? Query::HYDRATE_OBJECT : Query::HYDRATE_ARRAY;

        $qb = $this->getEntityQueryBuilder();
        $qb->andWhere($qb->expr()->in(static::ENTITY_ALIAS.'.'.$this->options['identifier'], ':values'))
            ->setParameter('values', $this->convertIdsToBinary($identifierValues));
        $query = $qb->getQuery();

        foreach ($this->options['hints'] as $hint) {
            $query->setHint($hint['name'], $hint['value']);
        }

        return $query->setHydrationMode($hydrationMode)->execute();
    }

    /**
     * @param array $ids
     *
     * @return array
     */
    private function convertIdsToBinary(array $ids) : array
    {
        $result = [];
        foreach ($ids as $id) {
            $result[] = UuidV4::fromString($id)->toBinary();
        }

        return $result;
    }
}