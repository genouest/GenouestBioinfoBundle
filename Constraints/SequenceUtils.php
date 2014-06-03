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

class SequenceUtils {

    const CHECK_WORD                = 0x01;
    const CHECK_FASTA               = 0x02;
    const CHECK_ADN                 = 0x08;
    const CHECK_PROTEIC             = 0x10;
    const CHECK_PROTEIC_OR_ADN      = 0x20;
    const CHECK_MULTIPLE            = 0x40;

    private $adnChars = 'ATGCUNRYatgcunry';
    private $proteinChars = 'ABCDEFGHIKLMNPQRSTVWYZXabcdefghiklmnpqrstvwyzx\*';
    private $wordChars = 'ÿÐÏà±þ';

    /**
     * Converts a text from dos (or mac before OSX) to unix carriage returns
     *
     * @param string The text to convert
     * @return The text converted
     **/
    public function dos2Unix($string) {
        // Processes \r\n's first so they aren't converted twice.
        return str_replace(array("\r\n", "\r"), "\n", $string);
    }

    /**
     * Ensure the text is a correct unix text with \n and a final \n
     *
     * @param string The text to convert
     * @return The text converted
     **/
    public function formatSequence($string) {
        return $this->dos2Unix($string)."\n";
    }

    /**
     * Check that the given sequence file matches a format
     *
     * @param sequence The file path containing the sequence to check
     * @param rule The formats to check (CHECK_*, see the const at the beginning of this file)
     * @param doFormat Ensure the file contains only unix line returns. This will modify the original file!
     * @return An error message if case of failure, an empty string otherwise.
     **/
    public function checkSequenceFromFile($seqPath, $rule = SequenceUtils::CHECK_WORD, $doFormat = true) {
        $seqFile = fopen( $seqPath, "r" );
        $data = "";
        if ($seqFile) {
            while (!feof($seqFile)) {
                $data .= fgets($seqFile);
            }
            fclose($seqFile);
        }
        else {
            return "Could not open the file for reading.";
        }

        $resCheck = $this->checkSequence($data, $rule);

        if ($doFormat && empty($resCheck)) {
            $data = $this->formatSequence($data);
            $seqFileOut = fopen( $seqPath, "w" );
            if ($seqFileOut) {
                $data = fwrite($seqFileOut, $data);
                fclose($seqFileOut);
            }
            else {
                return "Could not open the file for writing.";
            }
        }

        return $resCheck;
    }

    /**
     * Check that the given sequence matches a format
     *
     * @param sequence The sequence to check
     * @param rule The formats to check (CHECK_*, see the const at the beginning of this file)
     * @return An error message if case of failure, an empty string otherwise.
     **/
    public function checkSequence($sequence, $rule = SequenceUtils::CHECK_WORD) {
        
        if ($rule & SequenceUtils::CHECK_WORD) {
            if (strpos($sequence, "{\\rtf") !== false)
                return "This is not a valid text file.";

            if (preg_match('/.*['.$this->wordChars.'].*/', $sequence)) // Find a line with unauthorized char but no '>' at the start
                return "This is not a valid text file.";
        }
        
        if (($rule & SequenceUtils::CHECK_FASTA) || ($rule & SequenceUtils::CHECK_MULTIPLE)) {
            $seqNumber = substr_count($sequence, '>');
            
            if (($rule & SequenceUtils::CHECK_FASTA) && $seqNumber <= 0)
                return "Fasta definition line is missing (line begining by '>').";
            
            $lineBreakNumber = substr_count($sequence, "\n") + substr_count($sequence, "\r");
            
            if (($rule & SequenceUtils::CHECK_FASTA) && $lineBreakNumber <= 0)
                return "Wrong Fasta format.";

            if (($rule & SequenceUtils::CHECK_MULTIPLE) && $seqNumber <= 1)
                return "Multiple sequences are needed.";
        }
        
        // Ensure the file is a proper unix file
        $sequence = $this->dos2Unix($sequence);
        
        // Find a line with unauthorized char but no '>' at the start
        // 'm' modifier = search on every line
        // allow \n or \r chars as the line splitting does not remove both on dos files
        
        if (($rule & SequenceUtils::CHECK_ADN) && preg_match('/^[^>].*[^'.$this->adnChars.'\\r\\n]+.*$/m', $sequence))
            return "Not a nucleic sequence.";
        if (($rule & SequenceUtils::CHECK_PROTEIC) && preg_match('/^[^>].*[^'.$this->proteinChars.'\\r\\n]+.*$/m', $sequence))
            return "Not a proteic sequence.";
        if (($rule & SequenceUtils::CHECK_PROTEIC_OR_ADN) && preg_match('/^[^>].*[^'.$this->adnChars.$this->proteinChars.'\\r\\n]+.*$/m', $sequence))
            return "Bad sequence format";

        return ""; // No errors, the sequence match the rules
    }

}

