<?php

namespace IDDRS\ContTest\Rules;

/**
 * Regras para a camara.
 * 
 * @author Everton
 */
trait CamaraRules {

    /*public function testExistenciaDeReceitaOrcamentariaNaCamaraTestadaManualmente() {
        $this->assertTrue(true);
        $this->conferenciaExterna(__METHOD__, '6.2.1');
    }*/
    
    public function testReceitaArrecadadaIgualAZero() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '6.2.1.2') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $this->comparar(0.0, ($saldoCredor - $saldoDevedor));
    }

}
