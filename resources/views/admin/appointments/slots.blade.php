@if (count($availables) > 0)
@foreach ($availables as $available )

    <option value="{{$available}}">{{TwelveFormat($available,$time_slots->step??3)}}</option>

@endforeach

@else
<option value="">No Slot Available</option>
@endif
