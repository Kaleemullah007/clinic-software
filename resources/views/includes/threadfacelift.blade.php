@php
    if(!isset($page_link))
    $page_link =request()->segment(2);
    $page = config('mubashir.pages')[$page_link]??null;

    if($page == null){
        header("Location: " . URL::to('/'));
        exit();
    }
    $details = $page['procedure']['details']??array();
    $buttons = $page['buttons']??array();
    $images = $page['images']??array();
@endphp
@section('title',$page['title'] ?? '')
@section('keywords',$page['keywords'] ?? '')
@section('author',$page['author'] ?? '')
@section('description',$page['description'] ?? '')


            <div class="row py-4 d-flex justify-content-around">
                <div class="col-12 text-center pb-3">
                    <h1 class="fw-bold text-orange {{$page['heading_class'] }}">{{ $page['heading'] ?? 'Thread Face Lift ( Long Threads / Cog Threads )' }}</h1>
                </div>
                <div class="col-lg-6">
                    <span class="text-orange fs-1 fw-bold {{$page['about']['heading_class'] }}">{{ $page['about']['heading'] ?? 'About' }}</span><br>
                    <p class="h4 text-indent-css">
                        {!! $page['about']['description'] ??
                            "A thread lift is a Non invasive cosmetic procedure that tightens the skin on the face. During a thread lift, an aesthetic physician uses threads to pull up portions of the skin to reduce the appearance of aging.</br>
                            If a person looses skin on face, it may causes to develop jowls or drooping cheeks. A thread lift is a cosmetic procedure that can help tighten loose facial skin. During a thread lift, the cosmetic physician Inserts and slightly pulls the threads and the skin up, which ultimately lift and tighten the face. Along with lifting the skin and making it appear tighter, thread lift can also stimulate the body to direct large amounts of collagen to the treated areas. This can also impact the appearance of skin aging as collagen can help reduce or delay trusted source the skin-aging process.</br>
                            Thread lifts are low risk and noninvasive and their recovery is often easier. There is virtually no risk of scarring. Due to the noninvasive nature of thread lifts, a person is at low risk of complications. A thread lift is alternative to a facelift. A person can drive home after the procedure and does not require care afterward. A person will also not require strong pain medication after a thread lift. This means that they can more easily return to their normal routine." !!}
                    </p>
                    <span class="text-orange fs-1 fw-bold {{$page['procedure']['heading_class'] }}">{{ $page['procedure']['heading'] ?? 'Procedure' }}</span><br>
                    <table class="table table-borderless fs-4">
                        <tbody>
                            @foreach ($details as $key => $detail)
                    <tr>
                        <td class=""><input class="radio-button" type="radio" checked></td>
                        <th>{{ $detail['name'] ?? '' }}</th>
                        <td>{{ $detail['value'] ?? '' }}</td>
                    </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @foreach ($buttons as $key => $button)
                        @if ($button['active'])
                            <div class="row text-theme pt-3 d-flex justify-content-center">
                                <div class="col-lg-6 col-md-6 col-12 pt-2">
                                    <a href="{{ $button['href'] }}" class="{{ $button['class'] }}">{{ $button['text'] }}</a>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="col-lg-4 col-md-8 pt-3">
                    <div class="card border-css">
                        @foreach ($images as $key => $image)
                        @if ($loop->first)
                            <img class="{{$image['class']??'card-img-top'}}" src="{{$image['src']??''}}" alt="{{$image['alt']??''}}">
                            <div class="card-body text-center">
                                <h3 class="card-title">{{$image['heading']??'Thread Face Lift ( Long Threads / Cog Threads )'}}</h3>
                                <h4 class="fw-bold text-orange "> {{$image['price']}}</h4>
                                @if ($image['is_discount'])
                                <h3 class="fw-bold text-orange strike"> {{$image['discounted_price']}} </h3>
                                @endif
                            </div>
                        @endif
                    @endforeach
                    </div>
                </div>
            </div>