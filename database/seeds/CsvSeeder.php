<?php
trait CsvSeeder
{
    protected $csvFileName = "";
    protected $csvFilePath = "";
    protected $expectedCount = 0;
    protected $formatMethod;

    protected function getCsvFilePath($filename)
    {
        return database_path() . "/seeds/${filename}";
    }

    protected function getArrayFromCsv()
    {
        if(!$this->csvFilePath) {
            if(!$this->csvFileName) throw new Exception('set $this->csvFileName');
            $this->csvFilePath = $this->getCsvFilePath($this->csvFileName);
        }
        $content = file_get_contents($this->csvFilePath);
        $content = str_replace(['\r\n','\r','\n'], '\n', $content);
        $contentArray = explode("\n", $content);
        $indexArray = array_shift($contentArray);
        $indexArray = explode(',', $indexArray);
        if($this->expectedCount && count($contentArray) !== $this->expectedCount){
            throw new Exception("invalid master data. count=" . count($contentArray).". expected=" . $this->expectedCount);
        }
        $returnArray = [];
        foreach ($contentArray as $index => $recode) {
            $recodeArray = explode(',', $recode);
            $instanceArray = [];
            foreach($recodeArray as $columnNumber => $value){
                $columnName = $indexArray[$columnNumber];
                $instanceArray[$columnName] = $this->__format($columnName, $value);
            }
            if(isset($instanceArray['id'])){
                $returnArray[$instanceArray['id']] = $instanceArray;
            }
            else {
                $returnArray[] = $instanceArray;
            }
        }

        return $returnArray;
    }

    private function __format($columnName, $value){
        if(isset($this->formatMethod[$columnName])){
            $formatMethod = $this->formatMethod[$columnName];
            $value = $this->$formatMethod($value);
        }
        else {
            $value = trim($value);
        }
        return $value;
    }
}