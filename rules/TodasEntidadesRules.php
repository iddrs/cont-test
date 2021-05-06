<?php

namespace IDDRS\ContTest\Rules;

/**
 * Regras para todas as entidades.
 * 
 * @author Everton
 */
trait TodasEntidadesRules {

    /**
     * O saldo dos salários e ordenados - adiantamentos deve ser igual a zero no final do mês
     */
    public function testSaldoFinalDeAdiantamentosDeSalarioIgualAZero() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '1.1.3.1.1.01.01') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $this->comparar(0.0, ($saldoDevedor - $saldoCredor));
        
        $this->saldoVerificado(__METHOD__, '1.1.3.1.1.01.01');
    }

    /**
     * O saldo das férias - adiantamentos deve ser igual a zero no final do mês
     */
    public function testSaldoFinalDeAdiantamentosDeFeriasIgualAZero() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '1.1.3.1.1.01.04') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $this->comparar(0.0, ($saldoDevedor - $saldoCredor));
        
        $this->saldoVerificado(__METHOD__, '1.1.3.1.1.01.04');
    }

    /**
     * O saldo de outros adiantamentos deve ser igual a zero no final do mês
     */
    public function testSaldoFinalDeOutrosAdiantamentosIgualAZero() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '1.1.3.1.1.01.99') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $this->comparar(0.0, ($saldoDevedor - $saldoCredor));
        
        $this->saldoVerificado(__METHOD__, '1.1.3.1.1.01.99');
    }

    /**
     * O saldo de suprimento de fundos do ativo deve ser igual ao saldo a comprovar + a aprovar das contas de controle
     */
    public function testSaldoFinalDeSuprimentosDeFundosDoAtivoIgualAosControlesAComprovarEAAprovar() {
        $filterAtivo = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '1.1.3.1.1.02') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorAtivo = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filterAtivo);
        $saldoCredorAtivo = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filterAtivo);

        $filterAComprovar = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.9.1.2.1.01') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorAComprovar = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filterAComprovar);
        $saldoCredorAComprovar = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filterAComprovar);

        $filterAAprovar = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.9.1.2.1.02') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorAAprovar = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filterAAprovar);
        $saldoCredorAAprovar = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filterAAprovar);

        $this->comparar(($saldoDevedorAtivo - $saldoCredorAtivo), (($saldoCredorAComprovar - $saldoDevedorAComprovar) + ($saldoCredorAAprovar - $saldoDevedorAAprovar)));
        
        $this->saldoVerificado(__METHOD__, '1.1.3.1.1.02', '8.9.1.2.1.01', '8.9.1.2.1.02');
    }

    /**
     * O saldo de parcerias a comprovar do ativo deve ser igual ao saldo a comprovar + a aprovar das contas de controle
     */
    public function testSaldoFinalDeParceriasAApropriarDoAtivoIgualAosControlesAComprovarEAAprovar() {
        $filterAtivo = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '1.1.9.8.1.01') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorAtivo = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filterAtivo);
        $saldoCredorAtivo = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filterAtivo);

        $filterAComprovar = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.1.2.2.1.01.02') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorAComprovar = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filterAComprovar);
        $saldoCredorAComprovar = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filterAComprovar);

        $filterAAprovar = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.1.2.2.1.01.03') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorAAprovar = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filterAAprovar);
        $saldoCredorAAprovar = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filterAAprovar);

        $this->comparar(($saldoDevedorAtivo - $saldoCredorAtivo), (($saldoCredorAComprovar - $saldoDevedorAComprovar) + ($saldoCredorAAprovar - $saldoDevedorAAprovar)));
        
        $this->saldoVerificado(__METHOD__, '1.1.9.8.1.01', '8.1.22.1.01.02', '8.1.2.2.1.01.03');
    }

    /**
     * O saldo dos salários e ordenados a pagar deve ser igual ao valor liquidado a pagar
     */
    public function testSaldoFinalDeSalariosAPagarIgualAoSaldoLiquidadoAPagar() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '2.1.1.1.1.01.01.02') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    (str_starts_with($line['rubrica'], '3.1.90.04') && !str_starts_with($line['rubrica'], '3.1.90.04.15')) || str_starts_with($line['rubrica'], '3.1.90.11') || str_starts_with($line['rubrica'], '3.1.90.16') || str_starts_with($line['rubrica'], '3.1.90.94')
            ) {
                return true;
            }
            return false;
        };
        $liquidado = $this->somaColuna($this->getDataFrame('LIQUIDACAO'), 'valor_liquidacao', $filter);
        $pago = $this->somaColuna($this->getDataFrame('PAGAMENTO'), 'valor_pagamento', $filter);

        $this->comparar(($liquidado - $pago), ($saldoCredor - $saldoDevedor));
        
        $this->saldoVerificado(__METHOD__, '2.1.1.1.1.01.01.02');
    }

    /**
     * O saldo das férias a pagar deve ser igual ao valor liquidado a pagar
     */
    public function testSaldoFinalDeFeriasAPagarIgualAoSaldoLiquidadoAPagar() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '2.1.1.1.1.01.03.02') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['rubrica'], '3.1.90.11.42') || str_starts_with($line['rubrica'], '3.1.90.11.44') || str_starts_with($line['rubrica'], '3.1.90.11.45') || str_starts_with($line['rubrica'], '3.1.90.11.46') || str_starts_with($line['rubrica'], '3.1.90.11.94.01.03')
            ) {
                return true;
            }
            return false;
        };
        $liquidado = $this->somaColuna($this->getDataFrame('LIQUIDACAO'), 'valor_liquidacao', $filter);
        $pago = $this->somaColuna($this->getDataFrame('PAGAMENTO'), 'valor_pagamento', $filter);

        $this->comparar(($liquidado - $pago), ($saldoCredor - $saldoDevedor));
        
        $this->saldoVerificado(__METHOD__, '2.1.1.1.1.01.03.02');
    }

    /**
     * O saldo do PASEP a pagar deve ser igual ao valor liquidado a pagar
     */
    public function testSaldoFinalDePasepAPagarIgualAoSaldoLiquidadoAPagar() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '2.1.4.1.3.11.02') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['rubrica'], '3.3.90.47.12')
            ) {
                return true;
            }
            return false;
        };
        $liquidado = $this->somaColuna($this->getDataFrame('LIQUIDACAO'), 'valor_liquidacao', $filter);
        $pago = $this->somaColuna($this->getDataFrame('PAGAMENTO'), 'valor_pagamento', $filter);

        $this->comparar(($liquidado - $pago), ($saldoCredor - $saldoDevedor));
        
        $this->saldoVerificado(__METHOD__, '2.1.4.1.3.11.02');
    }

    /**
     * Testa os saldos de ativo/passivo extra com o saldo de recursos extra
     */
    public function testSaldoDeRecursosExtraOrcamentariosIgualAoSaldoDoPassivoARecolherMenosOAtivoACompensar() {
//        $this->markTestSkipped("Pulado porque a diferença é referente à conta 1.1.3.5.1.05.01");
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '1.1.1.1.1.') && $line['escrituracao'] === 'S' && $line['recurso_vinculado'] >= 8000 && $line['recurso_vinculado'] <= 8999
            ) {
                return true;
            }
            return false;
        };
        $saldoDevedorDisp = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorDisp = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    (
                    str_starts_with($line['conta_contabil'], '1.') && !str_starts_with($line['conta_contabil'], '1.1.1.1.1.')
                    ) && $line['escrituracao'] === 'S' && $line['recurso_vinculado'] >= 8000 && $line['recurso_vinculado'] <= 8999
            ) {
                return true;
            }
            return false;
        };
        $saldoDevedorAtivo = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorAtivo = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '2.') && $line['escrituracao'] === 'S' && $line['recurso_vinculado'] >= 8000 && $line['recurso_vinculado'] <= 8999
            ) {
                return true;
            }
            return false;
        };
        $saldoDevedorPassivo = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorPassivo = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $this->comparar(($saldoDevedorDisp - $saldoCredorDisp), ($saldoCredorPassivo - $saldoDevedorPassivo) - ($saldoDevedorAtivo - $saldoCredorAtivo));
        
    }

    /**
     * Testa o fechamento dos lançamentos em contas patrimoniais
     */
    public function testFechamentoDosLancamentosNasContasPatrimoniais() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '1.') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $debitosAtivo = $this->somaColuna($this->getDataFrame('BAL_VER'), 'movimentacao_debito', $filter);
        $creditosAtivo = $this->somaColuna($this->getDataFrame('BAL_VER'), 'movimentacao_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '2.') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $debitosPassivo = $this->somaColuna($this->getDataFrame('BAL_VER'), 'movimentacao_debito', $filter);
        $creditosPassivo = $this->somaColuna($this->getDataFrame('BAL_VER'), 'movimentacao_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '3.') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $debitosVPD = $this->somaColuna($this->getDataFrame('BAL_VER'), 'movimentacao_debito', $filter);
        $creditosVPD = $this->somaColuna($this->getDataFrame('BAL_VER'), 'movimentacao_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '4.') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $debitosVPA = $this->somaColuna($this->getDataFrame('BAL_VER'), 'movimentacao_debito', $filter);
        $creditosVPA = $this->somaColuna($this->getDataFrame('BAL_VER'), 'movimentacao_credito', $filter);

        $this->comparar(($debitosAtivo + $debitosPassivo + $debitosVPA + $debitosVPD), ($creditosAtivo + $creditosPassivo + $creditosVPA + $creditosVPD));
        
        $this->naturezaVerificada(__METHOD__, '1.', '2.', '3.', '4.');
    }

    /**
     * Crédito inicial
     */
    public function testCreditoInicialContabilizado() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '5.2.2.1.1.01') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $balDesp = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'dotacao_inicial', $filter);

        
        $this->comparar(($saldoDevedor - $saldoCredor), $balDesp);
        
        $this->saldoVerificado(__METHOD__, '5.2.2.1.1.01');
    }

    /**
     * Crédito suplementar aberto
     */
    public function testCreditoSuplementarContabilizado() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '5.2.2.1.2.01') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $balDesp = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'creditos_suplementares', $filter);

        
        $this->comparar(($saldoDevedor - $saldoCredor), $balDesp);
        
        $this->saldoVerificado(__METHOD__, '5.2.2.1.2.01');
    }

    /**
     * Crédito especial aberto/reaberto
     */
    public function testCreditoEspecialContabilizado() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '5.2.2.1.2.02') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $balDesp = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'creditos_especiais', $filter);

        
        $this->comparar(($saldoDevedor - $saldoCredor), $balDesp);
        
        $this->saldoVerificado(__METHOD__, '5.2.2.1.2.02');
    }

    /**
     * Crédito extraordinário aberto/reaberto
     */
    public function testCreditoExtraordinarioContabilizado() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '5.2.2.1.2.03') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $balDesp = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'creditos_extraordinarios', $filter);

        
        $this->comparar(($saldoDevedor - $saldoCredor), $balDesp);
        
        $this->saldoVerificado(__METHOD__, '5.2.2.1.2.03');
    }

    /**
     * Crédito especial/extraordinário reaberto
     */
    public function testCreditoReabertoContabilizado() {
        $filter = function (array $line): bool {
            if (
                    (
                        str_starts_with($line['conta_contabil'], '5.2.2.1.2.02.02')
                        || str_starts_with($line['conta_contabil'], '5.2.2.1.2.03.02')
                    ) && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $saldoDevedor1 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor1 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '5.2.2.1.3.06') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $saldoDevedor2 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor2 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };

        
        $this->comparar(($saldoDevedor1 - $saldoCredor1), ($saldoDevedor2 - $saldoCredor2));
        
        $this->saldoVerificado(__METHOD__, '5.2.2.1.2.02.02', '5.2.2.1.2.03.02', '5.2.2.1.3.06');
    }
    
    /**
     * Anulação de dotação
     */
    public function testAnulacaoDeDotacaoContabilizada() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '5.2.2.1.9.04') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $balDesp = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'reducao_dotacao', $filter);

        $this->comparar(($saldoCredor - $saldoDevedor), $balDesp);
        
        $this->saldoVerificado(__METHOD__, '5.2.2.1.9.04');
    }
    
    
    /**
     * Anulação de dotação
     */
    public function testCancelamentoDeDotacaoContabilizada() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '5.2.2.1.9.04') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $balDesp = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'reducao_dotacao', $filter);

        
        $this->comparar(($saldoCredor - $saldoDevedor), $balDesp);
        
        $this->saldoVerificado(__METHOD__, '5.2.2.1.9.04');
    }
    
    /**
     * Emissão de notas de empenho
     */
    public function testEmissaoDeNotasDeEmpenhosContabilizada() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '5.2.2.9.2.01') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $balDesp = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'valor_empenhado', $filter);

        
        $this->comparar(($saldoDevedor - $saldoCredor), $balDesp);
        
        $this->saldoVerificado(__METHOD__, '5.2.2.9.2.01');
    }
    
    /**
     * Crédito disponível
     */
    public function testDotacaoDisponivelContabilizada() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.2.2.1.1') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $dotacaoInicial = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'dotacao_inicial', $filter);
        $atualizacao = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'atualizacao_monetaria', $filter);
        $suplementar = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'creditos_suplementares', $filter);
        $especial = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'creditos_especiais', $filter);
        $extraordinario = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'creditos_extraordinarios', $filter);
        $reducao = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'reducao_dotacao', $filter);
        $suplementacaoRV = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'suplementacao_recurso_vinculado', $filter);
        $reducaoRV = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'reducao_recurso_vinculado', $filter);
        $empenhado = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'valor_empenhado', $filter);

        
        $this->comparar(($saldoCredor - $saldoDevedor), (($dotacaoInicial + $suplementar + $especial + $extraordinario - $reducao + $suplementacaoRV - $reducaoRV) - $empenhado));
        
        $this->saldoVerificado(__METHOD__, '6.2.2.1.1');
    }
    
    /**
     * Empenhado a liquidar
     */
    public function testCreditoEmpenhadoALiquidarContabilizado() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.2.2.1.3.01') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $empenhado = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'valor_empenhado', $filter);
        $liquidado = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'valor_liquidado', $filter);

        
        $this->comparar(($saldoCredor - $saldoDevedor), ($empenhado - $liquidado));
        $this->saldoVerificado(__METHOD__, '6.2.2.1.3.01');
        
    }
    
    /**
     * Liquidado a pagar
     */
    public function testCreditoLiquidadoAPagarContabilizado() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.2.2.1.3.03') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $liquidado = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'valor_liquidado', $filter);
        $pago = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'valor_pago', $filter);

        
        $this->comparar(($saldoCredor - $saldoDevedor), ($liquidado - $pago));
        
        $this->saldoVerificado(__METHOD__, '6.2.2.1.3.03');
    }
    
    /**
     * Crédito pago
     */
    public function testCreditoLiquidadoPagoContabilizado() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.2.2.1.3.04') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $pago = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'valor_pago', $filter);

        $this->comparar(($saldoCredor - $saldoDevedor), $pago);
        
        $this->saldoVerificado(__METHOD__, '6.2.2.1.3.04');
    }
    
    /**
     * Empenhos a liquidar
     */
    public function testEmpenhosALiquidarContabilizado() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.2.2.9.2.01.01') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $empenhado = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'valor_empenhado', $filter);
        $liquidado = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'valor_liquidado', $filter);
        
        $this->comparar(($saldoCredor - $saldoDevedor), ($empenhado - $liquidado));
        
        $this->saldoVerificado(__METHOD__, '6.2.2.9.2.01.01');
    }
    
    /**
     * Empenho Liquidado a pagar
     */
    public function testEmpenhoLiquidadoAPagarContabilizado() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.2.2.9.2.01.03') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $liquidado = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'valor_liquidado', $filter);
        $pago = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'valor_pago', $filter);

        $this->comparar(($saldoCredor - $saldoDevedor), ($liquidado - $pago));
        
        $this->saldoVerificado(__METHOD__, '6.2.2.9.2.01.03');
    }
    
    /**
     * Empenho pago
     */
    public function testEmpenhosPagosContabilizado() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.2.2.9.2.01.04') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $pago = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'valor_pago', $filter);
        
        $this->comparar(($saldoCredor - $saldoDevedor), $pago);
        
        $this->saldoVerificado(__METHOD__, '6.2.2.9.2.01.04');
    }
    
    /**
     * Testa o fechamento dos lançamentos em contas orcamentarias
     */
    public function testFechamentoDosLancamentosNasContasOrcamentarias() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '5.') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $debitosCAPO = $this->somaColuna($this->getDataFrame('BAL_VER'), 'movimentacao_debito', $filter);
        $creditosCAPO = $this->somaColuna($this->getDataFrame('BAL_VER'), 'movimentacao_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $debitosCEPO = $this->somaColuna($this->getDataFrame('BAL_VER'), 'movimentacao_debito', $filter);
        $creditosCEPO = $this->somaColuna($this->getDataFrame('BAL_VER'), 'movimentacao_credito', $filter);

        $this->comparar(($debitosCAPO + $debitosCEPO), ($creditosCAPO + $creditosCEPO));
        
        $this->naturezaVerificada(__METHOD__, '5.', '6.');
    }
    
    /**
     * Fechamento 5.2.2.1/6.2.2.1
     */
    public function testFechamentoDosNiveisDasContasOrcamentariasDeDotacaoOrcamentaria() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '5.2.2.1') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor5 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor5 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '6.2.2.1') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor6 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor6 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor5 - $saldoCredor5), ($saldoCredor6 - $saldoDevedor6));
        
        $this->nivelVerificado(__METHOD__, '5.2.2.1', '6.2.2.1');
    }
    
    /**
     * Fechamento 5.2.2.9/6.2.2.9
     */
    public function testFechamentoDosNiveisDasContasOrcamentariasDeOutrosControlesDaDespesa() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '5.2.2.9') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor5 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor5 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '6.2.2.9') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor6 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor6 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor5 - $saldoCredor5), ($saldoCredor6 - $saldoDevedor6));
        
        $this->nivelVerificado(__METHOD__, '5.2.2.9', '6.2.2.9');
    }
    
    /**
     * Fechamento 5.3.1/6.3.1
     */
    public function testFechamentoDosNiveisDasContasOrcamentariasDeRestosAPagarNaoProcessados() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '5.3.1') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor5 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor5 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '6.3.1') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor6 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor6 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor5 - $saldoCredor5), ($saldoCredor6 - $saldoDevedor6));
        
        $this->nivelVerificado(__METHOD__, '5.3.1', '6.3.1');
    }
    
    /**
     * Fechamento 5.3.2/6.3.2
     */
    public function testFechamentoDosNiveisDasContasOrcamentariasDeRestosAPagarProcessados() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '5.3.2') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor5 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor5 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '6.3.2') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor6 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor6 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor5 - $saldoCredor5), ($saldoCredor6 - $saldoDevedor6));
        
        $this->nivelVerificado(__METHOD__, '5.3.2', '6.3.2');
    }
    
    /**
     * Recursos disponiveis
     */
    public function testRecursosDisponiveis() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.2.1.1.1') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorControle = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorControle = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '1.')
                    && $line['escrituracao'] === 'S'
                    && $line['indicador_superavit_financeiro'] === 'F'
                ) {
                return true;
            }
            return false;
        };
        $saldoDevedor1 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor1 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '2.1.')
                    && $line['escrituracao'] === 'S'
                    && $line['indicador_superavit_financeiro'] === 'F'
                ) {
                return true;
            }
            return false;
        };
        $saldoDevedor21 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor21 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '2.2.')
                    && $line['escrituracao'] === 'S'
                    && $line['indicador_superavit_financeiro'] === 'F'
                ) {
                return true;
            }
            return false;
        };
        $saldoDevedor22 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor22 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.2.2.1.3.01')
                    && $line['escrituracao'] === 'S'
                ) {
                return true;
            }
            return false;
        };
        $saldoDevedorEmpenhadoALiquidar = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorEmpenhadoALiquidar = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.3.1.1')
                    && $line['escrituracao'] === 'S'
                ) {
                return true;
            }
            return false;
        };
        $saldoDevedorRPNP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorRPNP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(
                ($saldoCredorControle - $saldoDevedorControle),
                (
                    ($saldoDevedor1 - $saldoCredor1)
                    - ($saldoCredor21 - $saldoDevedor21)
                    - ($saldoCredor22 - $saldoDevedor22)
                    - ($saldoCredorEmpenhadoALiquidar - $saldoDevedorEmpenhadoALiquidar)
                    - ($saldoCredorRPNP - $saldoDevedorRPNP)
                )
            );
        
        $this->saldoVerificado(__METHOD__, '8.2.1.1.1');
    }
    
    /**
     * Recursos comprometidos por empenho
     */
    public function testRecursosComprometidosPorEmpenhoALiquidar() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.2.1.1.2') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorControle = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorControle = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.2.2.1.3.01')
                    && $line['escrituracao'] === 'S'
                ) {
                return true;
            }
            return false;
        };
        $saldoDevedorEmpenhadoALiquidar = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorEmpenhadoALiquidar = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.3.1.1')
                    && $line['escrituracao'] === 'S'
                ) {
                return true;
            }
            return false;
        };
        $saldoDevedorRPNP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorRPNP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $this->comparar(
                ($saldoCredorControle - $saldoDevedorControle),
                (
                    ($saldoCredorEmpenhadoALiquidar - $saldoDevedorEmpenhadoALiquidar)
                    + ($saldoCredorRPNP - $saldoDevedorRPNP)
                )
            );
        
        $this->saldoVerificado(__METHOD__, '8.2.1.1.2');
    }
    
    /**
     * Recursos comprometidos por liquidação/extra
     */
    public function testRecursosComprometidosPorLiquidacaoEExtrasAPagar() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.2.1.1.3') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorControle = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorControle = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '2.1.')
                    && $line['escrituracao'] === 'S'
                    && $line['indicador_superavit_financeiro'] === 'F'
                ) {
                return true;
            }
            return false;
        };
        $saldoDevedor21 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor21 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '2.2.')
                    && $line['escrituracao'] === 'S'
                    && $line['indicador_superavit_financeiro'] === 'F'
                ) {
                return true;
            }
            return false;
        };
        $saldoDevedor22 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor22 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '1.1.3.2')
                    && $line['escrituracao'] === 'S'
                    && $line['indicador_superavit_financeiro'] === 'F'
                ) {
                return true;
            }
            return false;
        };
        $saldoDevedor1 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor1 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $this->comparar(
                ($saldoCredorControle - $saldoDevedorControle),
                (
                    ($saldoCredor21 - $saldoDevedor21)
                    + ($saldoCredor22 - $saldoDevedor22)
                    - ($saldoDevedor1 - $saldoCredor1)
                )
            );
        
        $this->saldoVerificado(__METHOD__, '8.2.1.1.3');
    }
    
    /**
     * Rateio UPA Santa Rosa
     */
    public function testValoresTransferidosPorContratoDeRateioUpaSantaRosa() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.5.3.1.0.01') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorControle = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorControle = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                    $line['projativ'] == 2063
                ) {
                return true;
            }
            return false;
        };
        $pago = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'valor_pago', $filter);
        
        
        $this->comparar(
                ($saldoCredorControle - $saldoDevedorControle),
                $pago
            );
        
        $this->saldoVerificado(__METHOD__, '8.5.3.1.0.01');
    }
    
    /**
     * Rateio SAMU Santa Rosa
     */
    public function testValoresTransferidosPorContratoDeRateioSamuSantaRosa() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.5.3.1.0.02') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorControle = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorControle = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                    $line['projativ'] == 2067
                ) {
                return true;
            }
            return false;
        };
        $pago = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'valor_pago', $filter);
        
        
        $this->comparar(
                ($saldoCredorControle - $saldoDevedorControle),
                $pago
            );
        
        $this->saldoVerificado(__METHOD__, '8.5.3.1.0.02');
    }
    
    /**
     * Rateio cofron
     */
    public function testValoresTransferidosPorContratoDeRateioMensalidadeCofron() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.5.3.1.0.03') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorControle = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorControle = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                    $line['projativ'] == 2
                ) {
                return true;
            }
            return false;
        };
        $pago = $this->somaColuna($this->getDataFrame('BAL_DESP'), 'valor_pago', $filter);
        
        
        $this->comparar(
                ($saldoCredorControle - $saldoDevedorControle),
                $pago
            );
        
        $this->saldoVerificado(__METHOD__, '8.5.3.1.0.03');
    }
    
    /**
     * Testa o fechamento dos lançamentos em contas de controle
     */
    public function testFechamentoDosLancamentosNasContasDeControle() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '7.') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $debitosCD = $this->somaColuna($this->getDataFrame('BAL_VER'), 'movimentacao_debito', $filter);
        $creditosCD = $this->somaColuna($this->getDataFrame('BAL_VER'), 'movimentacao_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '8.') && $line['escrituracao'] === 'S'
            ) {
                return true;
            }
            return false;
        };
        $debitosCC = $this->somaColuna($this->getDataFrame('BAL_VER'), 'movimentacao_debito', $filter);
        $creditosCC = $this->somaColuna($this->getDataFrame('BAL_VER'), 'movimentacao_credito', $filter);

        $this->comparar(($debitosCD + $debitosCC), ($creditosCD + $creditosCC));
        
        $this->naturezaVerificada(__METHOD__, '7.', '8.');
    }
    
    /**
     * Fechamento 7.1.1.3.1.03/8.1.1.3.1.03
     */
    public function testFechamentoDosNiveisDasContasDeControleDeContratosDeAlugueis() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.1.1.3.1.03') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.1.1.3.1.03') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.1.1.3.1.03', '8.1.1.3.1.03');
    }
    
    /**
     * Fechamento 7.1.1.9.1.04/8.1.1.9.1.04
     */
    public function testFechamentoDosNiveisDasContasDeControleDeBensDoImobilizadoCedidos() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.1.1.9.1.04') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.1.1.9.1.04') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.1.1.9.1.04', '8.1.1.9.1.04');
    }
    
    /**
     * Fechamento 7.1.1.9.1.01/8.1.1.9.1.01
     */
    public function testFechamentoDosNiveisDasContasDeControleDePlanoDeAmortizacaoDoDeficitAtuarial() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.1.1.9.1.01') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.1.1.9.1.01') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.1.1.9.1.01', '8.1.1.9.1.01');
    }
    
    /**
     * Fechamento 7.1.1.9.1.05/8.1.1.9.1.05
     */
    public function testFechamentoDosNiveisDasContasDeControleDeAtivosContingentes() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.1.1.9.1.05') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.1.1.9.1.05') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.1.1.9.1.05', '8.1.1.9.1.05');
    }
    
    /**
     * Fechamento 7.1.2.2.1.01/8.1.2.2.1.01
     */
    public function testFechamentoDosNiveisDasContasDeControleDeObrigacoesConveniadas() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.1.2.2.1.01') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.1.2.2.1.01') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.1.2.2.1.01', '8.1.2.2.1.01');
    }
    
    /**
     * Fechamento 7.1.2.3.1.02/8.1.2.3.1.02
     */
    public function testFechamentoDosNiveisDasContasDeControleDeObrigacoesDeContratosDeServicos() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.1.2.3.1.02') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.1.2.3.1.02') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        $this->nivelVerificado(__METHOD__, '7.1.2.3.1.02', '8.1.2.3.1.02');
        
    }
    
    /**
     * Fechamento 7.1.2.3.1.04/8.1.2.3.1.04
     */
    public function testFechamentoDosNiveisDasContasDeControleDeObrigacoesDeContratosDeFornecimentoDeBens() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.1.2.3.1.04') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.1.2.3.1.04') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.1.2.3.1.04', '8.1.2.3.1.04');
    }
    
    /**
     * Fechamento 7.1.2.3.1.99/8.1.2.3.1.99
     */
    public function testFechamentoDosNiveisDasContasDeControleDeOutrasObrigacoesContratuais() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.1.2.3.1.99') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.1.2.3.1.99') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.1.2.3.1.99', '8.1.2.3.1.99');
    }
    
    /**
     * Fechamento 7.1.2.9.1.01/8.1.2.9.1.01
     */
    public function testFechamentoDosNiveisDasContasDeControleDePrecatoriosIncluidosNoOrcamento() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.1.2.9.1.01') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.1.2.9.1.01') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.1.2.9.1.01', '8.1.2.9.1.01');
    }
    
    /**
     * Fechamento 7.1.2.9.1.02/8.1.2.9.1.02
     */
    public function testFechamentoDosNiveisDasContasDeControleDePrecatoriosNaoIncluidosNoOrcamento() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.1.2.9.1.02') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.1.2.9.1.02') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.1.2.9.1.02', '8.1.2.29.1.02');
    }
    
    /**
     * Fechamento 7.2.1.1/8.2.1.1
     */
    public function testFechamentoDosNiveisDasContasDeControleDeDisponibilidades() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.2.1.1') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.2.1.1') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.2.1.1', '8.2.1.1');
    }
    
    /**
     * Fechamento 7.4.1.1.1/8.4.1.1.1
     */
    public function testFechamentoDosNiveisDasContasDeControleDePassivosContingentesDeDemandasJudiciais() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.4.1.1.1') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.4.1.1.1') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.4.1.1.1', '8.4.1.1.1');
    }
    
    /**
     * Fechamento 7.4.1.1.2/8.4.1.1.2
     */
    public function testFechamentoDosNiveisDasContasDeControleDePassivosContingentesDeDividasEmReconhecimento() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.4.1.1.2') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.4.1.1.2') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.4.1.1.2', '8.4.1.1.2');
    }
    
    /**
     * Fechamento 7.4.1.1.9/8.4.1.1.9
     */
    public function testFechamentoDosNiveisDasContasDeControleDeOutrosPassivosContingentes() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.4.1.1.9') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.4.1.1.9') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.4.1.1.9', '8.4.1.1.9');
    }
    
    /**
     * Fechamento 7.5.3.1/8.5.3.1
     */
    public function testFechamentoDosNiveisDasContasDeControleDeValoresTransferidosPorContratoDeRateio() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.5.3.1') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.5.3.1') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.5.3.1', '8.5.3.1');
    }
    
    /**
     * Fechamento 7.5.3.2/8.5.3.2
     */
    public function testFechamentoDosNiveisDasContasDeControleDeDespesasExecutadasEmConsorcio() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.5.3.2') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.5.3.2') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.5.3.2', '8.5.3.2');
    }
    
    /**
     * Fechamento 7.9.1.1.3/8.9.1.1.3
     */
    public function testFechamentoDosNiveisDasContasDeControleDeOutrasResponsabilidadesDeTerceiros() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.9.1.1.3') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.9.1.1.3') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.9.1.1.3', '8.9.1.1.3');
    }
    
    /**
     * Fechamento 7.9.1.2.1/8.9.1.2.1
     */
    public function testFechamentoDosNiveisDasContasDeControleDeSuprimentoDeFundos() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.9.1.2.1') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.9.1.2.1') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.9.1.2.1', '8.9.1.2.1');
    }
    
    /**
     * Fechamento 7.9.2.6/8.9.2.6
     */
    public function testFechamentoDosNiveisDasContasDeControleDePagamentosSemRespaldoOrcamentario() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.9.2.6') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.9.2.6') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.9.2.6', '8.9.2.6');
    }
    
    /**
     * Fechamento 7.9.2.9/8.9.2.9
     */
    public function testFechamentoDosNiveisDasContasDeControleDeResponsaveisPorDanosAoPatrimonio() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.9.2.9') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.9.2.9') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.9.2.9', '8.9.2.9');
    }
    
    /**
     * Fechamento 7.9.9.9.1/8.9.9.9.1
     */
    public function testFechamentoDosNiveisDasContasDeControleDeDespesaSemEmpenhoPrevio() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.9.9.9.1') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.9.9.9.1') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.9.9.9.1', '8.9.9.9.1');
    }
    
    /**
     * Fechamento 7.9.9.9.2/8.9.9.9.2
     */
    public function testFechamentoDosNiveisDasContasDeControleDeSuperavitFinanceiroDisponivel() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.9.9.9.2') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.9.9.9.2') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.9.9.9.2', '8.9.9.9.2');
    }
    
    /**
     * Fechamento 7.9.9.9.4/8.9.9.9.4
     */
    public function testFechamentoDosNiveisDasContasDeControleDeLimiteDeSuplementacao() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '7.9.9.9.4') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor7 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '8.9.9.9.4') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor8 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor7 - $saldoCredor7), ($saldoCredor8 - $saldoDevedor8));
        
        $this->nivelVerificado(__METHOD__, '7.9.9.9.4', '8.9.9.9.4');
    }
    
    /**
     * Obrigações patronais ao RPPS
     */
    public function testSaldoFinalDeContribuicaoAoRppsIgualAoSaldoLiquidadoAPagar() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '2.1.1.4.2.01.02') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (str_starts_with($line['rubrica'], '3.1.91.13')) {
                return true;
            }
            return false;
        };
        $liquidado = $this->somaColuna($this->getDataFrame('LIQUIDACAO'), 'valor_liquidacao', $filter);
        $pago = $this->somaColuna($this->getDataFrame('PAGAMENTO'), 'valor_pagamento', $filter);

        $this->comparar(($liquidado - $pago), ($saldoCredor - $saldoDevedor));
        
        $this->saldoVerificado(__METHOD__, '2.1.1.4.2.01.02');
    }
    
    /**
     * Obrigações patronais ao RGPS
     */
    public function testSaldoFinalDeContribuicaoAoRgpsIgualAoSaldoLiquidadoAPagar() {
        $filter = function (array $line): bool {
            if (
                    (
                        str_starts_with($line['conta_contabil'], '2.1.1.4.3.01.01.02')
                        || str_starts_with($line['conta_contabil'], '2.1.1.4.3.01.03.02')
                    )
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['rubrica'], '3.1.90.04.15.01')
                    || str_starts_with($line['rubrica'], '3.1.90.13.02')
                    || str_starts_with($line['rubrica'], '3.3.90.47.20')
                ) {
                return true;
            }
            return false;
        };
        $liquidado = $this->somaColuna($this->getDataFrame('LIQUIDACAO'), 'valor_liquidacao', $filter);
        $pago = $this->somaColuna($this->getDataFrame('PAGAMENTO'), 'valor_pagamento', $filter);

        $this->comparar(($liquidado - $pago), ($saldoCredor - $saldoDevedor));
        
        $this->saldoVerificado(__METHOD__, '2.1.1.4.3.01.01.02', '2.1.1.4.3.01.03.02');
    }
    
    /**
     * Obrigações patronais ao FGTS
     */
    public function testSaldoFinalDeContribuicaoAoFgtsIgualAoSaldoLiquidadoAPagar() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '2.1.1.4.3.05.02')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['rubrica'], '3.1.90.13.01')
                ) {
                return true;
            }
            return false;
        };
        $liquidado = $this->somaColuna($this->getDataFrame('LIQUIDACAO'), 'valor_liquidacao', $filter);
        $pago = $this->somaColuna($this->getDataFrame('PAGAMENTO'), 'valor_pagamento', $filter);

        $this->comparar(($liquidado - $pago), ($saldoCredor - $saldoDevedor));
        
        $this->saldoVerificado(__METHOD__, '2.1.1.4.3.05.02');
    }
    
    /**
     * Testa os restos a pagar não processados inscritos no ano
     */
    public function testRestosAPagarNaoProcessadosInscritosNoExercicio() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '5.3.1.1')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.2.2.9.2.01.01')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorAnt = $this->somaColuna($this->getDataFrame('BVER_ANT'), 'saldo_atual_debito', $filter);
        $saldoCredorAnt = $this->somaColuna($this->getDataFrame('BVER_ANT'), 'saldo_atual_credito', $filter);
        
        $this->comparar(($saldoCredorAnt - $saldoDevedorAnt), ($saldoDevedorRP - $saldoCredorRP));
        
        $this->saldoVerificado(__METHOD__, '5.3.1.1');
    }
    
    /**
     * Testa os restos a pagar processados inscritos no ano
     */
    public function testRestosAPagarProcessadosInscritosNoExercicio() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '5.3.2.1')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.2.2.9.2.01.03')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorAnt = $this->somaColuna($this->getDataFrame('BVER_ANT'), 'saldo_atual_debito', $filter);
        $saldoCredorAnt = $this->somaColuna($this->getDataFrame('BVER_ANT'), 'saldo_atual_credito', $filter);
        
        $this->comparar(($saldoCredorAnt - $saldoDevedorAnt), ($saldoDevedorRP - $saldoCredorRP));
        
        $this->saldoVerificado(__METHOD__, '5.3.2.1');
        
    }
    
    /**
     * Saldo inicial de RPNP
     */
    public function testRestosAPagarNaoProcessadosInscritos() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '5.3.1')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $saldoRP = $this->somaColuna($this->getDataFrame('RESTOS_PAGAR'), 'saldo_inicial_nao_processados', $filter);
        
        $this->comparar($saldoRP, ($saldoDevedorRP - $saldoCredorRP));
        
        $this->saldoVerificado(__METHOD__, '5.3.1');
    }
    
    /**
     * Saldo inicial de RPP
     */
    public function testRestosAPagarProcessadosInscritos() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '5.3.2')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $saldoRP = $this->somaColuna($this->getDataFrame('RESTOS_PAGAR'), 'saldo_inicial_processados', $filter);
        
        $this->comparar($saldoRP, ($saldoDevedorRP - $saldoCredorRP));
        
        $this->saldoVerificado(__METHOD__, '5.3.2');
    }
    
    /**
     * saldo de rpnp inscricao no exercicio igual a zero
     */
    public function testRestosAPagarNaoProcessadosInscricaoNoExercicioIgualAZero() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '5.3.1.7')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $this->comparar(0, ($saldoDevedorRP - $saldoCredorRP));
        
        $this->saldoVerificado(__METHOD__, '5.3.1.7');
    }
    
    /**
     * saldo de rpp inscricao no exercicio igual a zero
     */
    public function testRestosAPagarProcessadosInscricaoNoExercicioIgualAZero() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '5.3.2.7')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $this->comparar(0, ($saldoDevedorRP - $saldoCredorRP));
        $this->saldoVerificado(__METHOD__, '5.3.2.7');
        
    }
    
    public function testRestosAPagarNaoProcessadosInscricaoNoExercicioIgualEmpenhadoNaoLiquidado() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '5.3.1.7')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_anterior_debito', $filter);
        $saldoCredorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_anterior_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.2.2.1.3.01')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BVER_ANT'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BVER_ANT'), 'saldo_atual_credito', $filter);

        $this->comparar(($saldoCredor - $saldoDevedor), ($saldoDevedorRP - $saldoCredorRP));
        
        $this->saldoVerificado(__METHOD__, '5.3.1.7');
    }
    
    public function testRestosAPagarProcessadosInscricaoNoExercicioIgualLiquidadoNaoPago() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '5.3.2.7')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_anterior_debito', $filter);
        $saldoCredorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_anterior_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.2.2.1.3.03')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BVER_ANT'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BVER_ANT'), 'saldo_atual_credito', $filter);

        $this->comparar(($saldoCredor - $saldoDevedor), ($saldoDevedorRP - $saldoCredorRP));
        
        $this->saldoVerificado(__METHOD__, '5.3.2.7');
    }
    
    public function testRestosAPagarNaoProcessadosALiquidar() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.3.1.1')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $saldoRP = $this->somaColuna($this->getDataFrame('RESTOS_PAGAR'), 'saldo_final_nao_processados', $filter);
        
        $this->comparar($saldoRP, ($saldoCredorRP - $saldoDevedorRP));
        
        $this->saldoVerificado(__METHOD__, '6.3.1.1');
    }
    
    public function testRestosAPagarNaoProcessadosAPagar() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.3.1.3')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $liquidadosRP = $this->somaColuna($this->getDataFrame('RESTOS_PAGAR'), 'nao_processados_liquidados', $filter);
        $pagosRP = $this->somaColuna($this->getDataFrame('RESTOS_PAGAR'), 'nao_processados_pagos', $filter);
        
        $this->comparar($liquidadosRP - $pagosRP, ($saldoCredorRP - $saldoDevedorRP));
        
        $this->saldoVerificado(__METHOD__, '6.3.1.3');
    }
    
    public function testRestosAPagarNaoProcessadosPagos() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.3.1.4')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $pagosRP = $this->somaColuna($this->getDataFrame('RESTOS_PAGAR'), 'nao_processados_pagos', $filter);
        
        $this->comparar($pagosRP, ($saldoCredorRP - $saldoDevedorRP));
        
        $this->saldoVerificado(__METHOD__, '6.3.1.4');
    }
    
    public function testRestosAPagarNaoProcessadosALiquidarInscricaoNoExercicioIgualAZero() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.3.1.7.1')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $this->comparar(0, ($saldoCredorRP - $saldoDevedorRP));
        
        $this->saldoVerificado(__METHOD__, '6.3.1.7.1');
    }
    
    public function testRestosAPagarNaoProcessadosALiquidarInscricaoNoExercicioIgualEmpenhadoNaoLiquidado() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.3.1.7.1')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_anterior_debito', $filter);
        $saldoCredorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_anterior_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.2.2.1.3.01')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BVER_ANT'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BVER_ANT'), 'saldo_atual_credito', $filter);

        $this->comparar(($saldoCredor - $saldoDevedor), ($saldoCredorRP - $saldoDevedorRP));
        
        $this->saldoVerificado(__METHOD__, '6.3.1.7.1');
    }
    
    public function testRestosAPagarNaoProcessadosCancelados() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.3.1.9')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $pagosRP = $this->somaColuna($this->getDataFrame('RESTOS_PAGAR'), 'nao_processados_cancelados', $filter);
        
        $this->comparar($pagosRP, ($saldoCredorRP - $saldoDevedorRP));
        
        $this->saldoVerificado(__METHOD__, '6.3.1.9');
    }
    
    public function testRestosAPagarProcessadosAPagar() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.3.2.1')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $liquidadosRP = $this->somaColuna($this->getDataFrame('RESTOS_PAGAR'), 'saldo_inicial_processados', $filter);
        $pagosRP = $this->somaColuna($this->getDataFrame('RESTOS_PAGAR'), 'processados_pagos', $filter);
        
        $this->comparar($liquidadosRP - $pagosRP, ($saldoCredorRP - $saldoDevedorRP));
        
        $this->saldoVerificado(__METHOD__, '6.3.2.1');
    }
    
    public function testRestosAPagarProcessadosPagos() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.3.2.2')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $pagosRP = $this->somaColuna($this->getDataFrame('RESTOS_PAGAR'), 'processados_pagos', $filter);
        
        $this->comparar($pagosRP, ($saldoCredorRP - $saldoDevedorRP));
        
        $this->saldoVerificado(__METHOD__, '6.3.2.2');
    }
    
    public function testRestosAPagarProcessadosCancelados() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.3.2.9')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $filter = function (array $line): bool {
            return true;
        };
        $pagosRP = $this->somaColuna($this->getDataFrame('RESTOS_PAGAR'), 'processados_cancelados', $filter);
        
        $this->comparar($pagosRP, ($saldoCredorRP - $saldoDevedorRP));
        
        $this->saldoVerificado(__METHOD__, '6.3.2.9');
    }
    
    public function testRestosAPagarProcessadosALiquidarInscricaoNoExercicioIgualAZero() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.3.2.7')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);

        $this->comparar(0, ($saldoCredorRP - $saldoDevedorRP));
        
        $this->saldoVerificado(__METHOD__, '6.3.2.7');
    }
    
    public function testRestosAPagarProcessadosALiquidarInscricaoNoExercicioIgualEmpenhadoNaoLiquidado() {
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.3.2.7')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_anterior_debito', $filter);
        $saldoCredorRP = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_anterior_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['conta_contabil'], '6.2.2.1.3.03')
                    && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BVER_ANT'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BVER_ANT'), 'saldo_atual_credito', $filter);

        $this->comparar(($saldoCredor - $saldoDevedor), ($saldoCredorRP - $saldoDevedorRP));
        
        $this->saldoVerificado(__METHOD__, '6.3.2.7');
        
    }
    
    public function testImobilizadoTestadoManualmente() {
        $this->assertTrue(true);
        $this->conferenciaExterna(__METHOD__, '1.2.3');
    }
    
    public function testSuprimentosDeFundosIndividualmenteTestadosManualmente() {
        $this->assertTrue(true);
        $this->conferenciaExterna(__METHOD__, '1.1.3.1.1.02', '8.9.1.2.1');
    }
    
    public function testDisponibilidadesTestadasManualmente() {
        $this->assertTrue(true);
        $this->conferenciaExterna(__METHOD__, '1.1.1', '1.1.4');
    }
    
    public function testSaldosInvertidosTestadosManualmente() {
        $this->assertTrue(true);
    }
    
    public function testContasPatrimoniaisComIndicadorDeSuperavitFinanceiroIgualXTestadasManualmente() {
        $this->assertTrue(true);
    }
    
    public function testFechamentoDasFontesDeRecursosTestadoManualmente() {
        $this->assertTrue(true);
    }
    
    
}
