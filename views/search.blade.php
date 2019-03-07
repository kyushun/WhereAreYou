@extends('layouts.search-form')
@section('pagename', $query)
@section('query', $query)
@section('error')
    @if (isset($query))
        <div class="row last-updated"><p class="col s12 offset-m2 m8 last-updated">{{ date("Y/m/d H:i:s", time()) }}更新</p></div>
    @endif

    @if (isset($query) && count($calendars) <= 0)
    <div class="card-panel red lighten-1">
        <p class="white-text">対象ユーザーが見つかりませんでした</p>
    </div>
    @endif

    @if ($exceedQueries === true)
    <div class="card-panel red lighten-1">
        <p class="white-text">一部の検索は省略されました<br />同時に検索できるのは{{ MAX_QUERIES }}人までです</p>
    </div>
    @endif
@endsection
@section('main')
    @for ($i = 0; $i < count($calendars); $i++)
        <?php $cal = $calendars[$i]; ?>
        <div class="username-section">
            {{ $cal->getName() }}
            @if ($cal->getId())
                <a target=”_blank” href="{{ 'https://a-rakumo.appspot.com/calendar#calendar/'.$cal->getId() }}"><i class="material-icons rakumo-icons">event_note</i></a>
            @endif
        </div>

        <div class="card round-card">
            <div class="card-content">
                @if ($numbers[$i] != null)
                    <div class="card-section">
                        <div class="phone-numbers">
                            @foreach ($numbers[$i] as $n)
                                <i class="small material-icons">contact_phone</i><span>{{ $n }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="card-section">
                    <div class="descript-section">現在の予定</div>
                    <hr class="section-divide" />
                    <?php $currentEvents = $cal->currentEvents(); ?>
                    @if (count($currentEvents) > 0)
                        @foreach ($currentEvents as $e)
                            <div class="event-content">
                                @if ($e->getAllDay() === true)
                                    <div class="time-frame tag-red">終日</div>
                                @else
                                    <div class="time-frame tag-red">～{{ date('H:i', $e->getEndTimeAsTime()) }}</div>
                                @endif
                                <div class="event-summary-wrapper">
                                    <a target=”_blank” href="{{ 'https://a-rakumo.appspot.com/calendar#event/google:'.$cal->getId().'/'.$e->getId() }}"><i class="material-icons rakumo-icons">event_note</i></a>
                                    <span class="event-summary">{{ $e->getSummary() }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <span class="event-summary no-events">現在の予定はありません</span>
                    @endif
                </div>

                <div class="card-section">
                    <div class="descript-section">今後の予定</div>
                    <hr class="section-divide" />
                    <?php $followEvents = $cal->followEvents(); ?>
                    @if (count($followEvents) > 0)
                        @foreach ($followEvents as $e)
                            <div class="event-content">
                                @if ($e->getAllDay() === true)
                                    <div class="time-frame tag-green">終日</div>
                                @else
                                <div class="time-frame tag-green">{{ date('H:i', $e->getStartTimeAsTime()) }}～{{ date('H:i', $e->getEndTimeAsTime()) }}</div>
                                @endif
                                <div class="event-summary-wrapper">
                                    <a target=”_blank” href="{{ 'https://a-rakumo.appspot.com/calendar#event/google:'.$cal->getId().'/'.$e->getId() }}"><i class="material-icons rakumo-icons">event_note</i></a>
                                    <span class="event-summary">{{ $e->getSummary() }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <span class="event-summary no-events">今後の予定はありません</span>
                    @endif
                </div>
            </div>
        </div>
    @endfor
@endsection