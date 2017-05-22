<?php

namespace Pim\Bundle\DataGridBundle\Extension\MassAction\Actions\Ajax;

use Oro\Bundle\DataGridBundle\Extension\Action\ActionConfiguration;
use Oro\Bundle\DataGridBundle\Extension\MassAction\Actions\Ajax\AjaxMassAction;

class SynchMassAction extends AjaxMassAction
{
    /**
     * @var array
     */
    protected $requiredOptions = ['handler', 'entity_name'];

    /**
     * {@inheritdoc}
     */
    public function setOptions(ActionConfiguration $options)
    {
        if (empty($options['handler'])) {
            $options['handler'] = 'synch';
        }

        if (empty($options['route'])) {
            $options['route'] = 'pim_datagrid_mass_action';
        }

        return parent::setOptions($options);
    }
}
