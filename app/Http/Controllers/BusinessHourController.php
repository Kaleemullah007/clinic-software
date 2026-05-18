<?php

namespace App\Http\Controllers;

use App\Models\BusinessHour;
use App\Http\Requests\StoreBusinessHourRequest;
use App\Http\Requests\UpdateBusinessHourRequest;
use Illuminate\Support\Arr;

class BusinessHourController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function __construct()
     {
         $this->authorizeResource(BusinessHour::class);
         $this->middleware(['auth','avoid-back-history']);
     }

    public function index()
    {
        $days = BusinessHour::where('user_id',auth()->id())->get();
        return view('admin.business-hours.index',compact('days'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBusinessHourRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBusinessHourRequest $request)
    {
        $data = array_values($request->all()['data']);
        BusinessHour::upsert($data,'day');
        return redirect()->back();

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BusinessHour  $businessHour
     * @return \Illuminate\Http\Response
     */
    public function show(BusinessHour $businessHour)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BusinessHour  $businessHour
     * @return \Illuminate\Http\Response
     */
    public function edit(BusinessHour $businessHour)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBusinessHourRequest  $request
     * @param  \App\Models\BusinessHour  $businessHour
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBusinessHourRequest $request, BusinessHour $businessHour)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BusinessHour  $businessHour
     * @return \Illuminate\Http\Response
     */
    public function destroy(BusinessHour $businessHour)
    {
        //
    }
}
