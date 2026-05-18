<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Run permissions first (creates roles)
        $this->call(PermissionSeeder::class);

        $this->seedClinic();
        $this->seedUsers();
        $this->seedCategories();
        $this->seedVendors();
        $this->seedProducts();
        $this->seedPurchaseRequests();
        $this->seedPurchases();
        $this->seedAppointments();
        $this->seedDoctorAgreements();
        $this->seedAppointmentProducts();
        $this->seedExpenses();
        $this->seedSalaries();
        $this->seedCallLogs();
        $this->seedConsentForms();

        $this->command->info('✓ Demo data seeded. Login: admin@clinic.com / password');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Clinic
    // ─────────────────────────────────────────────────────────────────────────
    private function seedClinic(): void
    {
        DB::table('clinics')->insert([
            ['id' => 1, 'name' => 'RK Aesthetic Clinic – Main Branch', 'phone' => '0300-1234567',
             'address' => '123 Mall Road, Lahore', 'support_email' => 'support@rkclinic.pk',
             'notification_email' => 'notify@rkclinic.pk', 'status' => 1,
             'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'RK Aesthetic Clinic – DHA Branch', 'phone' => '0300-7654321',
             'address' => 'DHA Phase 5, Lahore', 'support_email' => 'dha@rkclinic.pk',
             'notification_email' => 'notify@rkclinic.pk', 'status' => 1,
             'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Users (super-admin, doctor, receptionist, patient, pharmacist)
    // ─────────────────────────────────────────────────────────────────────────
    private function seedUsers(): void
    {
        $av = 'https://ui-avatars.com/api/?background=B1083C&color=fff&name=';

        // Use DB::table to bypass $fillable and write salary columns directly
        $users = [
            ['id'=>1,'name'=>'Super Admin',       'email'=>'admin@clinic.com',    'phone'=>'03001111111','role'=>'admin',  'status'=>1,'avatar'=>$av.'SA','salary_type'=>'fixed',     'salary_amount'=>80000,'joining_date'=>'2022-01-01','cnic'=>'35201-1234567-1','bank_account'=>'HBL-001111'],
            ['id'=>2,'name'=>'Dr. Mubashir Daha', 'email'=>'doctor@clinic.com',   'phone'=>'03002222222','role'=>'doctor', 'status'=>1,'avatar'=>$av.'MD','salary_type'=>'commission','salary_amount'=>0,    'joining_date'=>'2022-03-15','cnic'=>'35201-9876543-2','bank_account'=>'UBL-002222'],
            ['id'=>3,'name'=>'Sara Receptionist', 'email'=>'reception@clinic.com','phone'=>'03003333333','role'=>'admin',  'status'=>1,'avatar'=>$av.'SR','salary_type'=>'fixed',     'salary_amount'=>35000,'joining_date'=>'2023-01-01','cnic'=>'35201-1111111-3','bank_account'=>'MCB-003333'],
            ['id'=>4,'name'=>'Ahmed Pharmacist',  'email'=>'pharma@clinic.com',   'phone'=>'03004444444','role'=>'admin',  'status'=>1,'avatar'=>$av.'AP','salary_type'=>'fixed',     'salary_amount'=>40000,'joining_date'=>'2022-06-01','cnic'=>'35201-2222222-4','bank_account'=>'ABL-004444'],
            ['id'=>5,'name'=>'Fatima Patient',    'email'=>'patient@clinic.com',  'phone'=>'03005555555','role'=>'patient','status'=>1,'avatar'=>$av.'FP','salary_type'=>'fixed',     'salary_amount'=>0,    'joining_date'=>null,        'cnic'=>null,             'bank_account'=>null],
            ['id'=>6,'name'=>'Zara Patient',      'email'=>'zara@example.com',    'phone'=>'03006666666','role'=>'patient','status'=>1,'avatar'=>$av.'ZP','salary_type'=>'fixed',     'salary_amount'=>0,    'joining_date'=>null,        'cnic'=>null,             'bank_account'=>null],
        ];

        $pw = Hash::make('password');
        foreach ($users as $u) {
            DB::table('users')->updateOrInsert(
                ['id' => $u['id']],
                array_merge($u, ['password'=>$pw,'created_at'=>now(),'updated_at'=>now()])
            );
        }

        // Clear Spatie permission cache and assign roles
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            1 => 'super-admin',
            2 => 'doctor',
            3 => 'receptionist',
            4 => 'pharmacist',
            5 => 'patient',
            6 => 'patient',
        ];
        foreach ($roles as $uid => $roleName) {
            $user = \App\Models\User::find($uid);
            $role = \App\Models\Role::where('name', $roleName)->first();
            if ($user && $role) {
                // Sync so duplicate runs don't add duplicate pivot rows
                $user->syncRoles([$role]);
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Categories / Services
    // ─────────────────────────────────────────────────────────────────────────
    private function seedCategories(): void
    {
        $cats = [
            ['id'=>1,'name'=>'HydraFacial',        'price'=>8000, 'status'=>1,'is_parent'=>null,'url'=>null,'slug'=>'hydrafacial'],
            ['id'=>2,'name'=>'Laser Hair Removal', 'price'=>15000,'status'=>1,'is_parent'=>null,'url'=>null,'slug'=>'laser-hair-removal'],
            ['id'=>3,'name'=>'PRP Hair Regrowth',  'price'=>12000,'status'=>1,'is_parent'=>null,'url'=>null,'slug'=>'prp-hair-regrowth'],
            ['id'=>4,'name'=>'Botox Treatment',    'price'=>25000,'status'=>1,'is_parent'=>null,'url'=>null,'slug'=>'botox-treatment'],
            ['id'=>5,'name'=>'Carbon Peel Laser',  'price'=>6000, 'status'=>1,'is_parent'=>null,'url'=>null,'slug'=>'carbon-peel-laser'],
        ];
        foreach ($cats as $c) {
            $c['created_at'] = now(); $c['updated_at'] = now();
            DB::table('categories')->updateOrInsert(['id' => $c['id']], $c);
        }

        // Also seed the services table (used by doctor_agreements FK)
        $services = [
            ['id'=>1,'name'=>'HydraFacial',       'price'=>8000, 'status'=>1,'clinic_id'=>1],
            ['id'=>2,'name'=>'Laser Hair Removal','price'=>15000,'status'=>1,'clinic_id'=>1],
            ['id'=>3,'name'=>'PRP Hair Regrowth', 'price'=>12000,'status'=>1,'clinic_id'=>1],
            ['id'=>4,'name'=>'Botox Treatment',   'price'=>25000,'status'=>1,'clinic_id'=>1],
            ['id'=>5,'name'=>'Carbon Peel Laser', 'price'=>6000, 'status'=>1,'clinic_id'=>1],
        ];
        foreach ($services as $s) {
            $s['created_at'] = now(); $s['updated_at'] = now();
            DB::table('services')->updateOrInsert(['id' => $s['id']], $s);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Vendors
    // ─────────────────────────────────────────────────────────────────────────
    private function seedVendors(): void
    {
        $vendors = [
            ['name'=>'DermaCare Supplies', 'company'=>'DermaCare (Pvt) Ltd', 'phone'=>'04211234567',
             'email'=>'info@dermacare.pk', 'address'=>'24 Jail Road, Lahore', 'status'=>1, 'notes'=>'Primary skincare supplier'],
            ['name'=>'MediStock Pakistan', 'company'=>'MediStock (Pvt) Ltd', 'phone'=>'04217654321',
             'email'=>'orders@medistock.pk', 'address'=>'45 Gulberg III, Lahore', 'status'=>1, 'notes'=>'Medical consumables'],
            ['name'=>'AesthetiQ Distributors', 'company'=>'AesthetiQ (Pvt) Ltd', 'phone'=>'0300-9876543',
             'email'=>'sales@aesthetiq.pk', 'address'=>'DHA Phase 2, Lahore', 'status'=>1, 'notes'=>'Botox & fillers supplier'],
        ];
        foreach ($vendors as $v) {
            $v['created_at'] = now(); $v['updated_at'] = now();
            DB::table('vendors')->insert($v);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Products & Variations + Inventory
    // ─────────────────────────────────────────────────────────────────────────
    private function seedProducts(): void
    {
        $products = [
            ['id'=>1,'name'=>'Hyaluronic Acid Serum', 'price'=>3500, 'status'=>1, 'has_variations'=>0, 'track_inventory'=>1,
             'description'=>'Premium HA serum for skin hydration'],
            ['id'=>2,'name'=>'Botox Vial (Allergan)', 'price'=>18000,'status'=>1, 'has_variations'=>1, 'track_inventory'=>1,
             'description'=>'Botulinum toxin type A'],
            ['id'=>3,'name'=>'Microneedling Pen Tips','price'=>500,  'status'=>1, 'has_variations'=>1, 'track_inventory'=>1,
             'description'=>'Disposable pen tips 16-pin & 36-pin'],
            ['id'=>4,'name'=>'PRP Kit',               'price'=>2500, 'status'=>1, 'has_variations'=>0, 'track_inventory'=>1,
             'description'=>'Platelet-rich plasma centrifuge kit'],
            ['id'=>5,'name'=>'Laser Gel (250ml)',     'price'=>800,  'status'=>1, 'has_variations'=>0, 'track_inventory'=>1,
             'description'=>'Cooling gel for laser procedures'],
        ];
        foreach ($products as $p) {
            $p['created_at'] = now(); $p['updated_at'] = now();
            DB::table('products')->updateOrInsert(['id'=>$p['id']], $p);
        }

        // Variations for Botox Vial
        DB::table('product_variations')->insert([
            ['product_id'=>2,'name'=>'50 Units', 'price'=>18000,'status'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['product_id'=>2,'name'=>'100 Units','price'=>30000,'status'=>1,'created_at'=>now(),'updated_at'=>now()],
        ]);

        // Variations for Microneedling Tips
        DB::table('product_variations')->insert([
            ['product_id'=>3,'name'=>'16 Pin','price'=>500,'status'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['product_id'=>3,'name'=>'36 Pin','price'=>600,'status'=>1,'created_at'=>now(),'updated_at'=>now()],
        ]);

        // Seed inventory for all products (only columns that exist in migration)
        $inventoryItems = [
            ['product_id'=>1,'variation_id'=>null,'quantity'=>50,'cost_price'=>2000],
            ['product_id'=>2,'variation_id'=>1,   'quantity'=>20,'cost_price'=>12000],
            ['product_id'=>2,'variation_id'=>2,   'quantity'=>10,'cost_price'=>20000],
            ['product_id'=>3,'variation_id'=>3,   'quantity'=>200,'cost_price'=>250],
            ['product_id'=>3,'variation_id'=>4,   'quantity'=>150,'cost_price'=>300],
            ['product_id'=>4,'variation_id'=>null,'quantity'=>40,'cost_price'=>1500],
            ['product_id'=>5,'variation_id'=>null,'quantity'=>80,'cost_price'=>450],
        ];
        foreach ($inventoryItems as $i) {
            $i['created_at'] = now(); $i['updated_at'] = now();
            DB::table('inventory')->insert($i);
        }

        // Seed a few inventory movements
        $movements = [
            ['product_id'=>1,'variation_id'=>null,'type'=>'purchase','quantity'=>50,'unit_price'=>2000,'reference_type'=>'manual','reference_id'=>1,'created_by'=>1,'notes'=>'Opening stock','created_at'=>now()->subDays(30),'updated_at'=>now()->subDays(30)],
            ['product_id'=>2,'variation_id'=>1,   'type'=>'purchase','quantity'=>20,'unit_price'=>12000,'reference_type'=>'manual','reference_id'=>1,'created_by'=>1,'notes'=>'Opening stock','created_at'=>now()->subDays(30),'updated_at'=>now()->subDays(30)],
            ['product_id'=>4,'variation_id'=>null,'type'=>'purchase','quantity'=>40,'unit_price'=>1500, 'reference_type'=>'manual','reference_id'=>1,'created_by'=>1,'notes'=>'Opening stock','created_at'=>now()->subDays(30),'updated_at'=>now()->subDays(30)],
            ['product_id'=>5,'variation_id'=>null,'type'=>'purchase','quantity'=>80,'unit_price'=>450,  'reference_type'=>'manual','reference_id'=>1,'created_by'=>1,'notes'=>'Opening stock','created_at'=>now()->subDays(30),'updated_at'=>now()->subDays(30)],
        ];
        DB::table('inventory_movements')->insert($movements);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Purchase Requests
    // ─────────────────────────────────────────────────────────────────────────
    private function seedPurchaseRequests(): void
    {
        // PR-1: pending
        DB::table('purchase_requests')->insert([
            'pr_number'    => 'PR-00001',
            'requested_by' => 4,
            'approved_by'  => null,
            'status'       => 'pending',
            'notes'        => 'Monthly restock of microneedling tips and laser gel',
            'approved_at'  => null,
            'created_at'   => now()->subDays(3),
            'updated_at'   => now()->subDays(3),
        ]);
        DB::table('purchase_request_items')->insert([
            ['purchase_request_id'=>1,'product_id'=>3,'variation_id'=>3,'quantity'=>100,'notes'=>'Microneedling Pen Tips 16 Pin','created_at'=>now(),'updated_at'=>now()],
            ['purchase_request_id'=>1,'product_id'=>5,'variation_id'=>null,'quantity'=>20,'notes'=>'Laser Gel (250ml)','created_at'=>now(),'updated_at'=>now()],
        ]);

        // PR-2: approved
        DB::table('purchase_requests')->insert([
            'pr_number'    => 'PR-00002',
            'requested_by' => 3,
            'approved_by'  => 1,
            'status'       => 'approved',
            'notes'        => 'Urgent Botox restock',
            'approved_at'  => now()->subDays(1),
            'created_at'   => now()->subDays(5),
            'updated_at'   => now()->subDays(1),
        ]);
        DB::table('purchase_request_items')->insert([
            ['purchase_request_id'=>2,'product_id'=>2,'variation_id'=>1,'quantity'=>10,'notes'=>'Allergan brand only','created_at'=>now(),'updated_at'=>now()],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Purchases (Purchase Orders)
    // ─────────────────────────────────────────────────────────────────────────
    private function seedPurchases(): void
    {
        DB::table('purchases')->insert([
            'purchase_number'      => 'PO-00001',
            'vendor_id'            => 1,
            'purchase_request_id'  => null,
            'created_by'           => 1,
            'purchase_date'        => now()->subDays(25)->toDateString(),
            'total_amount'         => 175000,
            'discount'             => 5000,
            'net_amount'           => 170000,
            'payment_status'       => 'paid',
            'paid_amount'          => 170000,
            'notes'                => 'Opening stock purchase — all products',
            'created_at'           => now()->subDays(25),
            'updated_at'           => now()->subDays(25),
        ]);
        DB::table('purchase_items')->insert([
            ['purchase_id'=>1,'product_id'=>1,'variation_id'=>null,'quantity'=>50,'unit_cost'=>2000,'total_cost'=>100000,'selling_price'=>3500,'created_at'=>now(),'updated_at'=>now()],
            ['purchase_id'=>1,'product_id'=>4,'variation_id'=>null,'quantity'=>40,'unit_cost'=>1500,'total_cost'=>60000, 'selling_price'=>2500,'created_at'=>now(),'updated_at'=>now()],
            ['purchase_id'=>1,'product_id'=>5,'variation_id'=>null,'quantity'=>80,'unit_cost'=>450, 'total_cost'=>36000, 'selling_price'=>800, 'created_at'=>now(),'updated_at'=>now()],
        ]);

        DB::table('purchases')->insert([
            'purchase_number'      => 'PO-00002',
            'vendor_id'            => 3,
            'purchase_request_id'  => 2,
            'created_by'           => 1,
            'purchase_date'        => now()->subDays(2)->toDateString(),
            'total_amount'         => 240000,
            'discount'             => 0,
            'net_amount'           => 240000,
            'payment_status'       => 'partial',
            'paid_amount'          => 120000,
            'notes'                => 'Botox restock from AesthetiQ',
            'created_at'           => now()->subDays(2),
            'updated_at'           => now()->subDays(2),
        ]);
        DB::table('purchase_items')->insert([
            ['purchase_id'=>2,'product_id'=>2,'variation_id'=>1,'quantity'=>20,'unit_cost'=>12000,'total_cost'=>240000,'selling_price'=>18000,'created_at'=>now(),'updated_at'=>now()],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Appointments
    // ─────────────────────────────────────────────────────────────────────────
    private function seedAppointments(): void
    {
        $base = [
            'doctor_id'=>2,'clinic_id'=>1,'price'=>8000,'discounted_price'=>7500,
            'subtotal_price'=>8000,'subtotal_discounted_price'=>7500,
            'subtotal_price_after_discount'=>7500,'subtotal_discounted_price_after_discount'=>7500,
            'remaining_amount'=>0,'paid_amount'=>7500,'discount'=>500,
            'is_paid'=>'paid','appointment_status'=>'completed',
        ];

        $appointments = [
            array_merge($base, ['id'=>1,'name'=>'Fatima Patient','email'=>'patient@clinic.com','phone'=>'03005555555','whatsapp_number'=>'03005555555','user_id'=>5,'date'=>now()->subDays(10)->toDateString(),'time'=>now()->subDays(10)->setTime(10,0),'gender'=>0,'created_at'=>now()->subDays(10),'updated_at'=>now()->subDays(10)]),
            array_merge($base, ['id'=>2,'name'=>'Zara Patient',   'email'=>'zara@example.com',  'phone'=>'03006666666','whatsapp_number'=>'03006666666','user_id'=>6,'date'=>now()->subDays(7)->toDateString(), 'time'=>now()->subDays(7)->setTime(11,30),'gender'=>0,'created_at'=>now()->subDays(7), 'updated_at'=>now()->subDays(7)]),
            array_merge($base, ['id'=>3,'name'=>'Fatima Patient','email'=>'patient@clinic.com','phone'=>'03005555555','whatsapp_number'=>'03005555555','user_id'=>5,'date'=>now()->subDays(3)->toDateString(), 'time'=>now()->subDays(3)->setTime(14,0), 'gender'=>0,'appointment_status'=>'scheduled','is_paid'=>'pending','remaining_amount'=>8000,'paid_amount'=>0,'created_at'=>now()->subDays(3), 'updated_at'=>now()->subDays(3)]),
        ];
        foreach ($appointments as $a) {
            DB::table('appointments')->updateOrInsert(['id'=>$a['id']], $a);
        }

        // Appointment services
        DB::table('appointment_services')->insert([
            ['appointment_id'=>1,'name'=>'HydraFacial','price'=>8000,'discounted_price'=>7500,'discount'=>500,'service_id'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['appointment_id'=>2,'name'=>'Carbon Peel Laser','price'=>6000,'discounted_price'=>6000,'discount'=>0,'service_id'=>5,'created_at'=>now(),'updated_at'=>now()],
            ['appointment_id'=>3,'name'=>'Laser Hair Removal','price'=>15000,'discounted_price'=>15000,'discount'=>0,'service_id'=>2,'created_at'=>now(),'updated_at'=>now()],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Doctor Agreements
    // ─────────────────────────────────────────────────────────────────────────
    private function seedDoctorAgreements(): void
    {
        DB::table('doctor_agreements')->insert([
            [
                'doctor_id'      => 2,
                'clinic_id'      => null,
                'service_id'     => null,
                'share_type'     => 'percentage',
                'doctor_share'   => 60,
                'clinic_share'   => 40,
                'effective_from' => now()->subYear()->toDateString(),
                'effective_to'   => null,
                'is_active'      => 1,
                'notes'          => 'Standard 60/40 revenue split — all services',
                'created_by'     => 1,
                'created_at'     => now()->subYear(),
                'updated_at'     => now()->subYear(),
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Appointment Products (dispensed products / services billed)
    // ─────────────────────────────────────────────────────────────────────────
    private function seedAppointmentProducts(): void
    {
        // Appointment 1 — Fatima: HA Serum + PRP Kit
        $items = [
            ['appointment_id'=>1,'product_id'=>1,'variation_id'=>null,'product_name'=>'Hyaluronic Acid Serum','product_code'=>'HA-001','quantity'=>1,'unit_price'=>3500,'total_price'=>3500,'doctor_share_amount'=>2100,'clinic_share_amount'=>1400,'deduct_inventory'=>1,'notes'=>null,'added_by'=>2,'created_at'=>now()->subDays(10),'updated_at'=>now()->subDays(10)],
            ['appointment_id'=>1,'product_id'=>4,'variation_id'=>null,'product_name'=>'PRP Kit','product_code'=>'PRP-001','quantity'=>1,'unit_price'=>2500,'total_price'=>2500,'doctor_share_amount'=>1500,'clinic_share_amount'=>1000,'deduct_inventory'=>1,'notes'=>null,'added_by'=>2,'created_at'=>now()->subDays(10),'updated_at'=>now()->subDays(10)],
            // Appointment 2 — Zara: Laser Gel + Microneedling tip
            ['appointment_id'=>2,'product_id'=>5,'variation_id'=>null,'product_name'=>'Laser Gel (250ml)','product_code'=>'LG-001','quantity'=>1,'unit_price'=>800,'total_price'=>800,'doctor_share_amount'=>480,'clinic_share_amount'=>320,'deduct_inventory'=>1,'notes'=>null,'added_by'=>2,'created_at'=>now()->subDays(7),'updated_at'=>now()->subDays(7)],
            ['appointment_id'=>2,'product_id'=>3,'variation_id'=>3,'product_name'=>'Microneedling Pen Tips','product_code'=>'MN-16','quantity'=>2,'unit_price'=>500,'total_price'=>1000,'doctor_share_amount'=>600,'clinic_share_amount'=>400,'deduct_inventory'=>1,'notes'=>'Disposable per patient','added_by'=>2,'created_at'=>now()->subDays(7),'updated_at'=>now()->subDays(7)],
        ];
        DB::table('appointment_products')->insert($items);

        // Deduct from inventory accordingly
        DB::table('inventory')->where('product_id',1)->whereNull('variation_id')->decrement('quantity', 1);
        DB::table('inventory')->where('product_id',4)->whereNull('variation_id')->decrement('quantity', 1);
        DB::table('inventory')->where('product_id',5)->whereNull('variation_id')->decrement('quantity', 1);
        DB::table('inventory')->where('product_id',3)->where('variation_id',3)->decrement('quantity', 2);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Expenses
    // ─────────────────────────────────────────────────────────────────────────
    private function seedExpenses(): void
    {
        $months = [now()->subMonths(2), now()->subMonth(), now()];
        $expenses = [];
        foreach ($months as $m) {
            $expenses = array_merge($expenses, [
                ['title'=>'Clinic Rent',         'category'=>'rent',      'clinic_id'=>1,'amount'=>120000,'expense_date'=>$m->copy()->day(1)->toDateString(), 'payment_method'=>'bank',  'reference_number'=>'CHQ-'.rand(1000,9999),'notes'=>null,'created_by'=>1,'created_at'=>$m->copy()->day(1),'updated_at'=>$m->copy()->day(1)],
                ['title'=>'Electricity Bill',    'category'=>'utilities', 'clinic_id'=>1,'amount'=>18000, 'expense_date'=>$m->copy()->day(5)->toDateString(), 'payment_method'=>'bank',  'reference_number'=>null,'notes'=>null,'created_by'=>1,'created_at'=>$m->copy()->day(5),'updated_at'=>$m->copy()->day(5)],
                ['title'=>'Internet & Phone',    'category'=>'utilities', 'clinic_id'=>1,'amount'=>4500,  'expense_date'=>$m->copy()->day(5)->toDateString(), 'payment_method'=>'bank',  'reference_number'=>null,'notes'=>null,'created_by'=>1,'created_at'=>$m->copy()->day(5),'updated_at'=>$m->copy()->day(5)],
                ['title'=>'Cleaning Supplies',   'category'=>'supplies',  'clinic_id'=>1,'amount'=>8000,  'expense_date'=>$m->copy()->day(10)->toDateString(),'payment_method'=>'cash',  'reference_number'=>null,'notes'=>null,'created_by'=>1,'created_at'=>$m->copy()->day(10),'updated_at'=>$m->copy()->day(10)],
                ['title'=>'Staff Refreshments',  'category'=>'misc',      'clinic_id'=>1,'amount'=>3500,  'expense_date'=>$m->copy()->day(15)->toDateString(),'payment_method'=>'cash',  'reference_number'=>null,'notes'=>null,'created_by'=>3,'created_at'=>$m->copy()->day(15),'updated_at'=>$m->copy()->day(15)],
                ['title'=>'Equipment Maintenance','category'=>'maintenance','clinic_id'=>1,'amount'=>15000,'expense_date'=>$m->copy()->day(20)->toDateString(),'payment_method'=>'bank', 'reference_number'=>'TRF-'.rand(100,999),'notes'=>'Laser machine servicing','created_by'=>1,'created_at'=>$m->copy()->day(20),'updated_at'=>$m->copy()->day(20)],
            ]);
        }
        DB::table('expenses')->insert($expenses);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Salaries (3 months × 3 staff members)
    // ─────────────────────────────────────────────────────────────────────────
    private function seedSalaries(): void
    {
        $staffUsers = [
            ['user_id'=>1,'basic'=>80000,'bonus'=>5000,'deductions'=>0],
            ['user_id'=>3,'basic'=>35000,'bonus'=>2000,'deductions'=>500],
            ['user_id'=>4,'basic'=>40000,'bonus'=>3000,'deductions'=>0],
        ];

        $rows = [];
        for ($i = 2; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            foreach ($staffUsers as $s) {
                $net = $s['basic'] + $s['bonus'] - $s['deductions'];
                $rows[] = [
                    'user_id'        => $s['user_id'],
                    'month'          => (int)$month->format('n'),
                    'year'           => (int)$month->format('Y'),
                    'basic_salary'   => $s['basic'],
                    'bonus'          => $s['bonus'],
                    'deductions'     => $s['deductions'],
                    'net_salary'     => $net,
                    'notes'          => null,
                    'status'         => $i > 0 ? 'paid' : 'pending',
                    'paid_date'      => $i > 0 ? $month->copy()->endOfMonth()->toDateString() : null,
                    'payment_method' => $i > 0 ? 'bank' : null,
                    'processed_by'   => $i > 0 ? 1 : null,
                    'created_at'     => $month->copy()->day(1),
                    'updated_at'     => $month->copy()->day(1),
                ];
            }
        }
        DB::table('salaries')->insert($rows);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Call Logs
    // ─────────────────────────────────────────────────────────────────────────
    private function seedCallLogs(): void
    {
        DB::table('appointment_call_logs')->insert([
            ['appointment_id'=>3,'patient_id'=>5,'called_by'=>3,'call_type'=>'reminder','call_status'=>'answered','notes'=>'Patient confirmed appointment for tomorrow.','call_at'=>now()->subDays(2),'created_at'=>now()->subDays(2),'updated_at'=>now()->subDays(2)],
            ['appointment_id'=>1,'patient_id'=>5,'called_by'=>3,'call_type'=>'follow_up','call_status'=>'answered','notes'=>'Patient reports skin is feeling great after HydraFacial.','call_at'=>now()->subDays(7),'created_at'=>now()->subDays(7),'updated_at'=>now()->subDays(7)],
            ['appointment_id'=>2,'patient_id'=>6,'called_by'=>3,'call_type'=>'follow_up','call_status'=>'no_answer','notes'=>'Tried to reach for post-treatment follow up. No answer.','call_at'=>now()->subDays(5),'created_at'=>now()->subDays(5),'updated_at'=>now()->subDays(5)],
            ['appointment_id'=>null,'patient_id'=>6,'called_by'=>3,'call_type'=>'reschedule','call_status'=>'scheduled','notes'=>'Patient wants to reschedule upcoming appointment to next week.','call_at'=>now()->addDays(2),'created_at'=>now(),'updated_at'=>now()],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Consent Forms
    // ─────────────────────────────────────────────────────────────────────────
    private function seedConsentForms(): void
    {
        DB::table('consent_forms')->insert([
            [
                'appointment_id' => 1,
                'patient_id'     => 5,
                'form_title'     => 'HydraFacial Treatment Consent',
                'form_content'   => '<p>I, the undersigned, consent to undergo the HydraFacial treatment at RK Aesthetic Clinic. I understand the procedure involves cleansing, exfoliation, extraction, and hydration using the HydraFacial device.</p><p><strong>Possible side effects:</strong> temporary redness, mild irritation.</p><p>I confirm that I have disclosed all relevant medical history and am not currently pregnant.</p>',
                'signature_image'=> null,
                'signed'         => 0,
                'signed_at'      => null,
                'created_by'     => 3,
                'created_at'     => now()->subDays(10),
                'updated_at'     => now()->subDays(10),
            ],
            [
                'appointment_id' => 2,
                'patient_id'     => 6,
                'form_title'     => 'Laser Treatment Consent',
                'form_content'   => '<p>I consent to undergo laser skin treatment. I understand this treatment uses light energy to target pigment and I have been advised to avoid sun exposure for 2 weeks post-treatment.</p><p><strong>Risks include:</strong> temporary redness, hyperpigmentation in rare cases.</p>',
                'signature_image'=> null,
                'signed'         => 0,
                'signed_at'      => null,
                'created_by'     => 3,
                'created_at'     => now()->subDays(7),
                'updated_at'     => now()->subDays(7),
            ],
        ]);
    }
}
