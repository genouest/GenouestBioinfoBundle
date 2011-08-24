<?php

/*
 * Copyright 2011 Anthony Bretaudeau <abretaud@irisa.fr>
 *
 * Licensed under the CeCILL License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt
 *
 */

namespace Genouest\Bundle\BioinfoBundle\Extension;

class BioinfoExtension extends \Twig_Extension {

    public function getFilters() {
        return array(
            'split' => new \Twig_Filter_Method($this, 'split_func'),
            'truncate' => new \Twig_Filter_Method($this, 'truncate_func'),
            'startsWith' => new \Twig_Filter_Method($this, 'startsWith_func'),
            'endsWith' => new \Twig_Filter_Method($this, 'endsWith_func'),
        );
    }

    public function getName()
    {
        return 'bioinfo';
    }
    
    public function split_func($str, $delimiter) {
        return explode($delimiter, $str);
    }
    
    public function truncate_func($str, $len, $suffix = null) {
        if (mb_strlen($str) > $len) {
            $str = mb_substr($str, 0, $len);
            if (!is_null($suffix)) {
                $str .= $suffix;
            }
        }
        return $str;
    }

    public function startsWith_func($str, $cmp) {
        return (substr($str, 0, mb_strlen($cmp)) == $cmp);
    }

    public function endsWith_func($str, $cmp) {
        return (substr($str, -mb_strlen($cmp)) == $cmp);
    }

}
