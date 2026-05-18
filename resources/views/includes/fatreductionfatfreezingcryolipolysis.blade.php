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
                    <h1 class="fw-bold text-orange {{$page['heading_class'] }}">{{ $page['heading'] ?? 'Fat Reduction / Fat Freezing / Cryolipolysis (Clatuu Laser)' }}</h1>
                </div>
                <div class="col-lg-6">
                    <span class="text-orange fs-1 fw-bold {{$page['about']['heading_class'] }}">{{ $page['about']['heading'] ?? 'About' }}</span><br>
                    <p class="h4 text-indent-css">
                        {!! $page['about']['description'] ??
                            "Clatuu Alpha is a non-invasive treatment that freezes and destroys fat cells without causing damage to the skin and other tissues. Clatuu is a treatment technology that offers a more modern and effective fat freezing technique.</br>
                Clatuu is an alternative method to traditional liposuction. It is non-surgical. The process of cryogenic lipolysis involves freezing fat cells. The temperature that is used in Clatuu freezes the fat to a degree that allows for the cells to be killed without harming the skin surrounding it.</br>
                CLATUU is the latest non-surgical body-sculpting device offering suitable patients a good alternative to surgical fat reduction. CLATUU permanently destroys fat through a process known as cryolipolysis, or fat freezing.</br>
                Whilst some people see instant results, fat freezing is not an instant, one stop treatment. Unlike liposuction, your body has to naturally dispose of its own fat cells, and this can take up to 12 weeks after treatment, so you will see more changes as time goes on.</br></br>
                Areas treatable include: double chins, inner and outer thighs, knees, love handles, tummy, flanks, arms, buttocks and back." !!}
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
                                <h3 class="card-title">{{$image['heading']??'Fat Reduction / Fat Freezing / Cryolipolysis'}}</h3>
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