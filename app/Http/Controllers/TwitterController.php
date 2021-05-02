<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Abraham\TwitterOAuth\TwitterOAuth;
use \Exception;

use App\User;

class TwitterController extends Controller
{



    public function index() {
        $user = Auth::user();

        $consumer_key = config('twitter.consumer_key');
        $consumer_secret_key = config('twitter.consumer_secret');
        $access_token = config('twitter.access_token');
        $access_secret_token = config('twitter.access_token_secret');

        $connect = new TwitterOAuth($access_token, $access_secret_token, $consumer_key, $consumer_secret_key);
        try {
            $list = $connect->get(
                'favorites/list',
                // 取得するツイートの条件を配列で指定
                array(
                    // ユーザー名（@は不要）
                    'screen_name'       => $user->name,
                    // ツイート件数
                    'count'             => '200',
                    'tweet_mode'        => 'extended',
                )
            );
            dd($list);
        } catch (Exception $e) {
            $hoge = $e->getCode();
            dd($hoge);
            print_r($hoge);//89なら expired
            die;
        }

        $list = json_decode(json_encode($list), true);

        return view('welcome');
    }
}
