<?php

namespace Majisti\Model\Storage;

/**
 * Based on CRUD pattern.
 * 
 * @author Steven Rosato
 */
interface IStorage
{
    public function create(array $args);
    public function read(array $args);
    public function has(array $args);
    public function update(array $args);
    public function upcreate(array $args);
    public function delete(array $args);
}