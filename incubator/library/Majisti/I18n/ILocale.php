<?php

namespace Majisti\I18n;

interface ILocale
{
    public function reset();
    public function getCurrentLocale();
    public function getDefaultLocale();
    public function getLocales();
    public function getSupportedLocales();
    public function isCurrentLocaleDefault();
    public function switchLocale($locale = null);
    public function isLocaleSupported($locale);
}