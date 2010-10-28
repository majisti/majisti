<?php

namespace Symfony\Component\Form;

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * A hidden field
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
class HiddenField extends InputField
{
    /**
     * {@inheritDoc}
     */
    public function __construct($key, array $options = array())
    {
        $options['type'] = 'hidden';

        parent::__construct($key, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function isHidden()
    {
        return true;
    }
}