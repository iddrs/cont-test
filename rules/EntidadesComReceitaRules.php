<?php

namespace IDDRS\ContTest\Rules;

/**
 * Regras para todas as entidades que possuem receita.
 * 
 * @author Everton
 */
trait EntidadesComReceitaRules {

    /**
     * Previsão inicial da receita bruta
     */
    public function testPrevisaoInicialDaReceitaBrutaContabilizada() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '5.2.1.1.1') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (!str_starts_with($line['codigo_receita'], '9.') && $line['tipo_nivel'] === 'A') {
                return true;
            }
            return false;
        };
        $receitaOrcada = $this->somaColuna($this->getDataFrame('BAL_REC'), 'receita_orcada', $filter);
        
        $this->comparar(($saldoDevedor - $saldoCredor), $receitaOrcada);
        
        $this->saldoVerificado(__METHOD__, '5.2.1.1.1');
    }
    
    /**
     * Previsão inicial da dedução apra o FUNDEB
     */
    public function testPrevisaoDasDeducoesDaReceitaParaFundebContabilizada() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '5.2.1.1.2.01.01') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                str_starts_with($line['codigo_receita'], '9.')
                && $line['tipo_nivel'] === 'A'
                && $line['caracteristica_peculiar_receita'] == 105
            ) {
                return true;
            }
            return false;
        };
        $receitaOrcada = $this->somaColuna($this->getDataFrame('BAL_REC'), 'receita_orcada', $filter);
        
        $this->comparar(($saldoDevedor - $saldoCredor), $receitaOrcada);
        
        $this->saldoVerificado(__METHOD__, '5.2.1.1.2.01.01');
    }
    
    /**
     * Previsão inicial da dedução apra o FUNDEB
     */
    public function testPrevisaoDasOutrasDeducoesDaReceitaContabilizada() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '5.2.1.1.2.99') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                str_starts_with($line['codigo_receita'], '9.')
                && $line['tipo_nivel'] === 'A'
                && $line['caracteristica_peculiar_receita'] != 105
            ) {
                return true;
            }
            return false;
        };
        $receitaOrcada = $this->somaColuna($this->getDataFrame('BAL_REC'), 'receita_orcada', $filter);
        
        $this->comparar(($saldoDevedor - $saldoCredor), $receitaOrcada);
        
        $this->saldoVerificado(__METHOD__, '5.2.1.1.2.99');
    }
    
    /**
     * Reestimativa da receita
     */
    public function testReestimativaDaReceitaContabilizada() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '5.2.1.2.1.01') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                $line['tipo_nivel'] === 'A'
            ) {
                return true;
            }
            return false;
        };
        $receitaOrcada = $this->somaColuna($this->getDataFrame('BAL_REC'), 'receita_orcada', $filter);
        $previsaoAtualizada = $this->somaColuna($this->getDataFrame('BAL_REC'), 'previsao_atualizada', $filter);
        
        $this->comparar(($saldoDevedor - $saldoCredor), ($previsaoAtualizada - $receitaOrcada));
        
        $this->saldoVerificado(__METHOD__, '5.2.1.2.1.01');
    }
    
    /**
     * Crédito aberto por excesso de arrecadação
     */
    public function testCreditoAbertoPorExcessoDeArrecadacao() {
//        $this->markTestSkipped("Pulado porque a diferença é referente a atualizaçaõ de receita não feita na data. Corrigida em abril.");
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '5.2.2.1.3.02') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                $line['tipo_nivel'] === 'A'
            ) {
                return true;
            }
            return false;
        };
        $receitaOrcada = $this->somaColuna($this->getDataFrame('BAL_REC'), 'receita_orcada', $filter);
        $previsaoAtualizada = $this->somaColuna($this->getDataFrame('BAL_REC'), 'previsao_atualizada', $filter);
        
        $this->comparar(($saldoDevedor - $saldoCredor), ($previsaoAtualizada - $receitaOrcada));
        $this->saldoVerificado(__METHOD__, '5.2.2.1.3.02');
        
    }
    
    /**
     * Receitas a realizar
     */
    public function testSaldoDaReceitaARealizar() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '6.2.1.1') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if ($line['tipo_nivel'] === 'A') {
                return true;
            }
            return false;
        };
        $previsaoAtualizada = $this->somaColuna($this->getDataFrame('BAL_REC'), 'previsao_atualizada', $filter);
        $receitaArrecadada = $this->somaColuna($this->getDataFrame('BAL_REC'), 'receita_realizada', $filter);
        
        $this->comparar(($saldoCredor - $saldoDevedor), ($previsaoAtualizada - $receitaArrecadada));
        
        $this->saldoVerificado(__METHOD__, '6.2.1.1');
    }
    
    /**
     * Receita bruta arrecadada
     */
    public function testReceitaBrutaArrecadadaContabilizada() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '6.2.1.2') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (!str_starts_with($line['codigo_receita'], '9.') && $line['tipo_nivel'] === 'A') {
                return true;
            }
            return false;
        };
        $receitaArrecadada = $this->somaColuna($this->getDataFrame('BAL_REC'), 'receita_realizada', $filter);
        
        $this->comparar(($saldoCredor - $saldoDevedor), $receitaArrecadada);
        
        $this->saldoVerificado(__METHOD__, '6.2.1.2');
    }
    
    /**
     * Dedução para o fundeb realizada
     */
    public function testDeducaoDaReceitaParaOFundebArrecadadaContabilizada() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '6.2.1.3.1.01') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['codigo_receita'], '9.')
                    && $line['tipo_nivel'] === 'A'
                    && $line['caracteristica_peculiar_receita'] == 105
                ) {
                return true;
            }
            return false;
        };
        $receitaArrecadada = $this->somaColuna($this->getDataFrame('BAL_REC'), 'receita_realizada', $filter);
        
        $this->comparar(($saldoCredor - $saldoDevedor), $receitaArrecadada);
        
        $this->saldoVerificado(__METHOD__, '6.2.1.3.1.01');
    }
    
    /**
     * REnúncia de receitas arrecadadas
     */
    public function testDeducaoDaReceitaPorRenunciaArrecadadaContabilizada() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '6.2.1.3.2') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['codigo_receita'], '9.')
                    && $line['tipo_nivel'] === 'A'
                    && (
                        $line['caracteristica_peculiar_receita'] == 101
                        || $line['caracteristica_peculiar_receita'] == 103
                    )
                ) {
                return true;
            }
            return false;
        };
        $receitaArrecadada = $this->somaColuna($this->getDataFrame('BAL_REC'), 'receita_realizada', $filter) * -1;
        
        $this->comparar(($saldoDevedor - $saldoCredor), $receitaArrecadada);
        
        $this->saldoVerificado(__METHOD__, '6.2.1.3.2');
    }
    
    /**
     * Outras deduções da receita
     */
    public function testOutrasDeducaoDaReceitaArrecadadaContabilizada() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '6.2.1.3.9') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (
                    str_starts_with($line['codigo_receita'], '9.')
                    && $line['tipo_nivel'] === 'A'
                    && (
                        $line['caracteristica_peculiar_receita'] != 101
                        && $line['caracteristica_peculiar_receita'] != 103
                        && $line['caracteristica_peculiar_receita'] != 105
                    )
                ) {
                return true;
            }
            return false;
        };
        $receitaArrecadada = $this->somaColuna($this->getDataFrame('BAL_REC'), 'receita_realizada', $filter) * -1;
        
        $this->comparar(($saldoDevedor - $saldoCredor), $receitaArrecadada);
        
        $this->saldoVerificado(__METHOD__, '6.2.1.3.9');
    }
    
    /**
     * Testa 5.2.1/6.2.1
     */
    public function testFechamentoDosNiveisDasContasOrcamentariasDeReceita() {
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '5.2.1') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor5 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor5 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        $filter = function (array $line): bool {
            if (str_starts_with($line['conta_contabil'], '6.2.1') && $line['escrituracao'] === 'S') {
                return true;
            }
            return false;
        };
        $saldoDevedor6 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_debito', $filter);
        $saldoCredor6 = $this->somaColuna($this->getDataFrame('BAL_VER'), 'saldo_atual_credito', $filter);
        
        
        $this->comparar(($saldoDevedor5 - $saldoCredor5), ($saldoCredor6 - $saldoDevedor6));
        
        $this->saldoVerificado(__METHOD__, '5.2.1', '6.2.1');
    }
}
