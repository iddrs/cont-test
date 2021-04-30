<?php

namespace IDDRS\ConTest\Tests;

use IDDRS\ContTest\Base\TestBase;
use IDDRS\ContTest\Config\BaseConfig;
use IDDRS\ContTest\Config\RPPSConfig;
use IDDRS\ContTest\Rules\EntidadesComReceitaRules;
use IDDRS\ContTest\Rules\RPPSRules;
use IDDRS\ContTest\Rules\TodasEntidadesRules;
use PHPUnit\Framework\TestCase;

/**
 * estes para a entidade do rpps (uniorcam 1201 ~ 1299)
 *
 * @author Everton
 */
class RPPSTest extends TestCase {
    use 
        BaseConfig,
        RPPSConfig,
        TestBase,
        TodasEntidadesRules,
        EntidadesComReceitaRules,
        RPPSRules
    ;
//    public function setUp(): void {
//        ini_set('memory_limit', -1);
//    }
    
    
}
