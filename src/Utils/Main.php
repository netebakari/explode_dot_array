<?php

namespace netebakari\Utils;

class Main
{
    /**
     * 配列がリスト（添字が0から始まる連続した数値）であるかを判定するヘルパー関数。
     * @param array $arr チェックする配列
     * @return bool リストであればtrue、そうでなければfalse
     */
    private static function is_list(array $arr): bool
    {
        // 古いPHPでも動くようにチェックしておく
        if (is_array($arr) === false) {
            return false;
        }
        if ($arr === []) {
            return true;
        }
        return array_keys($arr) === range(0, count($arr) - 1);
    }

    /**
     * 2つの配列またはスカラー値を再帰的にマージする
     *
     * @param array $array1 マージ先の配列
     * @param array $array2 マージ元の配列
     * @return array マージ後の配列
     */
    private static function recursiveMerge(array $array1, array $array2): array
    {
        $merged = $array1;
        foreach ($array2 as $key => $value) {
            // キーがマージ先に存在しない場合は、単純に追加
            if (!isset($merged[$key])) {
                $merged[$key] = $value;
                continue;
            }

            // キーが両方に存在する場合のマージ処理
            $existing = $merged[$key];

            // Case 1: 両方とも配列の場合
            if (is_array($existing) && is_array($value)) {
                // 両方がリスト形式なら、単純に連結する
                if (self::is_list($existing) && self::is_list($value)) {
                    $merged[$key] = array_merge($existing, $value);
                } else {
                    // 少なくとも一方が連想配列なら、再帰的にマージする
                    $merged[$key] = self::recursiveMerge($existing, $value);
                }
            }
            // Case 2: 既存が配列で、新しい値がスカラーの場合
            else if (is_array($existing) && !is_array($value)) {
                $existing[] = $value; // スカラー値を既存の配列に追加
                $merged[$key] = $existing;
            }
            // Case 3: 既存がスカラーで、新しい値が配列の場合
            else if (!is_array($existing) && is_array($value)) {
                array_unshift($value, $existing); // 既存のスカラー値を新しい配列の先頭に追加
                $merged[$key] = $value;
            }
            // Case 4: 両方ともスカラーの場合
            else {
                $merged[$key] = [$existing, $value]; // 2つのスカラー値から新しい配列を作成
            }
        }
        return $merged;
    }

    /**
     * ドット記法のキーを持つ配列を配列に展開する。
     * ["a.b.c" => "value"] なら ["a" => ["b" => ["c" => "value"]]] のようになる。
     */
    public static function explode_dot_array(array $array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            // 値が配列の場合、再帰的にこの関数を呼び出して内部を展開する
            if (is_array($value)) {
                $value = self::explode_dot_array($value);
            }

            // 各ドット記法キーに対して、対応するネストした配列構造を作成する
            $keys = explode('.', $key);
            $nestedArray = [];
            $temp = &$nestedArray;

            while (count($keys) > 1) {
                $k = array_shift($keys);
                $temp[$k] = [];
                $temp = &$temp[$k];
            }
            $temp[array_shift($keys)] = $value;

            // 新しく作成した配列をメインの結果配列にマージ
            $result = self::recursiveMerge($result, $nestedArray);
        }
        return $result;
    }
}