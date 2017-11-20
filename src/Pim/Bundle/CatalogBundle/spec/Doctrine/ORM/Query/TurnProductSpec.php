<?php

namespace spec\Pim\Bundle\CatalogBundle\Doctrine\ORM\Query;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CatalogBundle\Doctrine\ORM\Query\TurnProduct;
use Pim\Component\Catalog\Model\ProductModelInterface;
use Pim\Component\Catalog\Model\ValueCollectionInterface;
use Pim\Component\Catalog\Model\VariantProductInterface;
use Prophecy\Argument;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TurnProductSpec extends ObjectBehavior
{
    function let(EntityManagerInterface $entityManager, NormalizerInterface $normalizer)
    {
        $this->beConstructedWith($entityManager, $normalizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TurnProduct::class);
    }

    function it_is_a_query()
    {
        $this->shouldImplement();
    }

    function it transform a product into variant product in database(
        $entityManager,
        $normalizer,
        VariantProductInterface $variantProduct,
        Connection $connection,
        ValueCollectionInterface $valueCollection,
        ProductModelInterface $productModel
    ) {

        $variantProduct->getId()->willReturn(64);
        $variantProduct->getValuesForVariation()->willReturn($valueCollection);
        $variantProduct->getParent()->willReturn($productModel);
        $productModel->getId()->willReturn(40);

        $normalizer->normalize($valueCollection, 'storage')->willReturn(['normalized_value']);

        $entityManager->getConnection()->willReturn($connection);
        $connection->executeQuery(Argument::type('string'), [
            'product_model_id' => 40,
            'raw_values' => json_encode(['normalized_value']),
            'product_type' => 'variant_product',
            'id' => 64
        ])->shouldBeCalled();

        $this->into($variantProduct)->shouldReturn(null);
    }
}
