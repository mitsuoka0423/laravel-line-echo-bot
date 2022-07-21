# laravel-line-echo-bot

## はじめに

PHPのWebアプリケーションフレームワークの1つであるLaravelを利用した、LINE Botのサンプルコードです。  
オウム返しを実装しています。

## インストール

```bash
composer install
```

## 環境変数設定

```bash
cp .env.example .env
```

| 項目名 | 名前 | 参考URL |
| -- | -- | -- |
| `LINE_CHANNEL_ACCESS_TOKEN` | LINEチャネルアクセストークン | [LINE公式アカウントの作成 / LINE Botの初め方](https://zenn.dev/protoout/articles/16-line-bot-setup) |
| `LINE_CHANNEL_SECRET` | LINEチャネルシークレット | [LINE公式アカウントの作成 / LINE Botの初め方](https://zenn.dev/protoout/articles/16-line-bot-setup) |

## 実行

```bash
php artisan serve
```
