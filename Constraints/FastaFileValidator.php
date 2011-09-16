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

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\FileValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class FastaFileValidator extends FileValidator
{
    static protected $seqTypes = array('ADN' => SequenceUtils::CHECK_ADN, 'PROT' => SequenceUtils::CHECK_PROTEIC, 'PROT_OR_ADN' => SequenceUtils::CHECK_PROTEIC_OR_ADN);
    
    
    public function isValid($value, Constraint $constraint)
    {
        if (!parent::isValid($value, $constraint))
            return false;
        
        if (!empty($value)) {
            $qualifiedSeqType = self::$seqTypes[$constraint->seqType];

            if ($constraint->multiple)
                $qualifiedSeqType |= SequenceUtils::CHECK_MULTIPLE;

            $seqUtils = new SequenceUtils();
            $seqError = $seqUtils->checkSequenceFromFile($value->getRealPath(), SequenceUtils::CHECK_WORD | $qualifiedSeqType, true);

            if(!empty($seqError)) {
                $this->setMessage($constraint->badFastaMessage.' ('.$seqError.')');
                
                return false;
            }
        }
        
        return true;
    }
}
