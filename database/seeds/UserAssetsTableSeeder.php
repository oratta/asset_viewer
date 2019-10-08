<?php

use Illuminate\Database\Seeder;

class UserAssetsTableSeeder extends Seeder
{
    use CsvSeeder;
    protected $tableName = "user_assets";

    public function __construct()
    {
        $this->csvFileName = "UserAsset.csv";
        $this->formatMethod = [
            'value' => 'formatValue'
        ];

    }

    protected function formatValue($value)
    {
        $value = preg_replace("/^[ぁ-んァ-ヶー一-龠]+$/u", '', $value);
        $value = str_replace(',', '', $value);
        return intval(trim($value));
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $insertInstanceArray = $this->getArrayFromCsv();
        $insertInstanceArray = $this->addUserId($insertInstanceArray, UsersTableSeeder::TEST_USER_ID);
        DB::table($this->tableName)->insert($insertInstanceArray);
    }

    private function addUserId(array $datas, $userId)
    {
        foreach ($datas as &$data){
            $data['user_id'] = $userId;
        }

        return $datas;
    }
}
