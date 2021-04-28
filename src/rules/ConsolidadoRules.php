<?php

namespace IDDRS\ContTest\Rules;

/**
 * Regras para consolidacao.
 * 
 * @author Everton
 */
trait ConsolidadoRules {

    /**
     * Receita/Despesa Intra
     */
    public function testReceitaIntraOrcamentariaIgualADespesaIntraOrcamentaria() {
        $filter = function (array $line): bool {
            if (
                    (
                        str_starts_with($line['codigo_receita'], '4.7')
                        || str_starts_with($line['codigo_receita'], '4.8')
                    )
                    && $line['tipo_nivel'] === 'A') {
                return true;
            }
            return false;
        };
        $receita = $this->somaColuna($this->getDataFrame('BAL_REC'), 'receita_realizada', $filter);

        $filter = function (array $line): bool {
            if (
                str_starts_with($line['elemento'], '3.1.91')
                || str_starts_with($line['elemento'], '3.2.91')
                || str_starts_with($line['elemento'], '3.3.91')
                || str_starts_with($line['elemento'], '4.4.91')
                || str_starts_with($line['elemento'], '4.5.91')
                || str_starts_with($line['elemento'], '4.6.91')
            ) {
                return true;
            }
            return false;
        };
        $despesa = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'valor_pago', $filter);

        $this->comparar($receita, $despesa);
    }
    
    /**
     * Parcelamento a receber/a pagar - curto prazo
     */
    public function testParcelamentoDaDividaPrevidenciariaNoCurtoPrazo() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '1.1.2.1.2.71.01')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $ativoDebito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $ativoCredito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '2.1.1.4.2.02.01.01')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $passivoDebito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $passivoCredito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $this->comparar(
            ($ativoDebito - $ativoCredito),
            ($passivoCredito - $passivoDebito)
        );
    }
    
    /**
     * Parcelamento a receber/a pagar - longo prazo
     */
    public function testParcelamentoDaDividaPrevidenciariaNoLongoPrazo() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '1.2.1.1.2.06.04.01')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $ativoDebito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $ativoCredito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '2.2.1.4.2.01')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $passivoDebito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $passivoCredito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $this->comparar(
            ($ativoDebito - $ativoCredito),
            ($passivoCredito - $passivoDebito)
        );
    }
    
    /**
     * Duodecimo a receber/a pagar
     */
    public function testDuodecimoAReceberEAPagar() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '1.1.3.8.2.99.01')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $ativoDebito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $ativoCredito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '2.1.8.9.2.02')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $passivoDebito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $passivoCredito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $this->comparar(
            ($ativoDebito - $ativoCredito),
            ($passivoCredito - $passivoDebito)
        );
    }
    
    /**
     * Contas de ativo/passivo intra OFSS
     */
    public function testFechamentoDasContasDeAtivoEPassivoIntraOfss() {
        $filter = function (array $line): bool {
            if (
                    preg_match('/1\..\..\..\.2\./', $line['conta_contabil'])
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $ativoDebito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $ativoCredito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    preg_match('/2\.[1,2]\..\..\.2\./', $line['conta_contabil'])
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $passivoDebito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $passivoCredito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $this->comparar(
            ($ativoDebito - $ativoCredito),
            ($passivoCredito - $passivoDebito)
        );
    }
    
    /**
     * Contas de resultado intra OFSS
     */
    public function testFechamentoDasContasDeResultadoIntraOfss() {
        $filter = function (array $line): bool {
            if (
                    preg_match('/3\..\..\..\.2\./', $line['conta_contabil'])
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $VpdDebito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $VpdCredito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    preg_match('/4\..\..\..\.2\./', $line['conta_contabil'])
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $VpaDebito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $VpaCredito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $this->comparar(
            ($VpdDebito - $VpdCredito),
            ($VpaCredito - $VpaDebito)
        );
    }

    /**
     * contribuição patronal normal a receber
     */
    public function testContribuicaoPatronalNormalAReceber() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '1.1.3.6.2.01.01.01')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $ativoDebito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $ativoCredito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (str_starts_with($line['rubrica'], '3.1.91.13.03')) {
                return true;
            }
            return false;
        };
        $liquidado = $this->somaColuna($this->getDataFrame('LIQUIDACAO'), 'valor_liquidacao', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['rubrica'], '3.1.91.13.03')) {
                return true;
            }
            return false;
        };
        $pago = $this->somaColuna($this->getDataFrame('PAGAMENTO'), 'valor_pagamento', $filter);

        $this->comparar(
            ($ativoDebito - $ativoCredito),
            ($liquidado - $pago)
        );
    }
    
    /**
     * contribuição patronal suplementar a receber
     */
    public function testContribuicaoPatronalSuplementarAReceber() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '1.1.3.6.2.01.01.02')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $ativoDebito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $ativoCredito = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (str_starts_with($line['rubrica'], '3.1.91.13.99.01')) {
                return true;
            }
            return false;
        };
        $liquidado = $this->somaColuna($this->getDataFrame('LIQUIDACAO'), 'valor_liquidacao', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['rubrica'], '3.1.91.13.99.01')) {
                return true;
            }
            return false;
        };
        $pago = $this->somaColuna($this->getDataFrame('PAGAMENTO'), 'valor_pagamento', $filter);

        $this->comparar(
            ($ativoDebito - $ativoCredito),
            ($liquidado - $pago)
        );
    }
    
    /**
     * contribuição do servidor a receber
     */
    public function testContribuicaoDoServidorAReceber() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '1.1.3.6.2.01.02')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $ativoDebito1 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $ativoCredito1 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '2.1.8.8.2.01.01.01')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $ativoDebito2 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $ativoCredito2 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $this->comparar(
            ($ativoDebito1 - $ativoCredito1),
            ($ativoCredito2 - $ativoDebito2)
        );
    }
    
    /**
     * contribuicao patronal normal 
     */
    public function testContribuicaoPatronalNormalEmContasDeResultado() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '3.1.2.1.2.01')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $ativoDebito1 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $ativoCredito1 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '4.2.1.1.2.01')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $ativoDebito2 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $ativoCredito2 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $this->comparar(
            ($ativoDebito1 - $ativoCredito1),
            ($ativoCredito2 - $ativoDebito2)
        );
    }
    
    /**
     * contribuicao patronal suplementar 
     */
    public function testContribuicaoPatronalSuplementarEmContasDeResultado() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '3.1.2.1.2.99')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $ativoDebito1 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $ativoCredito1 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '4.2.1.1.2.03')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $ativoDebito2 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $ativoCredito2 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $this->comparar(
            ($ativoDebito1 - $ativoCredito1),
            ($ativoCredito2 - $ativoDebito2)
        );
    }
    
    /**
     * atualziação da divida previdenciaria
     */
    public function testAtualizacaoDoSaldoDaDividaPrevidenciariaEmContasDeResultado() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '3.4.3.9.2.01')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $ativoDebito1 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $ativoCredito1 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '4.4.3.9.2.01')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $ativoDebito2 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $ativoCredito2 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $this->comparar(
            ($ativoDebito1 - $ativoCredito1),
            ($ativoCredito2 - $ativoDebito2)
        );
    }
    
    /**
     * repassess de duodecimo
     */
    public function testDuodecimoEmContasDeResultado() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '3.5.1.1.2.02')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $ativoDebito1 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $ativoCredito1 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '4.5.1.1.2.02')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $ativoDebito2 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $ativoCredito2 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $this->comparar(
            ($ativoDebito1 - $ativoCredito1),
            ($ativoCredito2 - $ativoDebito2)
        );
    }
    
    /**
     * contrapartida de duodecimo a receber/pagar
     */
    public function testContrapartidaDoDuodecimoAReceberATransferirEmContasDeResultado() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '3.6.5.1.2.03')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $ativoDebito1 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $ativoCredito1 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '4.6.4.1.2.03')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $ativoDebito2 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $ativoCredito2 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $this->comparar(
            ($ativoDebito1 - $ativoCredito1),
            ($ativoCredito2 - $ativoDebito2)
        );
    }
    
    /**
     * contrapartida de duodecimo a receber/pagar no inicio do ano
     */
    public function testContrapartidaDaInscricaoDoDuodecimoAReceberATransferirEmContasDeResultado() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '3.9.9.9.2.01')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $ativoDebito1 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $ativoCredito1 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '4.9.9.9.2.01')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $ativoDebito2 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $ativoCredito2 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $this->comparar(
            ($ativoDebito1 - $ativoCredito1),
            ($ativoCredito2 - $ativoDebito2)
        );
    }
}
