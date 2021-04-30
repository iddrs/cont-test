<?php

namespace IDDRS\ConTest\Tests;

use IDDRS\ContTest\Base\TestBase;
use IDDRS\ContTest\Config\BaseConfig;
use IDDRS\ContTest\Config\CamaraConfig;
use IDDRS\ContTest\Rules\CamaraRules;
use IDDRS\ContTest\Rules\TodasEntidadesRules;
use PHPUnit\Framework\TestCase;

/**
 * estes para a entidade da camara (uniorcam 0101 ~ 0199)
 *
 * @author Everton
 */
class CamaraTest extends TestCase {
    use 
        BaseConfig,
        CamaraConfig,
        TestBase,
        TodasEntidadesRules,
        CamaraRules
    ;
    
}
