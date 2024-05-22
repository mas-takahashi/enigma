<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class enigmaController extends Controller
{
    const AES_KEY = 'poweridnokey';
    const AES_IV = 'ivha16bytedemake';

    /**
     * エニグマ画面起動
     */
    public function enigma(Request $request)
    {
        return view('enigmaView');
    }

    /**
     * 暗号化
     */
    public function encrypt(Request $request)
    {
        $answer = enigmaController::encrypt_AES_256_CBC($request->input('text'));
        return response()->json([
            'status' => 'success',
            'answer' => $answer
        ], 200);
    }

    /**
     * 復号化
     */
    public function decrypt(Request $request)
    {
        $answer = enigmaController::decrypt_AES_256_CBC($request->input('text'));
        return response()->json([
            'status' => 'success',
            'answer' => $answer
        ], 200);
    }

    /**
     * 暗号化
     * @param string $data
     * @return string
     */
    function encrypt_AES_256_CBC($data)
    {
        return $data === null ? null :
            openssl_encrypt($data, 'AES-256-CBC', enigmaController::AES_KEY, 0, enigmaController::AES_IV);
    }

    /**
     * 復号化
     * @param string $data
     * @return string
     */
    function decrypt_AES_256_CBC($data)
    {
        return $data === null ? null :
            openssl_decrypt($data, 'AES-256-CBC', enigmaController::AES_KEY, 0, enigmaController::AES_IV);
    }



    public function db(Request $request)
    {
        $results = DB::select(
            "select " .
                "item_cd as 項目コード," .
                "item_name as 項目名," .
                "unit," .
                "m_item.item_subname " .
                "from m_item"
        );
        $columnNames = array_keys((array) $results[0]);

        //dd($columnNames);



        $tatejiku = ["kdate", "course_cd"];
        $yokojiku = ["claim_item_cd", "uriage"];

        $results = DB::select(
            "select " .
                "kdate," .
                "course_cd," .
                "claim_item_cd," .
                "sum(price) as uriage " .
                "from t_claim_item " .
                "where kdate >= '2000/05/01' and kdate <= '2000/05/10' " .
                "group by " .
                "kdate," .
                "course_cd," .
                "claim_item_cd"
        );


        $datas = enigmaController::createDataTable($tatejiku, $yokojiku, $results);
        dd($datas);




        return view('enigmaView');
    }


    /**
     * データテーブル作成
     */
    public function createDataTable($tatejiku, $yokojiku, $results)
    {
        $rows = array();
        $columns = array();
        foreach ($results as $key => $result) {

            //縦軸に一致する行データIndexの取得
            $rowIndex = enigmaController::getMatchIndex($tatejiku, $rows, $result);
            if ($rowIndex === null) {
                //取得できない場合は新規追加
                $row = enigmaController::createData($tatejiku, $result);
                $row["cells"] = array();
                $rows[] = $row;
                $rowIndex = count($rows) - 1;
            }

            //横軸に一致するセルデータを取得
            $cellIndex = enigmaController::getMatchIndex($yokojiku, $rows[$rowIndex]["cells"], $result);
            if ($cellIndex === null) {
                //取得できない場合は新規追加
                $cell = enigmaController::createData($yokojiku, $result);
                $rows[$rowIndex]["cells"][] = $cell;
            }

            //横軸に一致する列データIndexの取得
            $columnIndex = enigmaController::getMatchIndex($yokojiku, $columns, $result);
            if ($columnIndex === null) {
                //取得できない場合は新規追加
                $column = enigmaController::createData($yokojiku, $result);
                $columns[] = $column;
            }
        }

        return array(
            "columns"=>$columns,
            "rows"=>$rows
        );
    }

    /**
     * マッチ項目が全て一致するデータをリストから検索しリスト内のインデックスを返す
     */
    public function getMatchIndex($matchItems, $list, $checkItems)
    {
        foreach ($list as $key => &$value) {
            if (enigmaController::isMatch($matchItems, $value, $checkItems)) {
                return $key;
            }
        }
        return null;
    }

    /**
     * 全ての値が一致するか？
     */
    public function isMatch($list, $values1, $values2)
    {
        $i = 0;
        foreach ($list as $key => $value) {
            if ($values1[$value] === $values2->{$value}) {
                $i++;
            } else {
                return false;
            }
        }
        return $i === count($list) ? true : false;
    }

    /**
     * 連想配列のオブジェクトを作成する
     */
    public function createData($matchItems, $values)
    {
        $items = array();
        foreach ($matchItems as $key => $value) {
            $items = $items + array($value => $values->{$value});
        }
        return $items;
    }
}
