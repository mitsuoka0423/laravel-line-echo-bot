<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use Tapp\Airtable\Facades\AirtableFacade;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

$httpClient = new CurlHTTPClient($_ENV['LINE_CHANNEL_ACCESS_TOKEN']);
$bot = new LINEBot($httpClient, ['channelSecret' => $_ENV['LINE_CHANNEL_SECRET']]);

Route::post('/webhook', function (Request $request) use ($bot) {
    Log::debug($request);

    $signature = $request->header(HTTPHeader::LINE_SIGNATURE);
    if (empty($signature)) {
        return abort(400);
    }

    $events = $bot->parseEventRequest($request->getContent(), $signature);
    Log::debug(['$events' => $events]);

    collect($events)->each(function ($event) use ($bot) {
        if ($event instanceof TextMessage) {
            if ($event instanceof TextMessage) {
                if ($event->getText() === '会員カード') {
                    // 会員登録済みか確認するため、Airtableからデータを取得する
                    $member = Airtable::where('UserId', $event->getUserId())->get();

                    if ($member->isEmpty()) {
                        // Airtableに会員データがなければ、生成して登録する
                        $memberId = strval(rand(1000000000, 9999999999));
                        $member = Airtable::firstOrCreate([
                            'UserId' => $event->getUserId(),
                            'Name' => $bot->getProfile($event->getUserId())->getJSONDecodedBody()['displayName'],
                            'MemberId' => $memberId,
                        ]);
                        Log::debug('Member is created.');
                    } else {
                        // Airtableにデータがあれば、取得したデータを利用する
                        $memberId = $member->first()['fields']['MemberId'];
                    }
                    Log::debug($memberId);

                    return $bot->replyText($event->getReplyToken(), "会員IDは {$memberId} です！");
                } else {
                    return $bot->replyText($event->getReplyToken(), $event->getText());
                }
            }
        }
    });

    return 'ok!';
});
