<?php

namespace IDDRS\ContTest\Config;

/**
 * Description of Config
 *
 * @author Everton
 */
class Config {
    use BaseConfig;
    
    public function getCsvDir(): string {
        return $this->csvDir;
    }
}
