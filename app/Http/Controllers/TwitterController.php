<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Abraham\TwitterOAuth\TwitterOAuth;
use \Exception;
use DateTime;

use App\User;

class TwitterController extends Controller
{

    public function top() {
        return view('welcome');
    }

    //----------------------------------------------------------------------
    //  index表示
    //----------------------------------------------------------------------
    public function index() {

        $user = Auth::user();

        if (is_null($user)) {
            return redirect()->route('top');
        }

        $consumer_key = config('twitter.consumer_key');
        $consumer_secret_key = config('twitter.consumer_secret');
        $access_token = config('twitter.access_token');
        $access_secret_token = config('twitter.access_token_secret');

        $connect = new TwitterOAuth($access_token, $access_secret_token, $consumer_key, $consumer_secret_key);
        // try {
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
        // } catch (Exception $e) {
        //     $hoge = $e->getCode();
        //     print_r($hoge);//89なら expired
        //     die;
        // }

        $list = json_decode(json_encode($list), true);
        $data_orthopedy = json_decode(TwitterController::createData($list), true);
        $data_name = TwitterController::classificationName($data_orthopedy);
        $ranking = TwitterController::getRanking3($data_orthopedy);

        // 利用者情報取得
        $user_info = TwitterController::getUserInfo();

        return view('index', compact('data_orthopedy', 'data_name', 'ranking', 'user_info'));
    }

    function getUserInfo() {

        $user_info = [];

        $user_info['id'] = TwitterController::getUserId();
        $user_info['name'] = TwitterController::getUserName();
        $user_info['screen_name'] = TwitterController::getUserScreenName();
        $user_info['image'] = TwitterController::getUserImage();

        return $user_info;
    }

    //----------------------------------------------------------------------
    // いいね情報を管理したJSONを生成するために整形
    //----------------------------------------------------------------------
    function orthopedyData($list) {
        $json = [];
        $user_id = NULL;
        $user_img_path = NULL;
        $link_url = NULL;
        $favorite_count = NULL;
        $retweet_count = NULL;

        for ($i = 0; $i < 4; $i++) {
            $tweet_img_path[$i] = "";
        }

        foreach ($list as $key1 => $val1) {
            $date = new DateTime($val1['created_at']);
            $full_text = $val1['full_text'];

            $user_id = $val1['user']['id'];

            $favorite_count = $val1['favorite_count'];
            $retweet_count = $val1['retweet_count'];

            // ユーザー画像取得
            $user_img_path = NULL;
            if (isset($val1['user']['profile_image_url'])) {
                $user_img_path = $val1['user']['profile_image_url'];
            }

            // ツイート内の画像を取得
            for ($i = 0; $i < 4; $i++) {
                $tweet_img_path[$i] = "";
            }
            if (isset($val1['extended_entities']['media'])) {
                foreach ($val1['extended_entities']['media'] as $key2 => $val2) {
                    $tweet_img_path[$key2] = $val2['media_url_https'];
                }
            }

            // ツイート内のリンクを取得
            $link_url = "";
            if (isset($val1['entities']['urls'])) {
                foreach ($val1['entities']['urls'] as $key2 => $val2) {
                    if (isset($val2['expanded_url'])) {
                        $link_url = $val2['expanded_url'];
                    }
                }
            }

            // JSON用の配列
            $json_val = array(
                "tweet_id" => $val1['id'],
                "date" => $date->format('Y/m/d H:i:s'),
                "user_id" => $val1['user']['id'],
                "user_img_path" => $user_img_path,
                "user_name" => $val1['user']['name'],
                "screen_name" => $val1['user']['screen_name'],
                "full_text" => $full_text,
                "expanded_url" => $link_url,
                "tweet_img1" => $tweet_img_path[0],
                "tweet_img2" => $tweet_img_path[1],
                "tweet_img3" => $tweet_img_path[2],
                "tweet_img4" => $tweet_img_path[3],
                "retweet_count" => $retweet_count,
                "favorite_count" => $favorite_count,
                "mytag1" => "",
                "mytag2" => ""
            );
            array_push($json, $json_val);
        }

        // JSONにエンコード
        $json = json_encode($json, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

        return $json;
    }

    //----------------------------------------------------------------------
    // いいね情報を管理したJSONを生成
    //----------------------------------------------------------------------
    function createData($list) {

        // 整形されたJSONを生成
        $json = TwitterController::orthopedyData($list);

        // ファイルに書き込み＆保存
        // $file_name = './assets/json/' . twitterAPI::getUserId() . '.json';
        // file_put_contents($file_name, $json);

        return $json;
    }

    //----------------------------------------------------------------------
    // 利用しているユーザーのIDを取得
    //----------------------------------------------------------------------
    function getUserId() {
        $user = Auth::user();
        $user_id = NULL;

        $consumer_key = config('twitter.consumer_key');
        $consumer_secret_key = config('twitter.consumer_secret');
        $access_token = config('twitter.access_token');
        $access_secret_token = config('twitter.access_token_secret');

        $connect = new TwitterOAuth($access_token, $access_secret_token, $consumer_key, $consumer_secret_key);
        $user = $connect->get(
            'users/show',
            // 取得するツイートの条件を配列で指定
            array(
                // ユーザー名（@は不要）
                'screen_name'       => $user->name,
            )
        );

        $user = json_decode(json_encode($user), true);
        return $user['id'];
    }

    //----------------------------------------------------------------------
    // 利用しているユーザーの名前取得
    //----------------------------------------------------------------------
    function getUserName() {
        $user = Auth::user();
        $user_id = NULL;

        $consumer_key = config('twitter.consumer_key');
        $consumer_secret_key = config('twitter.consumer_secret');
        $access_token = config('twitter.access_token');
        $access_secret_token = config('twitter.access_token_secret');

        $connect = new TwitterOAuth($access_token, $access_secret_token, $consumer_key, $consumer_secret_key);
        $user = $connect->get(
            'users/show',
            // 取得するツイートの条件を配列で指定
            array(
                // ユーザー名（@は不要）
                'screen_name'       => $user->name,
            )
        );

        $user = json_decode(json_encode($user), true);
        return $user['name'];
    }

    //----------------------------------------------------------------------
    // 利用しているユーザーの@以下取得
    //----------------------------------------------------------------------
    function getUserScreenName() {
        $user = Auth::user();
        $user_id = NULL;

        $consumer_key = config('twitter.consumer_key');
        $consumer_secret_key = config('twitter.consumer_secret');
        $access_token = config('twitter.access_token');
        $access_secret_token = config('twitter.access_token_secret');

        $connect = new TwitterOAuth($access_token, $access_secret_token, $consumer_key, $consumer_secret_key);
        $user = $connect->get(
            'users/show',
            // 取得するツイートの条件を配列で指定
            array(
                // ユーザー名（@は不要）
                'screen_name'       => $user->name,
            )
        );

        $user = json_decode(json_encode($user), true);
        return $user['screen_name'];
    }

    //----------------------------------------------------------------------
    // 利用しているユーザー画像を取得
    //----------------------------------------------------------------------
    function getUserImage() {
        $user = Auth::user();
        $user_id = NULL;

        $consumer_key = config('twitter.consumer_key');
        $consumer_secret_key = config('twitter.consumer_secret');
        $access_token = config('twitter.access_token');
        $access_secret_token = config('twitter.access_token_secret');

        $connect = new TwitterOAuth($access_token, $access_secret_token, $consumer_key, $consumer_secret_key);
        $user = $connect->get(
            'users/show',
            // 取得するツイートの条件を配列で指定
            array(
                // ユーザー名（@は不要）
                'screen_name'       => $user->name,
            )
        );

        $user = json_decode(json_encode($user), true);
        return $user['profile_image_url'];
    }

    //----------------------------------------------------------------------
    // 最近いいねしたユーザー名3人を取得
    //----------------------------------------------------------------------
    function classificationName($data) {


        $name_list = [];
        $unique = TwitterController::name_img_screen_dataCreate($data);

        // dd($unique);

        return $unique;
    }

    //----------------------------------------------------------------------
    // いいねランキング上位3人取得
    //----------------------------------------------------------------------
    function getRanking3($data) {

        $ranking3 = [];
        $ranking3_val = [];
        $return_data = [];
        $array = [];

        // dd($data);
        foreach ($data as $key1 => $val1) {
            $key = array_key_exists($val1['user_name'], $ranking3_val);
            if ($key) {
                $ranking3_val[$val1['user_name']]++;
            } else {
                $ranking3_val[$val1['user_name']] = 1;
            }
        }
        arsort($ranking3_val);
        // dd($ranking3_val);

        $i = 0;
        foreach ($ranking3_val as $key1 => $val1) {
            if ($i < 3) {
                $ranking3[$i] = $key1;
                $i++;
            }
        }

        // dd($ranking3);
        for ($i = 0; $i < 3; $i++) {
            foreach ($data as $key => $val) {
                if ($ranking3[$i] === $val['user_name']) {
                    $array = [
                        'user_name' => $ranking3[$i],
                        'user_img_path' => $val['user_img_path'],
                        'screen_name' => $val['screen_name'],
                    ];
                    array_push($return_data, $array);
                    break;
                }
            }
        }

        // dd($return_data);

        return $return_data;
    }

    //----------------------------------------------------------------------
    // 画像パス・名前・Twitter名だけのデータ形成
    //----------------------------------------------------------------------
    function name_img_screen_dataCreate($data) {

        $return_data = [];
        $unique = [];
        $name_list = [];

        // データを整形
        foreach ($data as $key => $val) {

            $array = [
                'user_id' => $val['user_id'],
                'user_img_path' => $val['user_img_path'],
                'user_name' => $val['user_name'],
                'screen_name' => $val['screen_name'],
            ];

            array_push($return_data, $array);
        }

        // 重複を削除
        foreach ($return_data as $name_img_screen_data) {
            if (!in_array($name_img_screen_data['user_id'], $name_list)) {
                $name_list[] = $name_img_screen_data['user_id'];
                $unique[] = $name_img_screen_data;
            }
        }

        return $unique;
    }
}
