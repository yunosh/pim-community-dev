<?php

namespace Pim\Bundle\CatalogBundle\Command;

use Akeneo\Component\StorageUtils\Updater\PropertyAdderInterface;
use Akeneo\Component\StorageUtils\Updater\PropertyCopierInterface;
use Akeneo\Component\StorageUtils\Updater\PropertyRemoverInterface;
use Akeneo\Component\StorageUtils\Updater\PropertySetterInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Updater\ProductUpdater;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TestProductEventCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $updatesExample = [
            [
                'type'  => 'set_data',
                'field' => 'name',
                'data'  => 'My name',
            ],
            [
                'type'        => 'copy_data',
                'from_field'  => 'description',
                'from_scope'  => 'ecommerce',
                'from_locale' => 'en_US',
                'to_field'    => 'description',
                'to_scope'    => 'mobile',
                'to_locale'   => 'en_US',
            ],
            [
                'type'  => 'add_data',
                'field' => 'categories',
                'data'  => ['tshirt'],
            ],
        ];

        $this
            ->setName('pim:product:test-events')
            ->setDescription('Test events on a product');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $identifier = '11342012';
        $product = $this->getProduct($identifier);
        if (null === $product) {
            $output->writeln(sprintf('<error>Product with identifier "%s" not found</error>', $identifier));

            return -1;
        }

        $username = 'julia';
        if (!$this->createToken($output, $username)) {
            return -1;
        }


        // issues / questions:
        // - collect all the domain events, inside the product model is the ideal place for this
        // - however, we want to collect only the events representing the user intent, sadly we need to be closer to
        //   updater, etc internal api because of our anemic models and the use of setters, the real intent may be
        //   already lost in the model. for instance, when a product is classified in 3 categories and i unclassify it
        //   from one, our setter start to unclassify from all category before to classify in in 2 and we get 5 domain
        //   events even if the only intent of the user was to unclassify from a single category
        // - does the event should contains models like, Product, Family, etc or only codes (could be uuid later)?

        // TODO:
        // - domain event must be immutable (use id and not models)

        var_dump($product->getRawValues());
        var_dump($product->getFamily()->getCode());
        var_dump($product->getCategoryCodes());

        $updates = [
            'family' => 'pc_monitors',
            'categories' => ['pc_monitors', 'clothes'],
            'enabled' => false,
            'values' => [
                'name' => [
                    [
                        'data' => 'Samsung TC180',
                        'locale' => null,
                        'scope' => null
                    ]
                ],
                'description' => [
                    [
                        'data' => 'new desc',
                        'locale' => 'de_DE',
                        'scope' => 'ecommerce'
                    ],
                    [
                        'data' => 'updated desc',
                        'locale' => 'de_DE',
                        'scope' => 'mobile'
                    ]
                ],
            ]
        ];
        $updater = $this->getProductUpdater();
        $updater->update($product, $updates);

        $violations = $this->validate($product);
        foreach ($violations as $violation) {
            $output->writeln(sprintf("<error>%s</error>", $violation->getMessage()));
        }
        if (0 !== $violations->count()) {
            $output->writeln(sprintf('<error>Product "%s" is not valid</error>', $identifier));

            return -1;
        }


        foreach ($product->getEvents() as $event) {
            var_dump(get_class($event));
        }

        //$this->save($product);
        $output->writeln(sprintf('<info>Product "%s" has been updated</info>', $identifier));



        return 0;
    }

    /**
     * @return ProductUpdater
     */
    protected function getProductUpdater()
    {
        return $this->getContainer()->get('pim_catalog.updater.product');
    }


    /**
     * @param string $identifier
     *
     * @return ProductInterface
     */
    protected function getProduct($identifier)
    {
        $repository = $this->getContainer()->get('pim_catalog.repository.product');
        $product = $repository->findOneByIdentifier($identifier);

        return $product;
    }

    /**
     * @return TokenStorageInterface
     */
    protected function getTokenStorage()
    {
        return $this->getContainer()->get('security.token_storage');
    }

    /**
     * @param ProductInterface $product
     *
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    protected function validate(ProductInterface $product)
    {
        $validator = $this->getContainer()->get('pim_catalog.validator.product');
        $errors = $validator->validate($product);

        return $errors;
    }

    /**
     * @param ProductInterface $product
     */
    protected function save(ProductInterface $product)
    {
        $saver = $this->getContainer()->get('pim_catalog.saver.product');
        $saver->save($product);
    }

    /**
     * Create a security token from the given username
     *
     * @param OutputInterface $output
     * @param string          $username
     *
     * @return bool
     */
    protected function createToken(OutputInterface $output, $username)
    {
        $userRepository = $this->getContainer()->get('pim_user.repository.user');
        $user = $userRepository->findOneByIdentifier($username);

        if (null === $user) {
            $output->writeln(sprintf('<error>Username "%s" is unknown<error>', $username));

            return false;
        }

        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->getTokenStorage()->setToken($token);

        return true;
    }
}
