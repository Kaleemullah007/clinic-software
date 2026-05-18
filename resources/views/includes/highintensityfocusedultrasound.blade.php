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
                    <h1 class="fw-bold text-orange {{$page['heading_class'] }}">{{ $page['heading'] ?? 'High-intensity focused ultrasound (HIFU)' }}</h1>
                </div>
                <div class="col-lg-6">
                    <span class="text-orange fs-1 fw-bold {{$page['about']['heading_class'] }}">{{ $page['about']['heading'] ?? 'About' }}</span><br>
                    <p class="h4 text-indent-css">
                        {!! $page['about']['description'] ??
                            "High-intensity focused ultrasound (HIFU) is a relatively new cosmetic treatment for skin tightening that some consider a noninvasive and painless replacement for face lifts. It uses ultrasound energy to encourage the production of collagen, which results in firmer skin.</br>
                            We highly recommend pairing the treatment with regular exercise and a healthy diet for best results. </br>
                            HIFU will need to be repeated 6 to 8 weeks apart after the initial treatment. The target area and size of the unwanted fat pockets will help determine how many treatments you will need. </br>
                            HIFU is suitable for people aged approximately 25+ years with mild to moderate skin laxity or sagging. The device uses the modern technology of high intensity focused ultrasonic waves (HIFU). It produces excellent results in face, chin and neck slimming and removing buccal face fat tissues." !!}
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
                                <h3 class="card-title">{{$image['heading']??'High-intensity focused ultrasound (HIFU)'}}</h3>
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