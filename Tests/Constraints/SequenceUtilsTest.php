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

namespace Genouest\Bundle\BioinfoBundle\Tests\Constraints;

use Genouest\Bundle\BioinfoBundle\Tests\TestCase;
use Genouest\Bundle\BioinfoBundle\Constraints\SequenceUtils;

class SequenceUtilsTest extends TestCase
{
    public function testPersoDb()
    {
        $util = new SequenceUtils();
        
        $this->assertEquals($util->checkSequence("", SequenceUtils::CHECK_WORD), "");
        $this->assertNotEquals($util->checkSequence("{\\rtf", SequenceUtils::CHECK_WORD), "");
        $this->assertNotEquals($util->checkSequence("EFGHIKLMNPQR\nSTVWYBZXacdefghiklmnpqr±KL\nMNPQSTVWY", SequenceUtils::CHECK_WORD), "");
        
        
        $this->assertNotEquals($util->checkSequence("", SequenceUtils::CHECK_FASTA), "");
        $this->assertNotEquals($util->checkSequence("vsd", SequenceUtils::CHECK_FASTA), "");
        $this->assertNotEquals($util->checkSequence(">vsd", SequenceUtils::CHECK_FASTA), "");
        $this->assertEquals($util->checkSequence(">vsd\naa", SequenceUtils::CHECK_FASTA), "");
        $this->assertEquals($util->checkSequence(">vsd\naa\n>test\nfoo", SequenceUtils::CHECK_FASTA), "");
        
        
        $this->assertNotEquals($util->checkSequence("", SequenceUtils::CHECK_MULTIPLE), "");
        $this->assertNotEquals($util->checkSequence("vsd", SequenceUtils::CHECK_MULTIPLE), "");
        $this->assertNotEquals($util->checkSequence(">vsd", SequenceUtils::CHECK_MULTIPLE), "");
        $this->assertNotEquals($util->checkSequence(">vsd\naa", SequenceUtils::CHECK_MULTIPLE), "");
        $this->assertEquals($util->checkSequence(">vsd\naa\n>test\nfoo", SequenceUtils::CHECK_MULTIPLE), "");
        
        
        $this->assertNotEquals($util->checkSequence("", SequenceUtils::CHECK_FASTA+SequenceUtils::CHECK_MULTIPLE), "");
        $this->assertNotEquals($util->checkSequence("vsd", SequenceUtils::CHECK_FASTA+SequenceUtils::CHECK_MULTIPLE), "");
        $this->assertNotEquals($util->checkSequence(">vsd", SequenceUtils::CHECK_FASTA+SequenceUtils::CHECK_MULTIPLE), "");
        $this->assertNotEquals($util->checkSequence(">vsd\naa", SequenceUtils::CHECK_FASTA+SequenceUtils::CHECK_MULTIPLE), "");
        $this->assertEquals($util->checkSequence(">vsd\naa\n>test\nfoo", SequenceUtils::CHECK_FASTA+SequenceUtils::CHECK_MULTIPLE), "");
        
        
        $this->assertEquals($util->checkSequence(">xxxx\natgcunATGCUNRYry", SequenceUtils::CHECK_ADN), "");
        $this->assertNotEquals($util->checkSequence(">xxxx\natcxgcuATGCUNRYnry", SequenceUtils::CHECK_ADN), "");
        $this->assertEquals($util->checkSequence(">xxxxatgcunATGCUNRYry", SequenceUtils::CHECK_ADN), "");
        $this->assertEquals($util->checkSequence(">xxxxatcxgcuATGCUNRYnry", SequenceUtils::CHECK_ADN), "");
        
        
        $this->assertEquals($util->checkSequence(">xxxx\nACDEFGHIfghiklmnKLMN*PQRSTVWYBZXacdepqrstvwybzx", SequenceUtils::CHECK_PROTEIC), "");
        $this->assertNotEquals($util->checkSequence(">xxxx\nACDJEFGHIfghiklmnKLMN*PQRSTVWYBZXacdepqrstvwybzx", SequenceUtils::CHECK_PROTEIC), "");
        $this->assertEquals($util->checkSequence(">xxxxACDEFGHIfghiklmnKLMN*PQRSTVWYBZXacdepqrstvwybzx", SequenceUtils::CHECK_PROTEIC), "");
        $this->assertEquals($util->checkSequence(">xxxxACDEFJGHIfghiklmnKLMN*PQRSTVWYBZXacdepqrstvwybzx", SequenceUtils::CHECK_PROTEIC), "");
        
        
        $this->assertEquals($util->checkSequence(">xxxx\natgcunATGCUNRYryACDEFGHIfghiklmnKLMN*PQRSTVWYBZXacdepqrstvwybzx", SequenceUtils::CHECK_PROTEIC_OR_ADN), "");
        $this->assertNotEquals($util->checkSequence(">xxxx\natcxgcuATGCUNRYnryACDJEFGHIfghiklmnKLMN*PQRSTVWYBZXacdepqrstvwybzx", SequenceUtils::CHECK_PROTEIC_OR_ADN), "");
        $this->assertEquals($util->checkSequence(">xxxxatgcunATGCUNRYryACDEFGHIfghiklmnKLMN*PQRSTVWYBZXacdepqrstvwybzx", SequenceUtils::CHECK_PROTEIC_OR_ADN), "");
        $this->assertEquals($util->checkSequence(">xxxxatcxgcuATGCUNRYnrACDJEFGHIfghiklmnKLMN*PQRSTVWYBZXacdepqrstvwybzxy", SequenceUtils::CHECK_PROTEIC_OR_ADN), "");
        
        $this->assertEquals($util->checkSequence(">gi|42592260|ref|NC_003070.5| Arabidopsis thaliana chromosome 1, complete sequence
CCCTAAACCCTAAACCCTAAACCCTAAACCTCTGAATCCTTAATCCCTAAATCCCTAAATCTTTAAATCC
TACATCCATGAATCCCTAAATACCTAATTCCCTAAACCCGAAACCGGTTTCTCTGGTTGAAAATCATTGT
GTATATAATGATAATTTTATCGTTTTTATGTAATTGCTTATTGTTGTGTGTAGATTTTTTAAAAATATCA
TTTGAGGTCAATACAAATCCTATTTCTTGTGGTTTTCTTTCCTTCACTTAGCTATGGATGGTTTATCTTC
ATTTGTTATATTGGATACAAGCTTTGCTACGATCTACATTTGGGAATGTGAGTCTCTTATTGTAACCTTA
GGGTTGGTTTATCTCAAGAATCTTATTAATTGTTTGGACTGTTTATGTTTGGACATTTATTGTCATTCTT
ACTCCTTTGTGGAAATGTTTGTTCTATCAATTTATCTTTTGTGGGAAAATTATTTAGTTGTAGGGATGAA
GTCTTTCTTCGTTGTTGTTACGCTTGTCATCTCATCTCTCAATGATATGGGATGGTCCTTTAGCATTTAT
TCTGAAGTTCTTCTGCTTGATGATTTTATCCTTAGCCAAAAGGATTGGTGGTTTGAAGACACATCATATC
AAAAAAGCTATCGCCTCGACGATGCTCTATTTCTATCCTTGTAGCACACATTTTGGCACTCAAAAAAGTA
TTTTTAGATGTTTGTTTTGCTTCTTTGAAGTAGTTTCTCTTTGCAAAATTCCTCTTTTTTTAGAGTGATT
TGGATGATTCAAGACTTCTCGGTACTGCAAAGTTCTTCCGCCTGATTAATTATCCATTTTACCTTTGTCG", SequenceUtils::CHECK_PROTEIC_OR_ADN), "");
    }
}
