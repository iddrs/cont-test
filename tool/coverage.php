<?php

use IDDRS\ContTest\Config\Config;
use PTK\DataFrame\DataFrame;
use PTK\DataFrame\Reader\CSVReader;

require_once 'vendor/autoload.php';

//carrega o balver e prapara ele
$config = new Config();
$filename = $config->getCsvDir().'BAL_VER.csv';
$handle = fopen($filename, 'r');
$dfBalVer = new DataFrame(new CSVReader($handle, ';', true));
fclose($handle);

$dfBalVerPreparado = DataFrame::getCols($dfBalVer, 'conta_contabil', 'uniorcam', 'saldo_atual_debito', 'saldo_atual_credito', 'especificacao', 'escrituracao', 'data_inicial', 'data_final', 'data_geracao');
$balVer = $dfBalVerPreparado->getAsArray();

//acrescenta novas colunas
foreach ($balVer as $index => $line){
    $balVer[$index]['conferencia_externa'] = [];
    $balVer[$index]['conferencia_saldo'] = [];
    $balVer[$index]['conferencia_nivel'] = [];
    $balVer[$index]['conferencia_natureza'] = [];
}

//carrega o arquivo de cobertura
if(!key_exists(1, $argv)){
    echo 'Nenhum arquivo de cobdertura de contas fornecido. Use php tool/coverage.php cache/report-ClasseTest.log'.PHP_EOL;
    die();
}
$coverageFile = $argv[1];
if(!file_exists($coverageFile)){
    echo "Arquivo de cobertura de contas não existe: [$coverageFile]".PHP_EOL;
    die();
}

$handle = fopen($coverageFile, 'r');
if($handle === false){
    echo "Arquivo de cobertura de contas não pode ser aberto: [$coverageFile]".PHP_EOL;
    die();
}
echo "Usando $coverageFile".PHP_EOL;

$dfCoverData = new DataFrame(new CSVReader($handle, ';', true));
$coverData = $dfCoverData->getAsArray();

$uniorcamInicial = $coverData[0]['uniorcam_inicial'];
$uniorcamFinal = $coverData[0]['uniorcam_final'];

// filtra bal ver de acordo com a unirocam de coverage
$balVerCoverage = [];
foreach ($balVer as $index => $line){
    if($line['uniorcam'] >= $uniorcamInicial && $line['uniorcam'] <= $uniorcamFinal){
        $balVerCoverage[] = $line;
    }
}

if(sizeof($balVerCoverage) === 0){
    echo "Nenhum dado filtrado para as Unidades Orçamentárias $uniorcamInicial e $uniorcamFinal".PHP_EOL;
    die();
}

//computa a cobertura de contas com conferência externa
foreach ($coverData as $cover){
    if($cover['conferencia'] !== 'externo'){
        continue;
    }
    
    $cc = $cover['conta_contabil'];
    $uniorcamInicial = $cover['uniorcam_inicial'];
    $uniorcamFinal = $cover['uniorcam_final'];
    $regra = $cover['regra'];
    foreach ($balVerCoverage as $index => $line){
        if(
            str_starts_with($line['conta_contabil'], $cc)
            && $line['uniorcam'] >= $uniorcamInicial
            && $line['uniorcam'] <= $uniorcamFinal
        ){
            $balVerCoverage[$index]['conferencia_externa'][] = $regra;
        }
    }
}

//computa a cobertura de contas com conferência de níveis
foreach ($coverData as $cover){
    if($cover['conferencia'] !== 'nivel'){
        continue;
    }
    
    $cc = $cover['conta_contabil'];
    $uniorcamInicial = $cover['uniorcam_inicial'];
    $uniorcamFinal = $cover['uniorcam_final'];
    $regra = $cover['regra'];
    foreach ($balVerCoverage as $index => $line){
        if(
            str_starts_with($line['conta_contabil'], $cc)
            && $line['uniorcam'] >= $uniorcamInicial
            && $line['uniorcam'] <= $uniorcamFinal
        ){
            $balVerCoverage[$index]['conferencia_nivel'][] = $regra;
        }
    }
}

//computa a cobertura de contas com conferência de natureza
foreach ($coverData as $cover){
    if($cover['conferencia'] !== 'natureza'){
        continue;
    }
    
    $cc = $cover['conta_contabil'];
    $uniorcamInicial = $cover['uniorcam_inicial'];
    $uniorcamFinal = $cover['uniorcam_final'];
    $regra = $cover['regra'];
    foreach ($balVerCoverage as $index => $line){
        if(
            str_starts_with($line['conta_contabil'], $cc)
            && $line['uniorcam'] >= $uniorcamInicial
            && $line['uniorcam'] <= $uniorcamFinal
        ){
            $balVerCoverage[$index]['conferencia_natureza'][] = $regra;
        }
    }
}

//computa a cobertura de contas com conferência de saldo
foreach ($coverData as $cover){
    if($cover['conferencia'] !== 'saldo'){
        continue;
    }
    
    $cc = $cover['conta_contabil'];
    $uniorcamInicial = $cover['uniorcam_inicial'];
    $uniorcamFinal = $cover['uniorcam_final'];
    $regra = $cover['regra'];
    foreach ($balVerCoverage as $index => $line){
        if(
            str_starts_with($line['conta_contabil'], $cc)
            && $line['uniorcam'] >= $uniorcamInicial
            && $line['uniorcam'] <= $uniorcamFinal
        ){
            $balVerCoverage[$index]['conferencia_saldo'][] = $regra;
        }
    }
}


//converte regras array para lista string
foreach ($balVerCoverage as $index => $line){
    $balVerCoverage[$index]['conferencia_externa'] = join(', ', $balVerCoverage[$index]['conferencia_externa']);
    $balVerCoverage[$index]['conferencia_saldo'] = join(', ', $balVerCoverage[$index]['conferencia_saldo']);
    $balVerCoverage[$index]['conferencia_nivel'] = join(', ', $balVerCoverage[$index]['conferencia_nivel']);
    $balVerCoverage[$index]['conferencia_natureza'] = join(', ', $balVerCoverage[$index]['conferencia_natureza']);
}

//salva o resultado
$filename = $coverageFile.'.csv';
$handle = fopen($filename, 'w');
if($handle === false){
    echo "Relatório de cobertura de contas não pode ser aberto: [$filename]".PHP_EOL;
    die();
}
echo "Salvando $filename".PHP_EOL;
$dfOutput = new DataFrame(new \PTK\DataFrame\Reader\ArrayReader($balVerCoverage));
$writer = new PTK\DataFrame\Writer\CSVWriter($dfOutput, $handle, ';', true);
$writer->write();
echo 'Tudo pronto!'.PHP_EOL;
exit(0);