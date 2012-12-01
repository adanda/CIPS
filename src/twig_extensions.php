<?php
/**
 * This file contains all twig extensions for the project.
 * 
 * PHP Version 5.3
 *
 * @category Application
 * @package  CIPS
 * @author   Alfred Danda <alfred.danda@gmail.com>
 * @license  MIT License
 * @link     Project 
 */

/**
 * Defines a twig-compatible version of the php str_replace function.
 * 
 * @param string $subject The string in which the replace is performed
 * @param string $search  The string that is searched and replaced
 * @param string $replace The string that replaces the searched string
 * 
 * @return string
 */
function twig_str_replace($subject, $search, $replace)
{
    return str_replace($search, $replace, $subject);
}