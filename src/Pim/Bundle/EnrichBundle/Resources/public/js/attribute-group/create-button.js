'use strict';

/**
 * Create button for attribute groups
 *
 * @author    Pierre Allard <pierre.allard@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define(
    [
        'jquery',
        'underscore',
        'oro/translator',
        'pim/form/common/index/create-button'
    ],
    function (
        $,
        _,
        __,
        CreateButton
    ) {
        return CreateButton.extend({
            /**
             * {@inheritdoc}
             */
            getEmptyData() {
                return {
                    code: '',
                    labels: {}
                };
            }
        });
    }
);
