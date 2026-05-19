<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Support\Arr;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {


        $this->authorizeResource(Category::class);

        $this->middleware(['auth','avoid-back-history']);
    }


    public function index()
    {
        if (request()->ajax()) {
            $query = Category::withCount('appointmentServices');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('price_col', fn (Category $c) =>
                    (auth()->user()->currency ?? '') . ' ' . number_format($c->price, 2)
                )
                ->addColumn('total_services', fn (Category $c) =>
                    '<span class="badge rounded-pill"
                          style="background:#B1083C;font-size:.82rem;padding:4px 10px">'
                    . $c->appointment_services_count . '</span>'
                )
                ->addColumn('status_col', fn (Category $c) =>
                    '<input type="checkbox" data-toggle="toggle" data-size="sm"
                            data-onstyle="success" data-offstyle="danger"
                            data-onlabel="On" data-offlabel="Off"'
                    . ($c->status ? ' checked' : '') . '>'
                )
                ->addColumn('action', function (Category $c) {
                    $edit = '<a href="' . url('category/' . $c->id . '/edit') . '"
                                class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark me-1">
                                <i class="bi bi-pencil"></i></a>';
                    $del  = '<form action="' . route('category.destroy', $c->id) . '" method="POST"
                                   class="d-inline" onsubmit="return confirm(\'Delete this service?\')">'
                          . csrf_field() . method_field('DELETE')
                          . '<button class="btn border border-dark rounded-pill px-2 py-0 fs-6 link-dark">
                                <i class="bi bi-trash-fill"></i></button></form>';
                    return $edit . $del;
                })
                // Allow server-side ordering on the withCount virtual column
                ->orderColumn('total_services', function ($q, $order) {
                    $q->orderBy('appointment_services_count', $order);
                })
                ->rawColumns(['total_services', 'status_col', 'action'])
                ->make(true);
        }

        return view('admin.category.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $categories = Category::get();
        return view('admin.category.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = $request->validated();

        $category ['is_parent'] = $request->is_parent ?? 0;
        $category ['url'] = $request->url ?? '/';
         Category::create($category);
         $request->session()->flash('message','Service added Sucessfully');
        return to_route('category.create');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return view('admin.category.create',compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {

        $categories = Category::get();
        return view('admin.category.edit',compact('category','categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        Category::where('id',$category->id)->update(Arr::except($request->validated(), 'id'));
        $request->session()->flash('message','Service updated Sucessfully');
        return redirect()->route('category.edit', [$category->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();
        $msg = "Service Deleted successful! ";
        return to_route('category.index')->with('msg', $msg);
    }


    public function recordsQuery($request)
    {
        // withoutGlobalScopes()->
        $expenses = Category::query();
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
        $expenses_html = view('admin.category.ajax-category',compact('expenses'))->render();
        $pagination_html = view('pages.pagination',compact('expenses'))->render();
        return response()->json(['html'=>$expenses_html,'phtml'=>$pagination_html]);
    }
    public function getPrice(Category $category)
    {
        return response()->json(['price'=>$category->price],200);
    }

}
