<?php

namespace Akeneo\Test\Integration\CRUD;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Product implements CRUDInterface
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $code, array $data = [])
    {
        $product = $this->container->get('pim_catalog.builder.product')->createProduct($code);
        $this->container->get('pim_catalog.updater.product')->update($product, $data);

        return $product;
    }

    /**
     * {@inheritdoc}
     */
    public function update($product, array $data)
    {
        $this->container->get('pim_catalog.updater.product')->update($product, $data);

        return $product;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $code)
    {
        return $this->container->get('pim_catalog.repository.product')->findOneByIdentifier($code);
    }

    /**
     * {@inheritdoc}
     */
    public function save($product)
    {
        $this->container->get('pim_catalog.saver.product')->save($product);

        $this->get('akeneo_elasticsearch.client')->refreshIndex();
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll(array $products)
    {
        $this->container->get('pim_catalog.saver.product')->saveAll([$products]);

        $this->get('akeneo_elasticsearch.client')->refreshIndex();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $code)
    {
        $this->container->get('pim_catalog.remover.product')->remove($code);
    }
}