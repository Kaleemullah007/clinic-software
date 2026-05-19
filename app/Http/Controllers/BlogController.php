<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->authorizeResource(Blog::class);
        $this->middleware(['auth','avoid-back-history']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Blog::latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status', function (Blog $b) {
                    return $b->status
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-secondary">Draft</span>';
                })
                ->addColumn('created_at_fmt', fn (Blog $b) =>
                    $b->created_at ? $b->created_at->format('d M Y') : '—'
                )
                ->addColumn('action', function (Blog $b) {
                    $edit = auth()->user()->can('update', $b)
                        ? '<a href="' . route('blogger.edit', $b->id) . '" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>'
                        : '';
                    $del = auth()->user()->can('delete', $b)
                        ? '<form action="' . route('blogger.destroy', $b->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Delete?\')">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                           </form>'
                        : '';
                    return '<div class="d-flex gap-1">' . $edit . $del . '</div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.blogs.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.blogs.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBlogRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBlogRequest $request)
    {
        // dd($request->all());
        Blog::create($request->validated());
        return redirect()->back()->with('message','Added new blog successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function show(Blog $blog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function edit(Blog $blogger)
    {

        $blog = $blogger;

        return view('admin.blogs.edit',compact('blog'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBlogRequest  $request
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBlogRequest $request, Blog $blog)
    {
        $data = $request->validated();
        Blog::whereId($blog->id)->update($data);
        return redirect()->back()->with('message','update blog successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blog $blog)
    {
        //
    }


    public function recordsQuery($request)
    {
        // withoutGlobalScopes()->
        $expenses = Blog::query();
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
        $expenses_html = view('admin.blog.ajax-blog',compact('expenses'))->render();
        $pagination_html = view('pages.pagination',compact('expenses'))->render();
        return response()->json(['html'=>$expenses_html,'phtml'=>$pagination_html]);
    }


}
