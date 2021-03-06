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

namespace Genouest\Bundle\BioinfoBundle\Constraints;

use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Constraint;

/** @Annotation */
class Fasta extends Constraint
{
    static protected $seqTypes = array('ADN', 'PROT', 'PROT_OR_ADN');
    
    public $seqType = 'ADN';
    public $multiple = false;
    public $message = 'This is not a valid Fasta format';

    /**
     * @inheritDoc
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        if (!in_array($this->seqType, self::$seqTypes)) {
            throw new ConstraintDefinitionException(sprintf('The option "seqType" must be one of "%s"', implode('", "', self::$seqTypes)));
        }
    }
}
