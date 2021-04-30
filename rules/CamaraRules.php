<?php

namespace IDDRS\ContTest\Rules;

/**
 * Regras para a camara.
 * 
 * @author Everton
 */
trait CamaraRules {

    public function testExistenciaDeReceitaOrcamentariaNaCamaraTestadaManualmente() {
        $this->assertTrue(true);
        $this->conferenciaExterna(__METHOD__, '6.2.1');
    }

}
