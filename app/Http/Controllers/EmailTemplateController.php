<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Http\Requests\StoreEmailTemplateRequest;
use App\Http\Requests\UpdateEmailTemplateRequest;
use App\Models\User;

class EmailTemplateController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(EmailTemplate::class,User::class);
        $this->middleware(['auth','avoid-back-history']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        dd(userRights());
        //  dd(auth()->user()->clinics,auth()->user()->modules,auth()->user()->userPermissions);

        return view('admin.email.index');


        // $emails = Email::paginate(config('services.per_page',10));

        // if($emails->lastPage() >= request('page')){
        //     return view('admin.email.index',compact('emails'));
        // }
        // return to_route('admin.email.index',['page'=>$emails->lastPage()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.email.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmailTemplateRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailTemplate $emailTemplate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmailTemplate $emailTemplate)
    {

        return view('admin.email.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmailTemplateRequest $request, EmailTemplate $emailTemplate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailTemplate $emailTemplate)
    {
        //
    }

    public function recordsQuery($request)
    {
        // withoutGlobalScopes()->
        $expenses = EmailTemplate::query();
        $search = $request->search;
        $dates = $request->daterange;

        if($dates != null){
            list($start_date,$end_date) = explode('-',$dates);
           $start_date = changeDateFormat($start_date,'Y-m-d');
           $end_date = changeDateFormat($end_date,'Y-m-d');
            $expenses =$expenses->whereDate('date','>=',$start_date)
            ->whereDate('date','<=',$end_date);
        }
        if($search != null)
            $expenses = $expenses->where('name','like',"%".$search."%");
        return $expenses ;
    }

    // public function getExpenses(Request $request)
    // {
    //     $expenses = $this->recordsQuery($request)->get();
    //     $expenses_html = view('admin.email.ajax-email',compact('expenses'))->render();
    //     $pagination_html = view('pages.pagination',compact('expenses'))->render();
    //     return response()->json(['html'=>$expenses_html,'phtml'=>$pagination_html]);
    // }


}
