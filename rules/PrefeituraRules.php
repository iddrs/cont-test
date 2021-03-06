<?php

namespace IDDRS\ContTest\Rules;

/**
 * Regras para prefeitura.
 * 
 * @author Everton
 */
trait PrefeituraRules {

    /**
     * O saldo do FPM a receber deve ser igual a zero no final do mês
     */
    public function testSaldoFinalDoFpmAReceberIgualAZero() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '1.1.2.3.3.01.02') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $this->comparar(0.0, ($saldoCredor - $saldoDevedor));
        
        $this->saldoVerificado(__METHOD__, '1.1.2.3.3.01.02');
    }

    /**
     * O saldo do FUNDEB a reter deve ser igual a zero no final do mês
     */
    public function testSaldoFinalDoFundebAReterIgualAZero() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '2.1.5.0.4.01.01.01') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $this->comparar(0.0, ($saldoCredor - $saldoDevedor));
        
        $this->saldoVerificado(__METHOD__, '2.1.5.0.4.01.01.01');
    }
    
    
    /**
     * O saldo de precatórios a pagar no curto prazo deve ser igual ao saldo incluídos em LOA das contas de controle
     */
    public function testSaldoFinalDePrecatoriosAPagarNoCurtoPrazoIgualAosControlesDePrecatoriosIncluidosEmLoa() {
        $filterPassivo = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '2.1.1.1.1.05') && $line['escrituracao'] === 'S'
                    || str_starts_with($line['conta_contabil'], '2.1.3.1.1.08') && $line['escrituracao'] === 'S'
                ) {
                return true;
            }
            return false;
        };
        $saldoDevedorPassivo = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filterPassivo);
        $saldoCredorPassivo = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filterPassivo);

        $filterControle = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.1.2.9.1.01') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorControle = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filterControle);
        $saldoCredorControle = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filterControle);

        $this->comparar(($saldoCredorPassivo - $saldoDevedorPassivo), ($saldoCredorControle - $saldoDevedorControle));
        
        $this->saldoVerificado(__METHOD__, '2.1.1.1.1.05', '2.1.3.1.1.08', '8.1.2.9.1.01');
    }
    
    /**
     * O saldo de precatórios a pagar no longo prazo deve ser igual ao saldo não incluídos em LOA das contas de controle
     */
    public function testSaldoFinalDePrecatoriosAPagarNoLongoPrazoIgualAosControlesDePrecatoriosNaoIncluidosEmLoa() {
        $filterPassivo = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '2.2.1.1.1.04') && $line['escrituracao'] === 'S'
                ) {
                return true;
            }
            return false;
        };
        $saldoDevedorPassivo = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filterPassivo);
        $saldoCredorPassivo = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filterPassivo);

        $filterControle = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.1.2.9.1.02') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorControle = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filterControle);
        $saldoCredorControle = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filterControle);

        $this->comparar(($saldoCredorPassivo - $saldoDevedorPassivo), ($saldoCredorControle - $saldoDevedorControle));
        
        $this->saldoVerificado(__METHOD__, '2.2.1.1.1.04', '8.1.2.9.1.02');
    }
    
    /**
     * Dotação aberta por superávit
     */
    public function testCreditoAbertoPorSuperavitFinanceiro() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '5.2.2.1.3.01') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    $line['origem_recurso'] == 1
            ) {
                return true;
            }
            return false;
        };
        $decreto = $this->somaColuna($this->getDataFrame('DECRETO'), 'valor_credito_adicional', $filter);

        $this->comparar(($saldoDevedor - $saldoCredor), $decreto);
        
        $this->saldoVerificado(__METHOD__, '5.2.2.1.3.01');
    }
    
    public function testDividaAtivaTestadaManualmente() {
        $this->assertTrue(true);
        $this->conferenciaExterna(__METHOD__, '1.1.2.5', '1.1.2.6', '1.2.1.1.1.04', '1.2.1.1.1.05');
    }
    
    public function testTermosDeFomentoIndividualmenteTestadosManualmente() {
        $this->assertTrue(true);
        $this->conferenciaExterna(__METHOD__, '1.1.9.8.1.01', '8.1.2.2');
    }
    
    public function testRendimentosFinanceirosDoLegislativoIgualAZero() {
//        $this->markTestIncomplete('1.1.9.2.1.01');
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '1.1.9.2.1.01') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $this->comparar(0.0, ($saldoCredor - $saldoDevedor));
        
        $this->saldoVerificado(__METHOD__, '1.1.9.2.1.01');
    }
}
