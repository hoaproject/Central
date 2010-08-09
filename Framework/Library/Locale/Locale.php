<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 * @category    Framework
 * @package     Hoa_Locale
 *
 */

/**
 * Hoa_Core
 */
require_once 'Core.php';

/**
 * Hoa_Locale_Exception
 */
import('Locale.Exception');

/**
 * Class Hoa_Locale.
 *
 * Get/Set language, territory, continent, city etc.
 * All datas and helps files could be found here :
 *     * http://unicode.org/cldr/data/diff/supplemental/languages_and_territories.html
 *     * http://unicode.org/onlinedat/countries.html
 *     * http://unicode.org/cldr/data/common/supplemental/supplementalData.xml
 *     * http://www.iso.org/iso/en/prods-services/iso3166ma/02iso-3166-code-lists/index.html
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Locale
 */

class Hoa_Locale {

    /**
     * Locale type.
     *
     * @const int
     */
    const USERAGENT          = 0;
    const ENVIRONMENT        = 1;
    const FRAMEWORK          = 2;

    /**
     * Locale type options.
     *
     * @const int
     */
    const USERAGENT_LARGEST  = 4;
    const USERAGENT_SMALLEST = 8;


    /**
     * Language to region.
     *
     * @var Hoa_Locale array
     */
    private $languageToRegion = array();

    /**
     * iso-3166-1_alpha-2 to alpha-3 for region.
     *
     * @var Hoa_Locale array 
     */
    private $alpha2ToAlpha3   = array();

    /**
     * Region to country name.
     *
     * @var Hoa_Locale array 
     */
    private $regionToCountry  = array();

    /**
     * Continent/City to region.
     *
     * @var Hoa_Locale array 
     */
    private $contCityToRegion = array();

    /**
     * Path to ressources.
     *
     * @var Hoa_Locale string
     */
    protected $resourcePath   = '';

    /**
     * Encoding.
     *
     * @var Hoa_Locale string
     */
    protected $_encoding      = 'utf-8';

    /**
     * Locale (xx_XX).
     *
     * @var Hoa_Locale string
     */
    private $_locale          = '';

    /**
     * Language (xx)
     *
     * @var Hoa_Locale string
     */
    private $_language        = '';

    /**
     * Region (XX).
     *
     * @var Hoa_Locale string
     */
    private $_region          = '';

    /**
     * Country.
     *
     * @var Hoa_Locale string
     */
    private $_country         = '';

    /**
     * Alpha3.
     *
     * @var Hoa_Locale string
     */
    private $_alpha3          = '';

    /**
     * Timezone (Continent/City).
     *
     * @var Hoa_Locale string
     */
    private $_tz              = '';



    /**
     * Set database, resource path, encoding, and fixe Windows.
     * At present :
     *     - 298 languages ;
     *     - 244 countries/regions ;
     *     - 385 cities.
     *
     * @access  public
     * @param   string  $encoding    The encoding charset.
     * @return  void
     * @throw   Hoa_Locale_Exception
     */
    public function __construct ( $encoding = 'utf-8' ) {

        $this->resourcePath     = dirname(__FILE__) . '/Resource/';

        $this->languageToRegion = $this->load('languageToRegion');
        $this->alpha2ToAlpha3   = $this->load('alpha2ToAlpha3');
        $this->regionToCountry  = $this->load('regionToCountry');
        $this->contCityToRegion = $this->load('contCityToRegion');

        $this->_encoding        = $encoding;

        // It is supposedly not defined on Windows.
        _define('LC_MESSAGES', 5);
    }

    /**
     * Load a database from a gz.php file.
     *
     * @access  protected
     * @param   string     $file    File to load.
     * @return  array
     * @throw   Hoa_Locale_Exception
     */
    protected function load ( $file = '' ) {

        if(file_exists($this->resourcePath . $file . '.gz.php'))
            return unserialize(gzuncompress(file_get_contents(
                $this->resourcePath . $file . '.gz.php'
            )));
        else
            throw new Hoa_Locale_Exception(
                'File %s.gz.php is not found in %s directory.',
                0, array($file, $this->resourcePath));
    }

    /**
     * Set locale.
     *
     * @access  public
     * @param   string   $locale       Specify locale.
     *                                 It could be a language and a region encoded in
     *                                 iso-3166-1_alpha_2 (xx_XX) or in alpha_3 (xxx_XXX),
     *                                 or a continent and a city (Continent/City).
     * @param   mixed    $category     Specify the category of the functions affecte by
     *                                 the locale settings. If category !== null,
     *                                 then apply immediatly or not setlocale().
     * @param   bool     $throw        Throw an exception or not.
     * @return  mixed
     * @throw   Hoa_Locale_Exception
     */
    public function setLocale ( $locale = '', $category = LC_ALL, $throw = false) {

        $error = null;

        // iso-3166-1_alpha-2.
        if(preg_match('#^(?:([a-z]{2,3})(?:-|_))?([A-Z]{2})$#', $locale, $matches)) {

            $language = $matches[1];
            $region   = $matches[2];

            // Region is valid.
            if($this->validateRegion($region))
                // Language is valid.
                if($this->validateLanguage($language, $region))
                    $this->_locale = $language . '_' . $region;
                // Langue is not valid.
                else {
                    // Search an appropriated language.
                    $language = $this->findLanguageFromRegion($region);
                    // We have find one !
                    if(is_string($language))
                        $this->_locale = $language . '_' . $region;
                    // We don't have find one :( We give a list !
                    elseif(is_array($language)) {
                        $error  = 'Cannot find an appropriated language from region ' .
                                  $region . '. You must specify one in : ' .  "\n" . '    ' .
                                  implode(' ;' . "\n" . '    ', $language) . '.';
                        $return = array('errno' => 0,
                                        'list'  => $language);
                    }
                }
            // Region is not valid.
            else {
                // But language is valid.
                if($this->validateLanguage($language)) {
                    // Search an appropriated region.
                    $region = $this->findRegionFromLanguage($language);
                    // We have find one !
                    if(is_string($region))
                        $this->_locale = $language . '_' . $region;
                    // We don't have find one :( We give a list !
                    elseif(is_array($region)) {
                        $error  = 'Cannot find an appropriated region from language ' .
                                  $language . '. You must specify one in : ' . "\n" . '    ' .
                                  implode(' ;' . "\n" . '    ', $region) . '.';
                        $return = array('errno' => 1,
                                        'list'  => $region);
                    }
                }
                // Niarf, even language is not valid ...
                else {
                    $error  = 'Region ' . $region . ' and language ' .
                              $language . ' are not valid.';
                    $return = array('errno' => 2,
                                    'list'  => null);
                }
            }
        }

        // iso-3166-1_alpha-3
        elseif(preg_match('#^(?:([a-z]{2,3})(?:-|_))?([A-Z]{3})$#', $locale, $matches)) {

            $language = $this->languageToAlpha2($matches[1]);
            $region   = $this->regionToAlpha2($matches[2]);

            return $this->setLocale($language . '_' . $region,
                                    $category, $throw);
        }

        // Continent/City.
        elseif(preg_match('#^([\w]+)/([\w]+)$#', $locale, $matches)) {

            $continent = $matches[1];
            $city      = $matches[2];

            // Continent is valid.
            if($this->validateContinent($continent))
                // City is valid.
                if($this->validateCity($city, $continent)) {
                    $region   = $this->findRegionFromContinentAndCity($continent, $city);
                    $language = $this->findLanguageFromRegion($region);
                    // We have find an appropriated language.
                    if(is_string($language)) {
                        $this->_locale = $language  . '_' . $region;
                        $this->_tz     = $continent . '/' . $city;
                    }
                    // We have find a list of languages.
                    elseif(is_array($language)) {
                        $error  = 'Cannot find an appropriated language ' .
                                  'from city ' . $city . '. ' .
                                  'You must specify one in : ' . "\n" . '    ' .
                                  implode(' ;' . "\n" . '    ', $language) . '.';
                        $return = array('errno' => 3,
                                        'list'  => $language);
                    }
                }
                // City is not valid :(
                else {
                    $foo   = $this->findCityFromContinent($continent);
                    $error = 'City ' . $city . ' is not valid. Find one in this list : ' . "\n" . '    ' .
                             implode(' ;' . "\n" . '    ', $foo) . '.';
                    $return = array('errno' => 4,
                                    'list'  => $foo);
                }
            // Continent is not valid :(
            else
                // But city is valid !
                if($this->validateCity($city)) {
                    // We find the continent.
                    $continent = $this->findContinentFromCity($city);
                    return $this->setLocale($continent . '/' . $city,
                                            $category, $throw);
                }
                else {
                    $error = 'Continent/City ' . $locale . ' is not valid.';
                    $return = array('errno' => 5,
                                    'list'  => null);
                }
        }

        // Country.
        else {

            $country = strtoupper($locale);
            $region  = $this->findRegionFromCountry($country);
            return $this->setLocale($region, $category, $throw);
        }


        if($error !== null)
            if($throw)
                throw new Hoa_Locale_Exception($error, 1);
            else
                return $return;


        $this->_language = $language;
        $this->_region   = $region;
        $this->_country  = $this->regionToCountry($this->_region);
        $this->_alpha3   = $this->regionToAlpha3($this->_region);

        if($category !== null)
            $this->apply($category);

        return $this->getLocale(self::FRAMEWORK);
    }

    /**
     * Apply setlocale.
     *
     * @access  protected
     * @param   mixed      $category    Specify the category of the functions affecte by
     *                                  the locale settings.
     * @param   string     $locale      Convotional locale (xx_XX, empty, or 0).
     * @return  mixed
     * @throw   Hoa_Locale_Exception
     */
    protected function apply ( $category = LC_ALL , $locale = null) {

        if($locale !== null)
            return setlocale($category, $locale);

        if(   empty($this->_locale)
           || empty($this->_language)
           || empty($this->_region)
           || empty($this->_country)
           || empty($this->_alpha3))
            throw new Hoa_Locale_Exception(
                'Variables _locale, _language, _region, _country and _alpha3 ' .
                'could not be empty.', 2);

        if(!empty($this->_tz))
            date_default_timezone_set($this->_tz);

        return setlocale($category       , $this->_locale . '.' . $this->_encoding,
                         $this->_language, $this->_region,
                         $this->_alpha3  , $this->_country);
    }

    /**
     * Get locale.
     *
     * @access  public
     * @param   int     $source    Source of locale. Could be self::USERAGENT,
     *                             self::ENVIRONMENT, self::FRAMEWORK.
     *                             For USERAGENT, we work with RFC 2616.
     * @param   string  $option    Source option.
     * @return  string
     */
    public function getLocale ( $source = self::FRAMEWORK, $option = null ) {

        if($source == self::FRAMEWORK)
            return $this->_locale;

        elseif($source == self::ENVIRONMENT) {

            $lc_all = setlocale(LC_ALL, 0);
            $return = array();

            if(strpos($lc_all, ';')) {

                $locale = explode(';', $lc_all);
                foreach($locale as $i => $value) {

                    list($category, $specLocale) = preg_split('#=#', $value);

                    if($specLocale == 'C')
                        continue;

                    $specLocale               = substr($specLocale, 0,
                                                       strpos($specLocale, '.'));
                    list($language, $country) = preg_split('#_|-#', $specLocale);

                    $language = strtolower(substr($language, 0, 2));
                    $region   = $this->findRegionFromCountry(strtoupper($country));

                    $return[$category] = $language . '_' . $region;
                }
            }
            else {

                    $specLocale               = substr($lc_all, 0,
                                                       strpos($lc_all, '.'));
                    list($language, $country) = preg_split('#_|-#', $specLocale);

                    $language = strtolower(substr($language, 0, 2));
                    $region   = $this->findRegionFromCountry(strtoupper($country));

                    $return['LC_ALL'] = $language . '_' . $region;
            }

            if($option !== null && isset($return[$option]))
                return $return[$option];
            else
                return current($return);
        }

        /**
         * 14.4 Accept-Language
         *     Accept-Language = "Accept-Language" ":"
         *                       1#( language-range [ ";" "q" "=" qvalue ] )
         *     language-range  = ( ( 1*8ALPHA *( "-" 1*8ALPHA ) ) | "*" )
         *
         **/
        elseif($source == self::USERAGENT) {

            $http = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

            if(preg_match_all('#(?:([\w-]+),)?([\w-]+);q=([0-9\.]+)#', $http,
               $matches, PREG_SET_ORDER)) {

                $return = array();
                foreach($matches as $i => $match) {

                    //               xx-XX (5)           xx (2)
                    $handle = strlen($match[1]) > strlen($match[2])
                                  ? $match[1]
                                  : $match[2];

                    if(false === strpos($handle, '_') && false === strpos($handle, '-'))
                        continue;

                    list($language, $region) = preg_split('#_|-#', $handle);
                    $return[$match[3]] = strtolower($language) . '_' . strtoupper($region);
                }

            }
            elseif(preg_match_all('#(?:([\w]+),?)#', $http, $matches, PREG_PATTERN_ORDER)) {

                $return = array();

                // inverse keys
                krsort($matches[1]);
                foreach($matches[1] as $i => $language)
                    $return[] = strtoupper($language);
                krsort($return);

                if(   $option === null
                   || $option == self::USERAGENT_LARGEST) {

                    reset($return);
                    return current($return);
                }

                else { //self::USERAGENT_SMALLEST
                    
                    end($return);
                    return current($return);
                }
            }
            else
                return strtoupper($http);
        }

        else
            return null;
    }

    /**
     * Get language.
     *
     * @access  public
     * @return  string
     */
    public function getLanguage ( ) {

        return $this->_language;
    }

    /**
     * Get region.
     *
     * @access  public
     * @return  string
     */
    public function getRegion ( ) {

        return $this->_region;
    }

    /**
     * Get country.
     *
     * @access  public
     * @return  string
     */
    public function getCountry ( ) {

        return $this->_country;
    }

    /**
     * Get alpha3.
     *
     * @access  public
     * @return  string
     */
    public function getAlpha3 ( ) {

        return $this->_alpha3;
    }

    /**
     * Validate a language.
     *
     * @access  public
     * @param   string  $language    Language to validate.
     * @return  bool
     */
    public function validateLanguage ( $language = '', $region = '' ) {

        if(empty($language))
            return false;

        $statement = array_key_exists($language, $this->languageToRegion);

        if(empty($region))
            return $statement;
        else
            if($statement)
                return in_array($region, $this->languageToRegion[$language]);
            else
                return false;
    }

    /**
     * Validate a region.
     *
     * @access  public
     * @param   string  $region      Region.
     * @param   string  $language    Language. Give language is faster.
     * @return  bool
     */
    public function validateRegion ( $region = '', $language = '' ) {

        if(empty($region))
            return false;

        if(!empty($language))
            return in_array($region, $this->languageToRegion[$language]);
        else {

            reset($this->languageToRegion);

            do {
                $language = key($this->languageToRegion);
                $return   = in_array($region, $this->languageToRegion[$language]);
            } while(!$return && next($this->languageToRegion));

            reset($this->languageToRegion);

            return $return;
        }

        return false;
    }

    /**
     * Validate a continent.
     *
     * @access  public
     * @param   continent
     * @return  bool
     */
    public function validateContinent ( $continent ) {

        return isset($this->contCityToRegion[$continent]);
    }

    /**
     * Validate a city.
     *
     * @access  public
     * @param   string  $city         City.
     * @param   string  $continent    Continent.
     * @return  bool
     */
    public function validateCity ( $city = '', $continent = '' ) {

        if(empty($city))
            return false;

        if(!empty($continent))
            return isset($this->contCityToRegion[$continent][$city]);
        else {

            reset($this->contCityToRegion);

            do {
                $continent = key($this->contCityToRegion);
                $return    = isset($this->contCityToRegion[$continent][$city]);
            } while(!$return && next($this->contCityToRegion));

            reset($this->contCityToRegion);

            return $return;
        }

        return false;
    }

    /**
     * Find an appropriated language from a specific region.
     *
     * @access  public
     * @param   string  $region    Region.
     * @return  mixed
     */
    public function findLanguageFromRegion ( $region = '' ) {

        if(empty($region))
            return false;

        $return = array();
        foreach($this->languageToRegion as $language => $regions) {

            if(in_array($region, $regions))
                $return[] = $language;
        }

        if(in_array(strtolower($region), $return))
            return strtolower($region);

        return $return;
    }

    /**
     * Find an appropriated region from a specific language.
     *
     * @access  public
     * @param   string  $language    Language.
     * @return  mixed
     */
    public function findRegionFromLanguage ( $language = '' ) {

        if(empty($language))
            return false;

        $return = $this->languageToRegion[$language];

        if(in_array(strtoupper($language), $return))
            return strtoupper($language);

        return $return;
    }

    /**
     * Find a list of cities from a specific continent.
     *
     * @access  public
     * @param   string  $continent    Continent
     * @return  array
     */
    public function findCityFromContinent ( $continent = '' ) {

        if(empty($continent) || !isset($this->contCityToRegion[$continent]))
            return array();

       return array_keys($this->contCityToRegion[$continent]);
    }

    /**
     * Find a continent from a specific city.
     *
     * @access  public
     * @param   string  $city    City.
     * @return  string
     */
    public function findContinentFromCity ( $city = '' ) {

        if(empty($city))
            return '';

        reset($this->contCityToRegion);

        do {
            $continent = key($this->contCityToRegion);
            $return    = isset($this->contCityToRegion[$continent][$city]);
        } while(!$return && next($this->contCityToRegion));

        reset($this->contCityToRegion);

        return $continent;
    }

    /**
     * Find a region from a continent and a city of the previous continent.
     *
     * @access  public
     * @param   string  $continent    Continent.
     * @param   string  $city         City.
     * @return  string
     */
    public function findRegionFromContinentAndCity ( $continent = '', $city = '' ) {

        if(empty($continent) || empty($city))
            return '';

        return $this->contCityToRegion[$continent][$city];
    }

    /**
     * Find continents/cities from a region.
     *
     * @access  public
     * @param   string  $region    Region.
     * @return  array
     */
    public function findContinentAndCityFromRegion ( $region = '' ) {

        if(empty($region))
            return array(0 => array('continent' => null, 'city' => null));

        $region = strtolower($region);
        $out    = array();

        foreach($this->contCityToRegion as $continent => $cities)
            foreach($cities as $city => $reg)
                if(strtolower($reg) == $region)
                    $out[] = array(
                        'continent' => $continent,
                        'city'      => $city
                    );

        return $out;
    }

    /**
     * Find a region from a country.
     *
     * @access  public
     * @param   string  $country    Country.
     * @return  string
     */
    public function findRegionFromCountry ( $country = '' ) {

        if(empty($country))
            return '';

        $return = array_keys($this->regionToCountry, strtoupper($country));

        if(!empty($return))
            return $return[0];
        else
            return '';
    }

    /**
     * Transform a language in alpha 3 to alpha 2 (remove the last char).
     *
     * @access  public
     * @param   string  $language    Language in alpha-3.
     * @return  string
     */
    public function languageToAlpha2 ( $language = '' ) {

        return substr($language, 0, 2);
    }

    /**
     * Find the equivalent of a region from iso-3166-1_alpha-3 to alpha-2.
     *
     * @access  public
     * @param   string  $region    Region in alpha-3.
     * @return  string
     */
    public function regionToAlpha2 ( $language = '' ) {

        if(false !== $return = array_search($language, $this->alpha2ToAlpha3))
            return $return;
        else
            return substr($language, 0, 2);
    }

    /**
     * Find the equivalent of a region from iso-3166-1_alpha-2 to alpha-3.
     *
     * @access  public
     * @param   string  $region    Region in alpha-2.
     * @return  string
     */
    public function regionToAlpha3 ( $language = '' ) {

        if(isset($this->alpha2ToAlpha3[$language]))
            return $this->alpha2ToAlpha3[$language];
        else
            return $language;
    }

    /**
     * Region to country.
     *
     * @access  public
     * @param   string  $region    Region.
     * @return  string
     */
    public function regionToCountry ( $region = '' ) {

        if(isset($this->regionToCountry[$region]))
            return $this->regionToCountry[$region];
        else
            return '';
    }
}
