<?php

namespace Pim\Component\Catalog\Updater\Setter;

use Akeneo\Component\StorageUtils\Exception\InvalidObjectException;
use Akeneo\Component\StorageUtils\Exception\InvalidPropertyException;
use Akeneo\Component\StorageUtils\Exception\InvalidPropertyTypeException;
use Akeneo\Component\StorageUtils\Repository\IdentifiableObjectRepositoryInterface;
use Pim\Component\Catalog\Builder\ProductBuilderInterface;
use Pim\Component\Catalog\Event\Product\AssociatedToAGroupEvent;
use Pim\Component\Catalog\Event\Product\AssociatedToAProductEvent;
use Pim\Component\Catalog\Event\Product\UnassociatedToAGroupEvent;
use Pim\Component\Catalog\Event\Product\UnassociatedToAProductEvent;
use Pim\Component\Catalog\Model\AssociationInterface;
use Pim\Component\Catalog\Model\EntityWithValuesInterface;
use Pim\Component\Catalog\Model\ProductInterface;

/**
 * Sets the association field
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AssociationFieldSetter extends AbstractFieldSetter
{
    /** @var IdentifiableObjectRepositoryInterface */
    protected $productRepository;

    /** @var IdentifiableObjectRepositoryInterface */
    protected $groupRepository;

    /** @var ProductBuilderInterface */
    protected $productBuilder;

    /**
     * @param IdentifiableObjectRepositoryInterface $productRepository
     * @param IdentifiableObjectRepositoryInterface $groupRepository
     * @param ProductBuilderInterface               $productBuilder
     * @param array                                 $supportedFields
     */
    public function __construct(
        IdentifiableObjectRepositoryInterface $productRepository,
        IdentifiableObjectRepositoryInterface $groupRepository,
        ProductBuilderInterface $productBuilder,
        array $supportedFields
    ) {
        $this->productRepository = $productRepository;
        $this->groupRepository = $groupRepository;
        $this->productBuilder = $productBuilder;
        $this->supportedFields = $supportedFields;
    }

    /**
     * {@inheritdoc}
     *
     * Expected data input format :
     * {
     *     "XSELL": {
     *         "groups": ["group1", "group2"],
     *         "products": ["AKN_TS1", "AKN_TSH2"]
     *     },
     *     "UPSELL": {
     *         "groups": ["group3", "group4"],
     *         "products": ["AKN_TS3", "AKN_TSH4"]
     *     },
     * }
     */
    public function setFieldData($entity, $field, $data, array $options = [])
    {
        if (!$entity instanceof EntityWithValuesInterface) {
            throw InvalidObjectException::objectExpected($entity, EntityWithValuesInterface::class);
        }

        $this->checkData($field, $data);
        $currentAssociations = $this->getCurrentAssociationsAsArray($product, $data);
        $this->clearAssociations($product, $data);
        $this->addMissingAssociations($product);
        $this->setProductsAndGroupsToAssociations($product, $data, $currentAssociations);
    }

    /**
     * Clear only concerned associations (remove groups and products from existing associations)
     *
     * @param ProductInterface $product
     * @param array            $data
     *
     * Expected data input format:
     * {
     *     "XSELL": {
     *         "groups": ["group1", "group2"],
     *         "products": ["AKN_TS1", "AKN_TSH2"]
     *     },
     *     "UPSELL": {
     *         "groups": ["group3", "group4"],
     *         "products": ["AKN_TS3", "AKN_TSH4"]
     *     },
     * }
     */
    protected function clearAssociations(ProductInterface $product, array $data = null)
    {
        if (null === $data) {
            return;
        }

        $product->getAssociations()
            ->filter(function (AssociationInterface $association) use ($data) {
                return isset($data[$association->getAssociationType()->getCode()]);
            })
            ->forAll(function ($key, AssociationInterface $association) use ($data) {
                $currentData = $data[$association->getAssociationType()->getCode()];
                if (isset($currentData['products'])) {
                    foreach ($association->getProducts() as $productToRemove) {
                        $association->removeProduct($productToRemove);
                    }
                }
                if (isset($currentData['groups'])) {
                    foreach ($association->getGroups() as $groupToRemove) {
                        $association->removeGroup($groupToRemove);
                    }
                }

                return true;
            });
    }

    /**
     * Add missing associations (if association type has been added after the last processing)
     *
     * @param ProductInterface $product
     */
    protected function addMissingAssociations(ProductInterface $product)
    {
        $this->productBuilder->addMissingAssociations($product);
    }

    /**
     * Set products and groups to associations
     *
     * @param ProductInterface $product
     * @param array            $data
     * @param array            $currentAssociations
     *
     * @throws InvalidPropertyException
     */
    protected function setProductsAndGroupsToAssociations(ProductInterface $product, array $data, array $currentAssociations)
    {
        foreach ($data as $typeCode => $items) {
            $association = $product->getAssociationForTypeCode($typeCode);
            if (null === $association) {
                throw InvalidPropertyException::validEntityCodeExpected(
                    'associations',
                    'association type code',
                    'The association type does not exist',
                    static::class,
                    $typeCode
                );
            }
            if (isset($items['products'])) {
                $currentAssociatedProducts = isset($currentAssociations[$typeCode]) ?
                    $currentAssociations[$typeCode]['products'] : [];
                $this->setAssociatedProducts($association, $items['products'], $currentAssociatedProducts);
            }
            if (isset($items['groups'])) {
                $currentAssociatedGroups = isset($currentAssociations[$typeCode]) ?
                    $currentAssociations[$typeCode]['groups'] : [];
                $this->setAssociatedGroups($association, $items['groups'], $currentAssociatedGroups);
            }
        }
    }

    /**
     * @param AssociationInterface $association
     * @param array $productsIdentifiers
     * @param array $currentAssociatedProducts
     *
     * @throws InvalidPropertyException
     */
    protected function setAssociatedProducts(
        AssociationInterface $association,
        array $productsIdentifiers,
        array $currentAssociatedProducts
    ) {
        foreach ($productsIdentifiers as $productIdentifier) {
            $associatedProduct = $this->productRepository->findOneByIdentifier($productIdentifier);
            if (null === $associatedProduct) {
                throw InvalidPropertyException::validEntityCodeExpected(
                    'associations',
                    'product identifier',
                    'The product does not exist',
                    static::class,
                    $productIdentifier
                );
            }
            $association->addProduct($associatedProduct);
            if (!in_array($productIdentifier, $currentAssociatedProducts)) {
                $association->getOwner()->registerEvent(
                    new AssociatedToAProductEvent(
                        $association->getOwner(),
                        $associatedProduct,
                        $association->getAssociationType()
                    )
                );
            }
        }

        foreach ($currentAssociatedProducts as $associatedProduct) {
            if (!in_array($associatedProduct, $productsIdentifiers)) {
                $removedProduct = $this->productRepository->findOneByIdentifier($productIdentifier);
                $association->getOwner()->registerEvent(
                    new UnassociatedToAProductEvent(
                        $association->getOwner(),
                        $removedProduct,
                        $association->getAssociationType()
                    )
                );
            }
        }
    }

    /**
     * @param AssociationInterface $association
     * @param array                $groupsCodes
     * @param array                $currentAssociatedGroups
     *
     * @throws InvalidPropertyException
     */
    protected function setAssociatedGroups(
        AssociationInterface $association,
        array $groupsCodes,
        array $currentAssociatedGroups
    ) {
        foreach ($groupsCodes as $groupCode) {
            $associatedGroup = $this->groupRepository->findOneByIdentifier($groupCode);
            if (null === $associatedGroup) {
                throw InvalidPropertyException::validEntityCodeExpected(
                    'associations',
                    'group code',
                    'The group does not exist',
                    static::class,
                    $groupCode
                );
            }
            $association->addGroup($associatedGroup);
            if (!in_array($groupCode, $currentAssociatedGroups)) {
                $association->getOwner()->registerEvent(
                    new AssociatedToAGroupEvent(
                        $association->getOwner(),
                        $associatedGroup,
                        $association->getAssociationType()
                    )
                );
            }
        }

        foreach ($currentAssociatedGroups as $associatedGroupCode) {
            if (!in_array($associatedGroupCode, $groupsCodes)) {
                $removedGroup = $this->groupRepository->findOneByIdentifier($associatedGroupCode);
                $association->getOwner()->registerEvent(
                    new UnassociatedToAGroupEvent(
                        $association->getOwner(),
                        $removedGroup,
                        $association->getAssociationType()
                    )
                );
            }
        }
    }

    /**
     * Check if data are valid
     *
     * @param string $field
     * @param mixed  $data
     *
     * @throws InvalidPropertyTypeException
     */
    protected function checkData($field, $data)
    {
        if (!is_array($data)) {
            throw InvalidPropertyTypeException::arrayExpected(
                $field,
                static::class,
                $data
            );
        }

        foreach ($data as $assocTypeCode => $items) {
            $this->checkAssociationData($field, $data, $assocTypeCode, $items);
        }
    }

    /**
     * @param string $field
     * @param array  $data
     * @param string $assocTypeCode
     * @param mixed  $items
     *
     * @throws InvalidPropertyTypeException
     */
    protected function checkAssociationData($field, array $data, $assocTypeCode, $items)
    {
        if (!is_array($items) || !is_string($assocTypeCode) ||
            (!isset($items['products']) && !isset($items['groups']))
        ) {
            throw InvalidPropertyTypeException::validArrayStructureExpected(
                $field,
                sprintf('association format is not valid for the association type "%s".', $assocTypeCode),
                static::class,
                $data
            );
        }

        foreach ($items as $type => $itemData) {
            if (!is_array($itemData)) {
                $message = sprintf(
                    'Property "%s" in association "%s" expects an array as data, "%s" given.',
                    $type,
                    $assocTypeCode,
                    gettype($itemData)
                );

                throw new InvalidPropertyTypeException(
                    $type,
                    $itemData,
                    static::class,
                    $message,
                    InvalidPropertyTypeException::ARRAY_EXPECTED_CODE
                );
            }

            $this->checkAssociationItems($field, $assocTypeCode, $data, $itemData);
        }
    }

    /**
     * @param string $field
     * @param string $assocTypeCode
     * @param array  $data
     * @param array  $items
     *
     * @throws InvalidPropertyTypeException
     */
    protected function checkAssociationItems($field, $assocTypeCode, array $data, array $items)
    {
        foreach ($items as $code) {
            if (!is_string($code)) {
                throw InvalidPropertyTypeException::validArrayStructureExpected(
                    $field,
                    sprintf('association format is not valid for the association type "%s".', $assocTypeCode),
                    static::class,
                    $data
                );
            }
        }
    }

    /**
     * @param ProductInterface $product
     * @return array
     *
     * Expected data output format :
     * {
     *     "XSELL": {
     *         "groups": ["group1", "group2"],
     *         "products": ["AKN_TS1", "AKN_TSH2"]
     *     },
     *     "UPSELL": {
     *         "groups": ["group3", "group4"],
     *         "products": ["AKN_TS3", "AKN_TSH4"]
     *     },
     * }
     */
    private function getCurrentAssociationsAsArray(ProductInterface $product)
    {
        $currentAssociations = [];
        foreach ($product->getAssociations() as $association) {
            $currentAssociations[$association->getAssociationType()->getCode()]= [
                'groups' => [],
                'products' => []
            ];
            foreach ($association->getProducts() as $product) {
                $currentAssociations[$association->getAssociationType()->getCode()]['products'][]= $product->getIdentifier();
            }
            foreach ($association->getGroups() as $group) {
                $currentAssociations[$association->getAssociationType()->getCode()]['groups'][]= $group->getCode();
            }
        }

        return $currentAssociations;
    }
}
