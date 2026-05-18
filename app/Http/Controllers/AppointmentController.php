<?php

namespace App\Http\Controllers;

use App\Enums\RoleName;
use App\Models\Appointment;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Mail\GenericForm;
use App\Models\AppointmentService;
use App\Models\BusinessHour;
use App\Models\Category;
use App\Models\Clinic;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Carbon\CarbonPeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;
class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->authorizeResource(Appointment::class);
        $this->middleware(['auth','avoid-back-history']);
    }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user  = auth()->user();
            $query = Appointment::with(['appointmentService', 'doctor:id,name'])->latest('date');

            if (!$user->isSuperAdmin() && $user->role === 'doctor') {
                $query->where('doctor_id', $user->id);
            }
            // Custom filters from filter bar
            if ($v = $request->input('filter_date'))    $query->whereDate('date', $v);
            if ($v = $request->input('filter_patient')) $query->where('user_id', $v);
            if ($request->filled('filter_paid')) {
                $request->input('filter_paid') === '1'
                    ? $query->where('is_paid', 'paid')
                    : $query->where('is_paid', '!=', 'paid');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('patient_col', fn (Appointment $a) =>
                    '<div class="fw-semibold">' . e($a->name) . '</div>'
                    . '<small class="text-muted">' . e($a->phone ?? '') . '</small>'
                )
                ->addColumn('doctor_col', fn (Appointment $a) =>
                    $a->doctor ? e($a->doctor->name) : '<span class="text-muted">—</span>'
                )
                ->addColumn('service_col', function (Appointment $a) {
                    $services = $a->appointmentService;
                    if (!$services || $services->isEmpty()) return '<span class="text-muted">—</span>';
                    return $services->map(fn ($s) =>
                        '<span class="badge bg-light text-dark border">' . e($s->service_name) . '</span>'
                    )->implode(' ');
                })
                ->addColumn('amount_col', fn (Appointment $a) =>
                    'PKR ' . number_format($a->subtotal_discounted_price_after_discount ?? 0, 0)
                )
                ->addColumn('paid_col', fn (Appointment $a) =>
                    $a->is_paid === 'paid'
                        ? '<span class="badge bg-success">Paid</span>'
                        : '<span class="badge bg-warning text-dark">Unpaid</span>'
                )
                ->addColumn('action', function (Appointment $a) {
                    $view = '<a href="' . route('appointments.show', $a->id) . '" class="btn btn-sm btn-outline-secondary me-1" title="View Receipt">
                                <i class="bi bi-eye"></i></a>';
                    $addProducts = '<a href="' . route('appointment-products.create') . '?appointment_id=' . $a->id . '" class="btn btn-sm btn-outline-primary me-1" title="Add Products">
                                <i class="bi bi-bag-plus"></i></a>';
                    $prescription = '<button type="button" class="btn btn-sm btn-outline-info me-1" title="Add Prescription"
                                data-bs-toggle="modal" data-bs-target="#prescriptionModal"
                                data-appointment-id="' . $a->id . '"
                                data-user-id="' . $a->user_id . '">
                                <i class="bi bi-capsule"></i></button>';
                    $edit = auth()->user()->can('update', $a)
                        ? '<a href="' . route('appointments.edit', $a->id) . '" class="btn btn-sm btn-outline-theme me-1" title="Edit">
                              <i class="bi bi-pencil-square"></i></a>'
                        : '';
                    $del = auth()->user()->can('delete', $a)
                        ? '<form action="' . route('appointments.destroy', $a->id) . '" method="POST" class="d-inline"
                               onsubmit="return confirm(\'Delete?\')">
                               ' . csrf_field() . method_field('DELETE') . '
                               <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                           </form>'
                        : '';
                    return $view . $addProducts . $prescription . $edit . $del;
                })
                ->filterColumn('patient_col', fn ($q, $k) =>
                    $q->where(fn ($s) =>
                        $s->where('name', 'like', "%{$k}%")->orWhere('phone', 'like', "%{$k}%")
                    )
                )
                ->rawColumns(['patient_col', 'doctor_col', 'service_col', 'paid_col', 'action'])
                ->make(true);
        }

        $availables = $this->getTimeSlots();
        $services   = Category::get();
        $patients   = User::withCount('appointments')->where('role', 'patient')->get();

        return view('admin.appointments.index', compact('availables', 'services', 'patients'));
    }

    public function UpdatePatientHistory(Request $request){


        $patient_id = $request->patientid;
        $search = $request->search;
        $status = $request->status;
        $date = $request->daterange;



        $appointments = new Appointment();
        if(!is_null($patient_id) && $patient_id != 'Select Patient'){
            $appointments= $appointments->where('user_id',$patient_id);
        }
        if(!is_null($search)){
            $appointments= $appointments->where(function($q) use($search){
                $q->orWhere('name','like','%'.$search.'%')
                ->orWhere('phone','like','%'.$search.'%');});
        }

        if(!is_null($status) && $status != 3 && $status != 'Select Status' ){

            $appointments= $appointments->where('is_paid',$status);
        }

        if(!is_null($date)){
            $appointments= $appointments->where('date',$date);
        }




        $appointments=$appointments->latest()->paginate(config('services.per_page',10));
        $availables = $this->getTimeSlots();
        $services = Category::get();
        $html =  view('admin.appointments.ajax-appointments',compact('appointments','availables','services'))->render();
        return response()->json(['html'=>$html]);


    }


    /**
     * get available timeslots .
     *
     * @return \Illuminate\Http\Response
     */
    public function getTimeSlots($date=null){
        $availables = array();
        // $days = CarbonPeriod::create(now(),now()->addDays(6));
        if($date == null)
        $date=date('Y-m-d');
        $d    = new DateTime($date);
        $currentDay  =   $d->format('l');

        // foreach($days as $day) {
            // $currentDay = $day->format('l');
        //    dd($currentDay,$date);
            $time_slots = BusinessHour::where('day', $currentDay)->where('is_day',1)->first();

            if($time_slots != null)
            $time_slots = $time_slots->timeSlots;
            else
            $time_slots = array();
          ;
            $booked_appointments = Appointment::where('date', $date)->pluck('time')->toArray();
            $availables = array_diff($time_slots, $booked_appointments);
            // break;
        // }

        return $availables;

    }



    public function time_slots(Request $request){
        $date = $request->date;
        $availables = $this->getTimeSlots($date);
        $d    = new DateTime($date);
        $currentDay  =   $d->format('l');

        $time_slots = BusinessHour::where('day', $currentDay)->where('is_day',1)->first();
        $html = view('admin.appointments.slots',compact('availables','time_slots'))->render();
        return response()->json(['slots'=>$html]);
    }

    public function getcategory(Request $request){

        $term = request('term');
        if(!empty($term)){
            $appoinmtents = Appointment::where(function($q) use($term){
                $q->orWhere('name','like','%'.$term.'%')
                ->orWhere('phone','like','%'.$term.'%');})
                ->groupBy('name')
                ->groupBy('phone')
                ->get();
        }else{
            $appoinmtents = Appointment::get();
        }

            $appoinmtentsData = array();
            foreach($appoinmtents as $appointment){
                $data['id'] = $appointment->id;
                $data['value'] = $appointment->phone.' '.$appointment->name;
                $data['name'] = $appointment->name;
                $data['email'] = $appointment->email;
                $data['phone'] = $appointment->phone;
                $data['doctor_id'] = $appointment->doctor_id;
                $data['clinic_id'] = $appointment->clinic_id;
                array_push($appoinmtentsData, $data);
            }

        return response()->json($appoinmtentsData);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $services = Category::get();
        $clinics  = Clinic::get();
        $doctors = User::where('role','doctor')->get();

        return view('admin.appointments.create',compact('services','clinics','doctors'));
    }

    function getInvoiceFields(){
         $months = config('Invoice');
        $month =  ltrim(date('m'), '0');
        $year  = date('Y');
        
        //$series  =  $months[$month].$year;
        $series  =  $months[ltrim($month, '0')] . $year;
        $serial_number=  (Appointment::where('serial',$series)->max('serial_number') ?? 0) + 1;
        $serial_series = $series.'-'.$serial_number;
        return [$series,$serial_number,$serial_series];

       }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAppointmentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAppointmentRequest $request)
    {
        $data  = $request->validated();
        


        $discount = $request->discount??0;
        $paid_amount = $request->paid_amount??0;

        $service_id = array_filter($request->service_id);
        $serviceIds = collect($service_id)->pluck('service_id');

        $DBCategories = Category::find($serviceIds)
        ->keyBy('id');
        $subtotal = collect($request->service_id)->reduce(function($carry,$item){
            return $carry + $item['price'];
        },0);
        $service_total = $subtotal - $discount;

        $actual_service_price = 0;
        $appointment_services = array();
        foreach ($service_id as $index => $services_array) {
            if(!isset($DBCategories[$services_array['service_id']]))
            continue;
            $temp = array();
            $temp['name'] = $DBCategories[$services_array['service_id']]->name;
            $temp['service_id']   = $DBCategories[$services_array['service_id']]->id;
            $temp['appointment_id']   = 0;

            $actual_service_price += $DBCategories[$services_array['service_id']]->price;
            $temp['price']   = $DBCategories[$services_array['service_id']]->price;
            $temp['discounted_price']   = $services_array['price'];
            $temp['discount']   = $temp['price']- $temp['discounted_price'];
            $temp['created_at'] = now();
            $temp['updated_at'] = now();
            $appointment_services[] = $temp;
        }

        $cost_total = $actual_service_price - $discount;
        list($series,$serial_number,$serial_series) = $this->getInvoiceFields();

        // dd($serviceIds,$subtotal,$appointment_services,$series,$serial_number,$serial_series);
        $data['serial'] = $series;
        $data['serial_series'] = $serial_series;
        $data['serial_number'] = $serial_number;



        $data['price'] = $actual_service_price;
        $data['discounted_price'] = $subtotal;

        $data['subtotal_price'] = $actual_service_price;
        $data['subtotal_discounted_price'] = $subtotal;

        $data['subtotal_price_after_discount'] = $actual_service_price-$discount;
        $data['subtotal_discounted_price_after_discount'] = $subtotal-$discount;

        $data['paid_amount'] = $paid_amount;
        $data['remaining_amount'] = $subtotal-$discount-$paid_amount;


        unset($data['service_id']);
        $user = [
            'name' => $data['name'],
            'role' => 'patient',
            'email'=>$data['email'],
            'phone'=>$data['phone'],
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];


       $checkUser = User::where(function($q) use($data){
            $q->orWhere('email',$data['email'])
            ->orWhere('phone',$data['phone']);})
            ->whereNotNull('email')
            ->first();

            if(is_null($checkUser)){
                $user = User::updateOrcreate(['phone'=>$data['phone']],$user);
                $userObject = $user;
                $user->roles()->sync(Role::where('name', RoleName::PATIENT->value)->first());
                $user  = $userObject;
            }
            else{
                if(!empty($checkUser->email)){
                    unset($user['email']);
                }
                if(!empty($checkUser->phone)){
                    unset($user['phone']);
                }
                // dd($checkUser->id);

                 User::where('id',$checkUser->id)->update($user);
                 $user = $checkUser;
            }


        $data['user_id'] = $user->id;
        $data['is_paid'] = (strtolower($data['is_paid'])=='on'  || $data['is_paid']==1 )?true:false;
        $data['appointment_status'] = $data['appointment_status']??'';
        $appointment = Appointment::create($data);

        // dd($appointment);
        $appointment_services= array_map(function($item) use($appointment) {
            $item['appointment_id'] =  $appointment->id;
            return $item;
       },$appointment_services);

       AppointmentService::insert($appointment_services);
        $request->session()->flash('message','Appointment Created Sucessfully');
        return to_route('appointments.create');



    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function show(Appointment $appointment)
    {
        $appointment->load([
            'appointmentService',
            'products.product',
            'products.variation',
            'patient',
        ]);
        return view('admin.appointments.view-sale', compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function edit(Appointment $appointment)
    {
        $services = Category::get();
        $clinics  = Clinic::get();
        $doctors = User::where('role','doctor')->get();
        return view('admin.appointments.edit',compact('services','clinics','doctors','appointment'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAppointmentRequest  $request
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {


        $data  = $request->validated();



        $discount = $request->discount??0;
        $paid_amount = $request->paid_amount??0;

        $service_id = array_filter($request->service_id);
        $serviceIds = collect($service_id)->pluck('service_id');
        try
        {

        DB::beginTransaction();
        $DBCategories = Category::find($serviceIds)
        ->keyBy('id');
        $subtotal = collect($request->service_id)->reduce(function($carry,$item){
            return $carry + $item['price'];
        },0);
        $service_total = $subtotal - $discount;

        $actual_service_price = 0;
        $appointment_services = array();
        foreach ($service_id as $index => $services_array) {
            if(!isset($DBCategories[$services_array['service_id']]))
            continue;
            $temp = array();
            $temp['name'] = $DBCategories[$services_array['service_id']]->name;
            $temp['service_id']   = $DBCategories[$services_array['service_id']]->id;
            $temp['appointment_id']   = 0;

            $actual_service_price += $DBCategories[$services_array['service_id']]->price;
            $temp['price']   = $DBCategories[$services_array['service_id']]->price;
            $temp['discounted_price']   = $services_array['price'];
            $temp['discount']   = $temp['price']- $temp['discounted_price'];
            $temp['created_at'] = now();
            $temp['updated_at'] = now();
            $appointment_services[] = $temp;
        }

        $cost_total = $actual_service_price - $discount;
        list($series,$serial_number,$serial_series) = $this->getInvoiceFields();

        // dd($serviceIds,$subtotal,$appointment_services,$series,$serial_number,$serial_series);
        $data['serial'] = $series;
        $data['serial_series'] = $serial_series;
        $data['serial_number'] = $serial_number;



        $data['price'] = $actual_service_price;
        $data['discounted_price'] = $subtotal;

        $data['subtotal_price'] = $actual_service_price;
        $data['subtotal_discounted_price'] = $subtotal;

        $data['subtotal_price_after_discount'] = $actual_service_price-$discount;
        $data['subtotal_discounted_price_after_discount'] = $subtotal-$discount;

        $data['paid_amount'] = $paid_amount;
        $data['remaining_amount'] = $subtotal-$discount-$paid_amount;


        unset($data['service_id']);
        $user = [
            'name' => $data['name'],
            'role' => 'patient',
            'email'=>$data['email'],
            'phone'=>$data['phone'],
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];


       $checkUser = User::where(function($q) use($data){
            $q->orWhere('email',$data['email'])
            ->orWhere('phone',$data['phone']);})
            ->whereNotNull('email')
            ->first();

            if(is_null($checkUser)){
                $user = User::updateOrcreate(['phone'=>$data['phone']],$user);
                $userObject = $user;
                $user->roles()->sync(Role::where('name', RoleName::PATIENT->value)->first());
                $user  = $userObject;
            }
            else{
                if(!empty($checkUser->email)){
                    unset($user['email']);
                }
                if(!empty($checkUser->phone)){
                    unset($user['phone']);
                }
                // dd($checkUser->id);

                 User::where('id',$checkUser->id)->update($user);
                 $user = $checkUser;
            }


        $data['user_id'] = $user->id;
        $data['is_paid'] = (strtolower($data['is_paid'])=='on'  || $data['is_paid']==1 )?true:false;
        $data['appointment_status'] = $data['appointment_status']??'';
        $oid = $data['id'];
        unset($data['id']);
        $appointment = Appointment::where('id',$oid)->update($data);
        // dd($appointment);
        $appointment_services= array_map(function($item) use($oid) {
            $item['appointment_id'] =  $oid;
            return $item;
       },$appointment_services);

       AppointmentService::where('appointment_id',$oid)->delete();
       AppointmentService::insert($appointment_services);
        $request->session()->flash('message','Appointment update Sucessfully');
        DB::commit();
        return to_route('appointments.edit',$oid);

    }
    catch(\Exception $e){
        DB::rollBack();
        $request->session()->flash('error', 'Appointment not updated, Try again.');
        return to_route('appointments.edit',$oid);
    }



    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Appointment $appointment)
    {
        //
    }


    public function recordsQuery($request)
    {
        // withoutGlobalScopes()->
        $expenses = Appointment::query();
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
        $expenses_html = view('admin.appointments.ajax-appointments',compact('expenses'))->render();
        $pagination_html = view('pages.pagination',compact('expenses'))->render();
        return response()->json(['html'=>$expenses_html,'phtml'=>$pagination_html]);
    }


            /**
     * Add the specified row from .
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function addNewRow(Request $request)
    {
        $new_row = $request->new_row;
        $totalrecords = $request->totalrecords;
        $services  = $request->services;
        $add_services  = $request->services;
        $services = Category::whereNotIn('id',array_values($services))->get();
        $html = view('admin.appointments.row',compact('new_row','totalrecords','services','add_services'))->render();
        return $html;

    }


           /**
     * Update Services.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function UpdateService(Request $request)
    {
        $new_row = $request->new_row;
        $totalrecords = $request->totalrecords;
        $services  = $request->services;
        $add_services  = $request->services;
        $services = Category::whereNotIn('id',array_values($services))->get();
        $html = view('admin.appointments.services_dropdown',compact('new_row','totalrecords','services','add_services'))->render();
        return $html;

    }



    public function generatePDF($id){
        $appointment = Appointment::with(['customer'])
        ->whereId($id)->first();

        $pdf = Pdf::loadView('admin.appointments.print', compact('appointment'));
        return $pdf->download($appointment->serial_series.'-'.$appointment->customer->name.'.pdf');
    }

}
