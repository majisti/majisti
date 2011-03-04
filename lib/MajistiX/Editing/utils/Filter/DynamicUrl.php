<?php

namespace MajistiX\Editing\Util\Filter;

/**
 * @desc Filters a value.
 * 
 * @author Steven Rosato
 */
class DynamicUrl extends AbstractUrl
{
    /*
     * (non-phpDoc) 
     * @see Inherited documentation.
     */
    public function filter($value)
    {
        $conf = $this->getConfiguration();

        return str_replace(
            array(
                $conf->find('majisti.app.url'),
                $conf->find('majisti.url')
            ),
            array(
                '##{majisti.app.url}##',
                '##{majisti.url}##',
            ), 
            $value
        );
    }
}
