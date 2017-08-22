<?php

namespace Pim\Bundle\DataGridBundle\Normalizer;

use Pim\Bundle\CatalogBundle\Filter\CollectionFilterInterface;
use Pim\Component\Catalog\Model\ProductModelInterface;
use Pim\Component\Catalog\Model\ValueCollectionInterface;
use Pim\Component\Catalog\Model\ValueInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

/**
 * Product model normalizer for datagrid
 *
 * @author    Samir Boulil <samir.boulil@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ProductModelNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    /** @var CollectionFilterInterface */
    private $filter;

    /**
     * @param CollectionFilterInterface $filter The collection filter
     */
    public function __construct(CollectionFilterInterface $filter)
    {
        $this->filter = $filter;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($productModel, $format = null, array $context = [])
    {
        if (!$this->serializer instanceof NormalizerInterface) {
            throw new \LogicException('Serializer must be a normalizer');
        }

        $context = array_merge(['filter_types' => ['pim.transform.productModel_value.structured']], $context);
        $data = [];
        $locale = current($context['locales']);

        $data['identifier'] = $productModel->getCode();
        $data['family'] = $this->getFamilyLabel($productModel, $locale);
        $data['values'] = $this->normalizeValues($productModel->getValues(), $format, $context);
        $data['created'] = $this->serializer->normalize($productModel->getCreated(), $format, $context);
        $data['updated'] = $this->serializer->normalize($productModel->getUpdated(), $format, $context);
        $data['label'] = $productModel->getLabel($locale);
        $data['image'] = $this->normalizeImage($productModel->getImage(), $format, $context);

        $data['groups'] = null;
        $data['enabled'] = null;
        $data['completeness'] = null;

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ProductModelInterface && 'datagrid' === $format;
    }

    /**
     * @param ProductModelInterface $productModel
     * @param string                $locale
     *
     * @return string
     */
    protected function getFamilyLabel(ProductModelInterface $productModel, $locale)
    {
        $family = $productModel->getFamilyVariant()->getFamily();
        if (null === $family) {
            return null;
        }

        $translation = $family->getTranslation($locale);

        return $this->getLabel($family->getCode(), $translation->getLabel());
    }

    /**
     * @param ProductModelInterface $productModel
     * @param string                $locale
     *
     * @return string
     */
    protected function getGroupsLabels(ProductModelInterface $productModel, $locale)
    {
        $groups = [];
        foreach ($productModel->getGroups() as $group) {
            $translation = $group->getTranslation($locale);
            $groups[] = $this->getLabel($group->getCode(), $translation->getLabel());
        }

        return implode(', ', $groups);
    }

    /**
     * Get the completenesses of the productModel
     *
     * @param ProductModelInterface $productModel
     * @param array                 $context
     *
     * @return int|null
     */
    protected function getCompleteness(ProductModelInterface $productModel, array $context)
    {
        $completenesses = null;
        $locale = current($context['locales']);
        $channel = current($context['channels']);

        foreach ($productModel->getCompletenesses() as $completeness) {
            if ($completeness->getChannel()->getCode() === $channel &&
                $completeness->getLocale()->getCode() === $locale) {
                $completenesses = $completeness->getRatio();
            }
        }

        return $completenesses;
    }

    /**
     * @param string      $code
     * @param string|null $value
     *
     * @return string
     */
    protected function getLabel($code, $value = null)
    {
        return '' === $value || null === $value ? sprintf('[%s]', $code) : $value;
    }

    /**
     * @param ValueInterface $data
     * @param string         $format
     * @param array          $context
     *
     * @return array|null
     */
    protected function normalizeImage(?ValueInterface $data, $format, $context = [])
    {
        return $this->serializer->normalize($data, $format, $context)['data'];
    }

    /**
     * Normalize the values of the productModel
     *
     * @param ValueCollectionInterface $values
     * @param string                   $format
     * @param array                    $context
     *
     * @return array
     */
    private function normalizeValues(ValueCollectionInterface $values, $format, array $context = [])
    {
        foreach ($context['filter_types'] as $filterType) {
            $values = $this->filter->filterCollection($values, $filterType, $context);
        }

        $data = $this->serializer->normalize($values, $format, $context);

        return $data;
    }
}
