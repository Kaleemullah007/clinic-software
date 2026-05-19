<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function __construct()
     {
         $this->authorizeResource(Contact::class);
         $this->middleware(['auth','avoid-back-history']);
     }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Contact::latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('message_short', function (Contact $c) {
                    return e(Str::limit($c->message, 50));
                })
                ->addColumn('action', function (Contact $c) {
                    $view = auth()->user()->can('view', $c)
                        ? '<a href="' . route('contacts.show', $c->id) . '" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>'
                        : '';
                    $del = auth()->user()->can('delete', $c)
                        ? '<form action="' . route('contacts.destroy', $c->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Delete?\')">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                           </form>'
                        : '';
                    return '<div class="d-flex gap-1">' . $view . $del . '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.contact.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreContactRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreContactRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateContactRequest  $request
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContactRequest $request, Contact $contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact)
    {
        //
    }


    public function recordsQuery($request)
    {
        // withoutGlobalScopes()->
        $expenses = Contact::query();
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

    public function getExpenses(Request $request)
    {
        $expenses = $this->recordsQuery($request)->get();
        $expenses_html = view('admin.contact.ajax-contact',compact('expenses'))->render();
        $pagination_html = view('pages.pagination',compact('expenses'))->render();
        return response()->json(['html'=>$expenses_html,'phtml'=>$pagination_html]);
    }



}
