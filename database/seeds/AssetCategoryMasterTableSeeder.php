<?php

use Illuminate\Database\Seeder;

class AssetCategoryMasterTableSeeder extends Seeder
{
    protected $csvFileName = "AssetCategoryMaster.csv";
    protected $csvFilePath = "";
    protected $tableName = 'asset_category_masters';
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!$this->csvFilePath)$this->csvFilePath = $this->getCsvFilePath($this->csvFileName);
        $insertInstanceArray = $this->getArrayFromCsv();
        DB::table($this->tableName)->insert($insertInstanceArray);

    }

    protected function getCsvFilePath($filename)
    {
        return database_path() . "/seeds/${filename}";
    }

    protected function getArrayFromCsv()
    {
        if(!$this->csvFilePath) {
            throw new Exception("not set csv File");
        }
        $content = file_get_contents($this->csvFilePath);
        $content = str_replace(['\r\n','\r','\n'], '\n', $content);
        $contentArray = explode("\n", $content);
        $indexArray = array_shift($contentArray);
        $indexArray = explode(',', $indexArray);
        if(count($contentArray) !== \App\AssetCategoryMaster::MASTER_COUNT){
            throw new Exception("invalid master data. count=" . count($contentArray).". MASTER_COUNT=" . \App\AssetCategoryMaster::MASTER_COUNT);
        }
        $returnArray = [];
        foreach ($contentArray as $index => $recode) {
            $recodeArray = explode(',', $recode);
            $instanceArray = [];
            foreach($recodeArray as $columnNumber => $value){
                $instanceArray[$indexArray[$columnNumber]] = trim($value);
            }
            $returnArray[$instanceArray['id']] = $instanceArray;
        }

        return $returnArray;
    }

}
