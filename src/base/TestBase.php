<?php

namespace IDDRS\ContTest\Base;

use Exception;
use PTK\DataFrame\DataFrame;
use PTK\DataFrame\Reader\CSVReader;
use PTK\FS\Path;

/**
 * Utilitários para os testes
 *
 * @author Everton
 */
trait TestBase {
    
    public static function setUpBeforeClass(): void {
        ini_set('memory_limit', -1);
        self::preparaCoberturaDeContas();
    }
    
    public function setUp(): void {
    }
    
    public static function tearDownAfterClass(): void {
        self::finalizaCoberturaDeContas();
    }
    
    protected function filtraDadosPorUniorcam(DataFrame $dataFrame): DataFrame {

        $filter = function(array $line): bool {
            if(!key_exists('uniorcam', $line)){
                return true;
            }
            $uniorcam = (int) $line['uniorcam'];
//            var_dump($uniorcam);
            if($uniorcam >= $this->uniorcamInicial && $uniorcam <= $this->uniorcamFinal){
                return true;
            }
            return false;
        };
        $dataFrame = DataFrame::filter($dataFrame, $filter);
//        print_r($dataFrame->getAsArray()); exit();
        return $dataFrame;
    }

    protected function getDataFrame(string $fileId): DataFrame {
        $path = new Path($this->csvDir, $fileId.'.csv');
//        echo $path->getRealPath(), PHP_EOL;
        $handle = fopen($path->getRealPath(), 'r');
        if($handle === false){
            throw new Exception("Não foi possível abrir {$path->getPath()}");
        }
        
        $reader = new CSVReader($handle, ';', true);
        $dataFrame = new DataFrame($reader);
//        print_r($dataFrame->getAsArray());exit();
        return $this->filtraDadosPorUniorcam($dataFrame);
    }

    protected function somaColuna(DataFrame $dataFrame, string $campo, callable $filter) {
        $filtrado = DataFrame::filter($dataFrame, $filter);
        if(sizeof($filtrado->getAsArray()) === 0){
            return 0;
        }
//        print_r($filtrado->getAsArray());exit();
        $filtrado->applyOnCols($campo, function ($cell): float {
            $cell = str_replace('.', '', $cell);
            $cell = str_replace(',', '.', $cell);
            return (float) $cell;
        });

        return round($filtrado->sumCol($campo), 2);
    }
    
    protected function comparar($valor1, $valor2): void {
        $this->assertEquals(round($valor1, 2), round($valor2, 2));
    }
    
    protected function saldoVerificado(string $rule, string ...$contas): void {
        $uniorcamInicial = $this->uniorcamInicial;
        $uniorcamFinal = $this->uniorcamFinal;
        $handle = fopen('cache/cobertura_saldo.log', 'a');
        $boom = explode('::', $rule);
        $rule = $boom[1];
        foreach ($contas as $cc){
            fwrite($handle, "$cc;$uniorcamInicial;$uniorcamFinal;saldo;$rule".PHP_EOL);
        }
        fclose($handle);
    }
    
    protected function naturezaVerificada(string $rule, string ...$contas): void {
        $uniorcamInicial = $this->uniorcamInicial;
        $uniorcamFinal = $this->uniorcamFinal;
        $handle = fopen('cache/cobertura_natureza.log', 'a');
        $boom = explode('::', $rule);
        $rule = $boom[1];
        foreach ($contas as $cc){
            fwrite($handle, "$cc;$uniorcamInicial;$uniorcamFinal;natureza;$rule".PHP_EOL);
        }
        fclose($handle);
    }
    
    protected function nivelVerificado(string $rule, string ...$contas): void {
        $uniorcamInicial = $this->uniorcamInicial;
        $uniorcamFinal = $this->uniorcamFinal;
        $handle = fopen('cache/cobertura_nivel.log', 'a');
        $boom = explode('::', $rule);
        $rule = $boom[1];
        foreach ($contas as $cc){
            fwrite($handle, "$cc;$uniorcamInicial;$uniorcamFinal;nivel;$rule".PHP_EOL);
        }
        fclose($handle);
    }
    
    protected function conferenciaExterna(string $rule, string ...$contas): void {
        $uniorcamInicial = $this->uniorcamInicial;
        $uniorcamFinal = $this->uniorcamFinal;
        $handle = fopen('cache/cobertura_externo.log', 'a');
        $boom = explode('::', $rule);
        $rule = $boom[1];
        foreach ($contas as $cc){
            fwrite($handle, "$cc;$uniorcamInicial;$uniorcamFinal;externo;$rule".PHP_EOL);
        }
        fclose($handle);
    }
    
    protected static function preparaCoberturaDeContas(): void {
        foreach([
            'cache/cobertura_saldo.log',
            'cache/cobertura_natureza.log',
            'cache/cobertura_nivel.log',
            'cache/cobertura_externo.log'
        ] as $filename){
            if(file_exists($filename)){
                unlink($filename);
            }
        }
    }
    
    protected static function finalizaCoberturaDeContas(): void {
        
        $classe = __CLASS__;
        $boom = explode('\\', $classe);
        $classe = $boom[array_key_last($boom)];
        
        $filename = "cache/report-$classe.log";
        $handle = fopen($filename, 'w');
        fwrite($handle, 'conta_contabil;uniorcam_inicial;uniorcam_final;conferencia;regra'.PHP_EOL);
        fwrite($handle, file_get_contents('cache/cobertura_externo.log'));
        fwrite($handle, file_get_contents('cache/cobertura_natureza.log'));
        fwrite($handle, file_get_contents('cache/cobertura_nivel.log'));
        fwrite($handle, file_get_contents('cache/cobertura_saldo.log'));
        fclose($handle);
    }
}
