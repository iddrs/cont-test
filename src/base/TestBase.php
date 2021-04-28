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

}
