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
                    <h1 class="fw-bold text-orange {{$page['heading_class'] }}">{{ $page['heading'] ?? 'PRP for Face + Micro Needling & Mesotherapy' }}</h1>
                </div>
                <div class="col-lg-6">
                    <span class="text-orange fs-1 fw-bold {{$page['about']['heading_class'] }}">{{ $page['about']['heading'] ?? 'About' }}</span><br>
                    <p class="h4 text-indent-css">
                        {!! $page['about']['description'] ??
                            "If your problem is that your skin is looking very dull and lost that youthful glow that you once had or you have developed Acne Scars, then micro needling with platelet-rich plasma (PRP) therapy may be right for you. The procedure combines two therapies: micro needling, which stimulates collagen production, and PRP therapy, which uses a highly concentrated form of platelets from your own blood that contains immense regenerative and healing powers.</br>
                            We have found that by combining both therapies, patients receive much more dramatic effects. The benefits of PRP with micro needling are plenty – which is why more and more patients are signing up to see the phenomenal effects of this procedure for themselves. It has wonderful results for skin rejuvenation, skin glow, reduces pigmentation and to reduce acne scars. Serums are also added to get the maximum results." !!}
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
                                <h3 class="card-title">{{$image['heading']??'PRP for Face + Micro Needling & Mesotherapy'}}</h3>
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
