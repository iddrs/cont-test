<?php

namespace IDDRS\ConTest\Tests;

use IDDRS\ContTest\Base\TestBase;
use IDDRS\ContTest\Config\BaseConfig;
use IDDRS\ContTest\Config\PrefeituraConfig;
use IDDRS\ContTest\Rules\EntidadesComReceitaRules;
use IDDRS\ContTest\Rules\PrefeituraRules;
use IDDRS\ContTest\Rules\TodasEntidadesRules;
use PHPUnit\Framework\TestCase;

/**
 * estes para a entidade da prefeitura (uniorcam 2 ~ 11)
 *
 * @author Everton
 */
class PrefeituraTest extends TestCase {
    use 
        BaseConfig,
        PrefeituraConfig,
        TestBase,
        TodasEntidadesRules,
        EntidadesComReceitaRules,
        PrefeituraRules
    ;
//    public function setUp(): void {
//        ini_set('memory_limit', -1);
//    }
    
    
}
