@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex">
        <div id="sidebar_left">
            <div class="card border border-light2">
                <div class="card-header bg-primary text-light">ログインユーザー</div>
                <div class="card-body">
                    <p class="card-text">
                        <div class="d-flex align-items-center mt-2 mb-3">
                            <img class="rounded-circle" src="{{ $user_info['image'] }}" alt="ユーザー画像" width="49" height="49">
                            <div class="ml-2">
                                <p class="mt-1 mb-0"><a class="d-block text-main font-weight-bold" target="_blank" href="https://twitter.com/{{ $user_info['screen_name'] }}">{{ $user_info['name'] }}</a></p>
                                <p class="m-0 text-main">{{ '＠' . $user_info['screen_name'] }}</p>
                            </div>
                        </div>
                </div>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">An item</li>
                <li class="list-group-item">A second item</li>
                <li class="list-group-item">A third item</li>
                <li class="list-group-item">A fourth item</li>
                <li class="list-group-item">And a fifth one</li>
            </ul>
        </div>
        <div class="tweet_list">
            @foreach ($data_orthopedy as $key1 => $val1)
                <div class="container">
                    <div id="{{ $val1['tweet_id'] }}" class="card border border-light2 rounded-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="far fa-clock text-main"></i><span class="mt-05 ml-1 text-main">{{ $val1['date'] }}</span>
                            </div>
                        <div class="d-flex align-items-center mt-2 mb-3">
                            <img class="rounded-circle" src="{{ $val1['user_img_path'] }}" alt="ユーザー画像" width="49" height="49">
                            <div class="ml-2">
                                <p class="mt-1 mb-0"><a class="d-block text-main font-weight-bold" target="_blank" href="https://twitter.com/{{ $val1['screen_name'] }}">{{ $val1['user_name'] }}</a></p>
                                <p class="m-0 text-main">{{ '＠' . $val1['screen_name'] }}</p>
                            </div>
                        </div>

                        @for ($i = 1; $i <= 4; $i++)
                            @if ($val1['tweet_img' . $i] !== "")
                                <img src="{{ $val1['tweet_img' . $i] }}" alt="" width="200" height="200">
                            @endif
                        @endfor

                        <p>{!! nl2br(e($val1['full_text'])) !!}</p>

                        <div class="d-flex">
                            <div class="d-flex align-items-center mr-2"><img class="mr-1" src="{{ asset('images/retweet.svg') }}" alt="" width="18.75" height="18.75">{{ $val1['retweet_count'] }}</div>
                            <div class="d-flex align-items-center"><img class="mr-1" src="{{ asset('images/favorite.svg') }}" alt="" width="18.75" height="18.75">{{ $val1['favorite_count'] }}</div>
                        </div>

                        @if (isset($val1['expanded_url']))
                            <a class="card-link" href="{{ $val1['expanded_url'] }}">{{ $val1['expanded_url'] }}</a>
                        @endif
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
        <aside id="sidebar_right">
            <div id="sidebar_right__wrap">
                <div class="mb-3">
                    <form action="">
                        <input type="text" class="form-control rounded-start" id="exampleFormControlInput1" placeholder="キーワード検索">
                    </form>
                </div>
                <div class="card border border-light2">
                    <div class="card-header bg-primary text-white">最近いいねしたユーザー</div>
                    <div class="card-body">
                        <p class="card-text">
                            @for ($i = 0; $i < 3; $i++)
                                <div class="d-flex align-items-center mt-2 mb-3">
                                    <img class="rounded-circle" src="{{ $data_name[$i]['user_img_path'] }}" alt="ユーザー画像" width="49" height="49">
                                    <div class="ml-2">
                                        <p class="mt-1 mb-0"><a class="d-block text-main font-weight-bold" target="_blank" href="https://twitter.com/{{ $data_name[$i]['screen_name'] }}">{{ $data_name[$i]['user_name'] }}</a></p>
                                        <p class="m-0 text-main">{{ '＠' . $data_name[$i]['screen_name'] }}</p>
                                    </div>
                                </div>
                                @if ($i != 2)
                                    <hr>
                                @endif
                            @endfor
                        </p>
                    </div>
                </div>
                <div class="card border border-light2">
                    <div class="card-header bg-primary text-white">いいねしたユーザーTOP3</div>
                    <div class="card-body">
                        <p class="card-text">
                            @for ($j = 0; $j < 3; $j++)
                                <div class="d-flex align-items-center mt-2 mb-3">
                                    <img class="rounded-circle" src="{{ $ranking[$j]['user_img_path'] }}" alt="ユーザー画像" width="49" height="49">
                                    <div class="ml-2">
                                        <p class="mt-1 mb-0"><a class="d-block text-main font-weight-bold" target="_blank" href="https://twitter.com/{{ $ranking[$j]['screen_name'] }}">{{ $ranking[$j]['user_name'] }}</a></p>
                                        <p class="m-0 text-main">{{ '＠' . $ranking[$j]['screen_name'] }}</p>
                                    </div>
                                </div>
                                @if ($j != 2)
                                    <hr>
                                @endif
                            @endfor
                        </p>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
