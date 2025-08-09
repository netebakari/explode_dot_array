<?php
namespace netebakari\Tests\Utils;

use PHPUnit\Framework\TestCase;
use netebakari\Utils\Main;

class MainTest extends TestCase
{
    /**
     * 何も変更を加えずにそのまま値が返ってくるパターン
     */
    public function testIdenticalCases(): void
    {
        $testData = [
            ["a" => 1, "b" => "B"],
            [],
            [0, 1, 2],
            [3, 4, 5],
            [6, 7, 8, "A", "B", "C"],
            [[]],
            [["a", "b"]],
            [[1, 2, 3]],
            [["key1" => "value1", "key2" => "value2"]],
            ["key1" => "value1", "key2" => "value2"],
            ["key1" => ["subkey1" => "value1", "subkey2" => "value2"]],
        ];

        foreach ($testData as $data) {
            $result = Main::explode_dot_array($data);
            $this->assertSame($data, $result, 'Data should remain unchanged');
        }
    }
    
    /**
     * シンプルなパターン
     */
    public function testSimpleCases(): void
    {
        $testData = [
            "深さ2" => [
                ["a.b" => "value"],
                ["a" => ["b" => "value"]]
            ],
            "深さ3" => [
                ["a.b.c" => "value"],
                ["a" => ["b" => ["c" => "value"]]]
            ],
            "配列を含む" => [
                ["a.b.c" => ["value1", "value2"]],
                ["a" => ["b" => ["c" => ["value1", "value2"]]]]
            ],
            "ドット記法と配列が混在している" => [
                ["a.b" => ["c.d" => "value1", "e.f" => "value2"]],
                ["a" => ["b" => ["c" => ["d" => "value1"], "e" => ["f" => "value2"]]]]
            ]
        ];

        foreach ($testData as $name => $data) {
            $result = Main::explode_dot_array($data[0]);
            $this->assertSame($data[1], $result, "Test case: $name");
        }
    }

    /**
     * 同じ階層に複数の要素があったらマージするパターン
     */
    public function testMergingCases(): void
    {
        $testData = [
            "同じ階層に要素が複数ある場合は連想配列にする" => [
                ["a.b.X" => "value1", "a.b.Y" => "value2"],
                ["a" => ["b" => ["X" => "value1", "Y" => "value2"]]]
            ],
            "まったく同じ階層に要素が複数ある場合は配列にする" => [
                ["a.b.X" => "P", "a.b" => ["X" => "Q"]],
                ["a" => ["b" => ["X" => ["P", "Q"]]]]
            ],
            "まったく同じ階層に要素が複数ある場合は配列にする。配列はマージする" => [
                ["a.b.X" => "P", "a.b" => ["X" => ["Q"]]],
                ["a" => ["b" => ["X" => ["P", "Q"]]]]
            ],
            "まったく同じ階層に要素が複数ある場合は配列にする。配列はマージする（上記の逆パターン）" => [
                ["a.b.X" => ["P"], "a.b" => ["X" => "Q"]],
                ["a" => ["b" => ["X" => ["P", "Q"]]]]
            ],
            "まったく同じ階層に要素が複数ある場合は配列にする。配列同士はマージする" => [
                ["a.b.X" => ["P", "Q"], "a.b" => ["X" => ["R", "S"]]],
                ["a" => ["b" => ["X" => ["P", "Q", "R", "S"]]]]
            ],
            "まったく同じ階層に要素が複数ある場合は配列にする。配列同士はマージする。3個あるパターン" => [
                ["a.b.X" => ["P", "Q"], "a.b" => ["X" => ["R", "S"]], "a" => ["b" => ["X" => "T"]]],
                ["a" => ["b" => ["X" => ["P", "Q", "R", "S", "T"]]]]
            ],
        ];

        foreach ($testData as $name => $data) {
            $result = Main::explode_dot_array($data[0]);
            $this->assertSame($data[1], $result, "Test case: $name");
        }
    }

}