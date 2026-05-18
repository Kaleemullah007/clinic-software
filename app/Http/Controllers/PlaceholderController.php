<?php

namespace App\Http\Controllers;

use App\Models\PlaceHolder;
use App\Http\Requests\StorePlaceHolderRequest;
use App\Http\Requests\UpdatePlaceHolderRequest;

class PlaceHolderController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(PlaceHolder::class);
        $this->middleware(['auth','avoid-back-history']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {

        return view('admin.placeholder.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.placeholder.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePlaceHolderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePlaceHolderRequest $request)
    {
        PlaceHolder::create($request->validated());
        return redirect()->back()->with('message','Added new placeholder successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PlaceHolder  $placeHolder
     * @return \Illuminate\Http\Response
     */
    public function show(PlaceHolder $placeHolder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PlaceHolder  $placeHolder
     * @return \Illuminate\Http\Response
     */
    public function edit(PlaceHolder $placeHolder)
    {
        return view('admin.placeholder.edit',compact('placeHolder'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePlaceHolderRequest  $request
     * @param  \App\Models\PlaceHolder  $placeHolder
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePlaceHolderRequest $request, PlaceHolder $placeHolder)
    {
        $data = $request->validated();
        PlaceHolder::whereId($placeHolder->id)->update($data);
        return redirect()->back()->with('message','update Placeholder successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PlaceHolder  $placeHolder
     * @return \Illuminate\Http\Response
     */
    public function destroy(PlaceHolder $placeHolder)
    {
        $placeHolder->delete();
        return redirect()->back()->with('message','Deleted placeholder successfully.');
    }

    public function recordsQuery($request)
    {
        // withoutGlobalScopes()->
        $placeholders = Placeholder::query();
        $search = $request->search;
        $dates = $request->daterange;

        if($dates != null){
            list($start_date,$end_date) = explode('-',$dates);
           $start_date = changeDateFormat($start_date,'Y-m-d');
           $end_date = changeDateFormat($end_date,'Y-m-d');
            $placeholders =$placeholders->whereDate('created_at','>=',$start_date)
            ->whereDate('created_at','<=',$end_date);
        }
        if($search != null)
            $placeholders = $placeholders->where('name','like',"%".$search."%");
        return $placeholders ;
    }

}
