<?php

namespace Symfony\Component\Translation;

use Symfony\Component\Translation\Resource\ResourceInterface;

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * MessageCatalogueInterface.
 *
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 */
interface MessageCatalogueInterface
{
    /**
     * Gets the catalogue locale.
     *
     * @return string The locale
     */
    function getLocale();

    /**
     * Gets the domains.
     *
     * @return array An array of domains
     */
    function getDomains();

    /**
     * Gets the messages within a given domain.
     *
     * If $domain is null, it returns all messages.
     *
     * @param string $domain The domain name
     *
     * @return array An array of messages
     */
    function getMessages($domain = null);

    /**
     * Sets a message translation.
     *
     * @param string $id          The message id
     * @param string $translation The messages translation
     * @param string $domain      The domain name
     */
    function setMessage($id, $translation, $domain = 'messages');

    /**
     * Checks if a message has a translation.
     *
     * @param string $id     The message id
     * @param string $domain The domain name
     *
     * @return Boolean true if the message has a translation, false otherwise
     */
    function hasMessage($id, $domain = 'messages');

    /**
     * Gets a message translation.
     *
     * @param string $id     The message id
     * @param string $domain The domain name
     *
     * @return string The message translation
     */
    function getMessage($id, $domain = 'messages');

    /**
     * Sets translations for a given domain.
     *
     * @param string $messages An array of translations
     * @param string $domain   The domain name
     */
    function setMessages($messages, $domain = 'messages');

    /**
     * Adds translations for a given domain.
     *
     * @param string $messages An array of translations
     * @param string $domain   The domain name
     */
    function addMessages($messages, $domain = 'messages');

    /**
     * Merges translations from the given Catalogue into the current one.
     *
     * @param MessageCatalogueInterface $catalogue A MessageCatalogueInterface instance
     */
    function addCatalogue(MessageCatalogueInterface $catalogue);

    /**
     * Returns an array of resources loaded to build this collection.
     *
     * @return ResourceInterface[] An array of resources
     */
    function getResources();

    /**
     * Adds a resource for this collection.
     *
     * @param ResourceInterface $resource A resource instance
     */
    function addResource(ResourceInterface $resource);
}
