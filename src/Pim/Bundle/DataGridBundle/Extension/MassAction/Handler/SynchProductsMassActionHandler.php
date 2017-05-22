<?php

namespace Pim\Bundle\DataGridBundle\Extension\MassAction\Handler;

use Oro\Bundle\DataGridBundle\Datagrid\DatagridInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\Actions\MassActionInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionResponse;
use Pim\Bundle\DataGridBundle\Datasource\ResultRecord\HydratorInterface;
use Pim\Component\Catalog\Repository\ProductRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Process\PhpExecutableFinder;

class SynchProductsMassActionHandler implements MassActionHandlerInterface
{
    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;
    /**
     * @var Kernel
     */
    private $kernel;
    /**
     * @var
     */
    private $rootDir;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * Constructor
     *
     * @param HydratorInterface        $hydrator
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        HydratorInterface $hydrator,
        EventDispatcherInterface $eventDispatcher,
        Kernel $kernel,
        $rootDir,
        ProductRepositoryInterface $productRepository
    ) {
        $this->hydrator = $hydrator;
        $this->eventDispatcher = $eventDispatcher;
        $this->kernel = $kernel;
        $this->rootDir = $rootDir;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DatagridInterface $datagrid, MassActionInterface $massAction)
    {
        $datasource = $datagrid->getDatasource();
        $datasource->setHydrator($this->hydrator);

        // hydrator uses index by id
        $objectIds = $datasource->getResults();

        $pathFinder = new PhpExecutableFinder();
        foreach ($objectIds as $id) {
            $cmd =
                sprintf(
                    '%s %s/console pim:synchro push %s admin %s',
                    $pathFinder->find(),
                    $this->rootDir,
                    'admin',
                    $this->productRepository->findOneBy(['id' => $id])->getIdentifier()->getData()
                );
            exec($cmd);
        }

        return $this->getResponse($massAction);
    }

    /**
     * Prepare mass action response
     *
     * @param MassActionInterface $massAction
     * @param int                 $countRemoved
     *
     * @return MassActionResponse
     */
    protected function getResponse(MassActionInterface $massAction)
    {
        $responseMessage = $massAction->getOptions()->offsetGetByPath(
            '[messages][success]',
            'test'
        );

        return new MassActionResponse(
            true,
            $responseMessage
        );
    }
}
