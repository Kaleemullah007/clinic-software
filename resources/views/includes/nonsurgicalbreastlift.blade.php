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
                    <h1 class="fw-bold text-orange {{$page['heading_class'] }}">{{ $page['heading'] ?? 'Non Surgical Breast Lift (Thread Lift)' }}</h1>
                </div>
                <div class="col-lg-6">
                    <span class="text-orange fs-1 fw-bold {{$page['about']['heading_class'] }}">{{ $page['about']['heading'] ?? 'About' }}</span><br>
                    <p class="h4 text-indent-css">
                        {!! $page['about']['description'] ??
                            "During the non surgical breast lift, the cosmetic physicians insert Long/Cog threads into the skin through tiny incisions. These threads attach to the skin tissue and are then pulled to lift and smooth the skin. Patients remain fully conscious during the procedure.</br>
                The threads are then secured together and pulled upward toward collarbone to lift the breasts. The procedure is noninvasive breast lift, with results lasting up to 2 years.</br>
                Similar to a cheek or jowl lift, a thread lift for the breast involves placing threads into the breast to lift up fallen tissue. It gives fantastic results for fallen breasts. Threads can also help tighten loose skin on the abdomen.  We can also use threads to sculpt the jaw line, reduce the appearance of lines, improve wrinkles and lift and tighten the face and neck." !!}
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
                                <h3 class="card-title">{{$image['heading']??'Non Surgical Breast Lift (Thread Lift)'}}</h3>
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