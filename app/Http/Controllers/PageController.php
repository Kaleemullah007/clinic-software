<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->authorizeResource(Page::class);
        $this->middleware(['auth','avoid-back-history']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Page::latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status', function (Page $p) {
                    return $p->status
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function (Page $p) {
                    $edit = auth()->user()->can('update', $p)
                        ? '<a href="' . route('pages.edit', $p->id) . '" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>'
                        : '';
                    $del = auth()->user()->can('delete', $p)
                        ? '<form action="' . route('pages.destroy', $p->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Delete?\')">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                           </form>'
                        : '';
                    return '<div class="d-flex gap-1">' . $edit . $del . '</div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.pages.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::get();

        return view('admin.pages.create',compact('categories'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function edit(Page $page)
    {
       $categories = Category::all();
        return view('admin.pages.edit',compact('page','categories'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePageRequest $request)
    {
        Page::create($request->validated());
        return redirect()->back()->with('message','Added new page successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function show(Page $page)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePageRequest  $request
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePageRequest $request, Page $page)
    {
        $data = $request->validated();
        Page::whereId($page->id)->update($data);
        return redirect()->back()->with('message','update page successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->back()->with('message','Deleted page successfully.');
    }


    public function recordsQuery($request)
    {
        // withoutGlobalScopes()->
        $expenses = Page::query();
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
        $expenses_html = view('admin.pages.ajax-pages',compact('expenses'))->render();
        $pagination_html = view('pages.pagination',compact('expenses'))->render();
        return response()->json(['html'=>$expenses_html,'phtml'=>$pagination_html]);
    }

}
