<?php

class My_Config_Handler_Custom implements \Majisti\Config\Handler\IHandler
{
    public function handle(\Zend_Config $config)
    {
        return $config;
    }
}

