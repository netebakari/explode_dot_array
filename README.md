# explode_dot_array

## 概要
```php
["a.b.c" => 1]
```

ドット記法のこのような配列を展開して

```php
["a" => ["b" => ["c" => "value"]]]
```

このように変換する関数。

## テスト
```
docker compose run --rm test
```
