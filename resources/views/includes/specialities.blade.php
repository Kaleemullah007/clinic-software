@php
    if (!isset($page_link)) {
        //    $page_link = 'pricing'request()->segment(2);
        $page = config('mubashir.pages') ?? null;
    }

    $page = collect($page)->except(['melasma-pigmentation-skin-glow','acne-scar','hydra-facial','fillers','botox','non-surgical-face-lift']);

    $allpages =$page->toArray();
    // dd($allpages);
    $random_order = array_rand($page->toArray(),8);
    
   
@endphp

<div class="mx-3 my-1 text-theme fadeInn">
            <div class="row">
                @foreach ($random_order as  $randomKey)
                @php
                    $allpage = $allpages[$randomKey];
                    $images = $allpage['images'] ?? [];
                    // if(empty($images))
                    // dd($allpage);
                @endphp
                @foreach ($images as $key => $image)
                    <div class="col-lg-3 col-md-6 col-12 mt-5">
                        <div class="card border-css">
                            <a href="{{ isset($image['link']) ? route($image['link']) : '' }}"
                                class="{{ $image['linkclass'] ?? '' }}">
                                <img class="{{ $image['class'] ?? 'card-img-top' }}" style="height: 240px"
                                    src="{{ $image['src'] ?? '' }}" alt="{{ $image['alt'] ?? '' }}">
                            
                                <div class="card-body text-center">
                                    <h3 class="card-title">
                                        {{ substr($image['heading'], 0, 20) ?? '' }}{{ strlen($image['heading']) > 20 ? '...' : '' }}
                                    </h3>
                                    <h4 class="fw-bold text-orange "> {{ $image['price'] }}</h4>
                                    @if ($image['is_discount'])
                                        <h3 class="fw-bold text-orange strike"> {{ $image['discounted_price'] }} </h3>
                                    @endif
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            @endforeach
            </div>
        </div>