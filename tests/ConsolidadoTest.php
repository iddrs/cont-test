<?php

namespace IDDRS\ConTest\Tests;

use IDDRS\ContTest\Base\TestBase;
use IDDRS\ContTest\Config\BaseConfig;
use IDDRS\ContTest\Config\ConsolidadoConfig;
use IDDRS\ContTest\Rules\ConsolidadoRules;
use IDDRS\ContTest\Rules\EntidadesComReceitaRules;
use IDDRS\ContTest\Rules\TodasEntidadesRules;
use PHPUnit\Framework\TestCase;

/**
 * estes para consolidacao (uniorcam 0000 ~ 9999)
 *
 * @author Everton
 */
class ConsolidadoTest extends TestCase {
    use 
        BaseConfig,
        ConsolidadoConfig,
        TestBase,
        TodasEntidadesRules,
        EntidadesComReceitaRules,
        ConsolidadoRules
    ;
//    public function setUp(): void {
//        ini_set('memory_limit', -1);
//    }
//    
    
}
