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
   $prices = $page['table']??array();

@endphp

@section('title',$page['title'] ?? '')
@section('keywords',$page['keywords'] ?? '')
@section('author',$page['author'] ?? '')
@section('description',$page['description'] ?? '')

            <div class="row py-4 d-flex justify-content-around">
                <div class="col-12 text-center pb-3">
                    <h1 class="fw-bold text-orange {{$page['heading_class'] }}">{{ $page['heading'] ?? 'Laser Hair Removal' }}</h1>
                </div>
                <div class="col-lg-6">
                    <span class="text-orange fs-1 fw-bold {{$page['about']['heading_class'] }}">{{ $page['about']['heading'] ?? 'About' }}</span><br>
                    <p class="h4 text-indent-css">
                        {!! $page['about']['description'] ??
                            "Laser hair removal is the process of hair removal by means of exposure to pulses of laser light that destroy the hair follicle. It is being performed since many years.  Laser hair removal is widely practiced in clinics and is considered the safest and secure method to reduce the hair growth on the different parts of body.
                            The females are more concerned about their hair growth especially on face and on other parts of body as well. Laser hair removal has wonderful results to reduce hair growth.</br>
                            Laser Machine: - CANDELA N-D YAG LASER" !!}
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
                    <div class="table-responsive">
                        <table class="table table-bordered fs-4 mt-4">
                            <tbody>                            
                                <tr class="bg-theme text-light">
                                    <th colspan="3" class="text-center">{{ $prices['heading'] ?? '' }}</th>
                                </tr>
                                @foreach ($prices['data'] as $key => $price)
                                    @if ($loop->first)
                                        <tr class="bg-theme text-light">
                                            <th>{{ $price[0] ?? '' }}</th>
                                            <td>{{ $price[1] ?? '' }}</td>
                                            <td>{{ $price[2] ?? '' }}</td>

                                        </tr>
                                    @else
                                        <tr>
                                            <th>{{ $price[0] ?? '' }}</th>
                                            <td>{{ $price[1] ?? '' }}</td>
                                            <td>{{ $price[2] ?? '' }}</td>

                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-4 col-md-8 pt-3">
                    <div class="card border-css">
                        @foreach ($images as $key => $image)
                        @if ($loop->first)
                            <img class="{{$image['class']??'card-img-top'}}" src="{{$image['src']??''}}" alt="{{$image['alt']??''}}">
                            <div class="card-body text-center">
                                <h3 class="card-title">{{$image['heading']??'Hair Transplant'}}</h3>
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