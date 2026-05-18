<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\BulkAppointment;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Hash;

class DataScrapingService
{

    protected $email;
    protected $password;
    protected $loginUrl;
    public function __construct()
    {
        $this->email = 'dr_daha885@hotmail.com';
        $this->password = 'DigitalCode2024';

        $this->loginUrl = 'https://cms-beautysthetics.com/login';  // URL of the login page
    }

    function scrapeAllPagesData()
    {



        $loginUrl = $this->loginUrl; //'https://cms-beautysthetics.com/login';  // Login URL
        $scrapeUrl = 'https://cms-beautysthetics.com/per-page-item'; // Scrape URL

        $filename = "output.csv";
        $file = fopen($filename, 'w'); // Open the file for writing

        // Initialize cURL session for login
        $ch = curl_init();

        // 1. Fetch the login page to get the CSRF token
        curl_setopt($ch, CURLOPT_URL, $loginUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');  // Save cookies in cookies.txt
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt'); // Load cookies for the session
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);      // Follow redirects if any

        $loginPage = curl_exec($ch);

        // Check if the page loads properly
        if (!$loginPage) {
            die("Failed to load login page.");
        }

        // Extract CSRF token from the login page
        preg_match('/name="_token" value="([^"]+)"/', $loginPage, $matches);

        if (isset($matches[1])) {
            $csrfToken = $matches[1];  // Successfully extracted the CSRF token
        } else {
            die("CSRF token not found. Cannot proceed with login.");
        }

        // Perform login request with email, password, and CSRF token
        $postFields = http_build_query([
            'email' => $this->email,
            'password' => $this->password,
            '_token' => $csrfToken,   // CSRF token field is required for Laravel
        ]);

        curl_setopt($ch, CURLOPT_URL, $loginUrl);   // Set login URL again
        curl_setopt($ch, CURLOPT_POST, true);       // Use POST for form submission
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);   // Send login data
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);      // Get the response

        $loginResponse = curl_exec($ch);

        // Check if login was successful
        if (strpos($loginResponse, 'These credentials do not match our records') !== false) {
            die("Login failed. Invalid credentials.");
        }

        // Step 2: Set dynamic page and scrape data
        curl_setopt($ch, CURLOPT_URL, $scrapeUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['page' => 150, '_token' => $csrfToken]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-Requested-With: XMLHttpRequest',
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3'
        ]);
        // Execute the request and store the response
        $response = curl_exec($ch);

        // print_r($response);

        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        } else {
            // Process and save response data to CSV


            $scrapeUrl = 'https://cms-beautysthetics.com/invoice';


            curl_setopt($ch, CURLOPT_URL, $scrapeUrl);    // Set the URL of the page to scrape
            curl_setopt($ch, CURLOPT_POST, false);           // Set it to a GET request now
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Get the response of the page

            $dashboardPage = curl_exec($ch);

            // Debug: Print the protected page content to verify data is fetched
            // echo $dashboardPage;

            // 5. Parse and extract the data from the protected page
            // For example, extract some information from the dashboard
            // You can use regex or DOMDocument to parse the page as needed
            preg_match('/<table>(.*?)<\/table>/', $dashboardPage, $matches);


            $dom = new DOMDocument();

            // Suppress errors due to invalid HTML (optional)
            libxml_use_internal_errors(true);

            // Load the HTML content
            $dom->loadHTML($dashboardPage);

            // Restore error handling
            libxml_clear_errors();

            // Create a new DOMXPath instance
            $xpath = new DOMXPath($dom);

            // Query for table elements
            $tables = $xpath->query('//table');

            foreach ($tables as $table) {
                // Process each table as needed
                // Example: Extract rows and columns
                $rows = $table->getElementsByTagName('tr');
                // foreach ($rows as $row) {
                //     $cols = $row->getElementsByTagName('td');
                //     $data = [];
                //     foreach ($cols as $col) {
                //         $data[] = trim($col->textContent);
                //     }
                //     fputcsv($file, $data);
                // }


                foreach ($rows as $row) {
                    // Find the last td in the row
                    $lastTd = $xpath->query('td[last()]', $row)->item(0);
                    if ($lastTd) {
                        // Find the first a within the last td
                        $firstLink = $xpath->query('a', $lastTd)->item(0);
                        if ($firstLink && $firstLink->getAttribute('href')) {
                            // Output the href attribute of the link
                            $link = $firstLink->getAttribute('href');
                            $invoiceid = explode('.com/invoice/', $link)[1];
                            $data[] = ['url' => $link, 'invoice_id' => $invoiceid, 'is_processed' => false];
                        }
                    }
                }
                BulkAppointment::upsert($data, ['invoice_id']);
                return true;
            }
        }
    }
    function CreateSale()
    {


        // Initialize cURL session
        $ch = curl_init();

        // 1. Fetch the login page to get the CSRF token
        curl_setopt($ch, CURLOPT_URL, $this->loginUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');  // Save cookies in cookies.txt
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt'); // Load cookies for the session
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);      // Follow redirects if any

        $loginPage = curl_exec($ch);

        // Check if the page loads properly
        if (!$loginPage) {
            die("Failed to load login page.");
        }

        // Debug: print the login page to verify the CSRF token is available
        // echo $loginPage;

        // 2. Extract CSRF token from the login page
        preg_match('/name="_token" value="([^"]+)"/', $loginPage, $matches);

        if (isset($matches[1])) {
            $csrfToken = $matches[1];  // Successfully extracted the CSRF token
        } else {
            die("CSRF token not found. Cannot proceed with login.");
        }

        // Debug: Check if the CSRF token was captured correctly
        // echo "CSRF Token: $csrfToken\n";

        // 3. Perform login request with email, password, and CSRF token
        $postFields = http_build_query([
            'email' => $this->email,
            'password' => $this->password,
            '_token' => $csrfToken,   // CSRF token field is required for Laravel
        ]);

        curl_setopt($ch, CURLOPT_URL, $this->loginUrl);   // Set login URL again
        curl_setopt($ch, CURLOPT_POST, true);       // Use POST for form submission
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);   // Send login data
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);      // Get the response

        $loginResponse = curl_exec($ch);

        // Debug: Print the response to check for any login errors
        // echo "Login Response: $loginResponse\n";

        // Check if login was successful (you can look for some known text in the response or check redirects)
        if (strpos($loginResponse, 'These credentials do not match our records') !== false) {
            die("Login failed. Invalid credentials.");
        }


        $urls = BulkAppointment::where('is_processed', 0)->limit(100)->get();
        if($urls->count()==0){
        echo "Waiting for new Appointment";
        dd("OK");
        }
        foreach ($urls as $scrapeUrl) {
            // 4. After logging in successfully, access the protected page (e.g., dashboard)
            curl_setopt($ch, CURLOPT_URL, $scrapeUrl->url);    // Set the URL of the page to scrape
            curl_setopt($ch, CURLOPT_POST, false);           // Set it to a GET request now
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Get the response of the page
            $dashboardPage = curl_exec($ch);
            // Debug: Print the protected page content to verify data is fetched
            // echo $dashboardPage;

            // 5. Parse and extract the data from the protected page
            // For example, extract some information from the dashboard
            // You can use regex or DOMDocument to parse the page as needed
            preg_match('/<table>(.*?)<\/table>/', $dashboardPage, $matches);

            // Create a new DOMDocument instance
            $dom = new DOMDocument();

            // Suppress errors due to invalid HTML (optional)
            libxml_use_internal_errors(true);

            // Load the HTML content

            $dom->loadHTML($dashboardPage);

            // Restore error handling
            libxml_clear_errors();

            // Create a new DOMXPath instance
            $xpath = new DOMXPath($dom);

            // Query for div elements with a specific class
            $divs = $xpath->query('//div[@class="col-3"]');
          //  echo "<pre>";

            $temp = [];

            foreach ($divs as $div) {
                $string = trim($div->textContent);
                // Explode the string by newline characters
                $lines = preg_split('/\r\n|\r|\n/', $string);
                // print_r($lines);

                if (trim($lines[0]) == 'Patient Details') {
                    if (substr(trim($lines[1]), 0, 2) == 'Mr') {
                        $temp['gender'] = 'Male';
                    } else {
                        $temp['gender'] = 'Female';
                    }

                    $temp['patient_name'] = trim($lines[1]);
                    $temp['patient_phone'] = str_replace('+', '', trim($lines[3]));
                    $temp['patient_email'] = trim($lines[4]);
                } elseif (trim($lines[0]) == 'Doctor Details') {

                    $temp['name'] = trim($lines[1]);
                    $temp['phone'] = str_replace('+', '', trim($lines[3]));
                    $temp['email'] = trim($lines[4]);
                } elseif (trim($lines[0]) == 'Payment Details') {
                    $payment_method = explode(':', trim($lines[1]));
                    $temp['payment_method'] = trim($payment_method[1]);
                    $payment_status = explode(':', trim($lines[2]));
                    $temp['payment_status'] = trim($payment_status[1]);
                }
                // die();
                // Output the content of each div
                // echo $div->nodeValue . "\n"; // Outputs: First Div \n Second Div
            }

            // print_r($temp);
            // die();
            $divs = $xpath->query('//tr');
            // echo "<pre>";
            // echo $scrapeUrl;
            $services = [];

            foreach ($divs as $key => $div) {

                if ($key == 0)
                    continue;

                // print_r($div->textContent);
                $string = trim($div->textContent);
                $tmepser = [];
                // Explode the string by newline characters
                $lines = preg_split('/\r\n|\r|\n/', $string);
                if (count($lines) == 2) {
                    $temp['subtotal'] = trim(explode(':', $line)[1]);
                } elseif (count($lines) == 3) {
                    $count = 0;
                    foreach ($lines as $services_price => $line) {
                        if ($services_price == 0 && substr(trim($line), 0, 3) == 'Tax') {
                            // print_r(trim($lines[2]));

                            $temp['tax'] = trim(explode(':', trim($lines[2]))[1]);
                        }
                        if ($services_price == 1 && !empty(trim($line))) {
                            // echo substr(trim($line),0,3);
                            $tmepser['service_name'] = trim($line);
                        } elseif ($services_price == 2) {
                            $tmepser['price'] = trim(explode(':', $line)[1]);
                        }
                    }
                    // if(isset($tmepser['service_name']))
                    $temp['services'][] = $tmepser;
                } elseif (count($lines) == 4) {
                    $temp['total'] = trim(explode(':', trim($lines[3]))[1]);
                }



                // Output the content of each div
                // echo $div->nodeValue . "\n"; // Outputs: First Div \n Second Div
            }
            if (count($divs) == 0) {
                $temp['services'] = [];
            }

            $divs = $xpath->query('//div[@class="col-3 pull-right"]');
            // echo "<pre>";
            foreach ($divs as $div) {
                $string = trim($div->textContent);
                $lines = preg_split('/\r\n|\r|\n/', $string);
                $temp['invoice_date'] = explode('Invoice date:', trim($lines[0]))[1];
                $temp['appointment_date'] = explode('to', trim(explode('Appointment date:', trim($lines[1]))[1]))[0];
            }

            $temp['services_price'] = $this->arraytoString($temp['services']);
            ksort($temp);
            // Loop through the array and write each row to the CSV file
            $data[] = $temp;
            echo "<pre>";
            $invoices = $data;

            foreach ($invoices as $invoice) {

                $this->AppointmentService($invoice, $scrapeUrl);
            }
            $scrapeUrl->is_processed = true;
            $scrapeUrl->save();
            // dd($data);
            $data =  array();
        }
    }
    function AppointmentService($data, $invoice)
    {


        $alreadyExist = Appointment::where('invoice_id', $invoice->invoice_id)->where('invoice_id', '!=', 0)->first();
        if (!is_null($alreadyExist))
            return true;

        $discount = 0;
        $paid_amount = $data['total'];
        $service_id = $this->createService($data['services']);

        $serviceIds = collect($service_id)->pluck('id');
        // dd($data,$service_id,$serviceIds);
        $DBCategories = Category::find($serviceIds)
            ->keyBy('id');
        $subtotal = $data['total'];
        $service_total = $subtotal - $discount;

        $actual_service_price = 0;
        $appointment_services = array();
        foreach ($service_id as $index => $services_array) {
            if (!isset($DBCategories[$services_array['id']]))
                continue;
            $temp = array();
            $temp['name'] = $DBCategories[$services_array['id']]->name;
            $temp['service_id']   = $DBCategories[$services_array['id']]->id;
            $temp['appointment_id']   = 0;

            $actual_service_price += $DBCategories[$services_array['id']]->price;
            $temp['price']   = $DBCategories[$services_array['id']]->price;
            $temp['discounted_price']   = $services_array['price'];
            $temp['discount']   = 0;
            $temp['created_at'] = now();
            $temp['updated_at'] = now();
            $appointment_services[] = $temp;
        }

        $cost_total = $actual_service_price - $discount;

        list($series, $serial_number, $serial_series) = $this->getInvoiceFields();

        // dd($serviceIds,$subtotal,$appointment_services,$series,$serial_number,$serial_series);
        $appointmData['serial'] = $series;
        $appointmData['invoice_id'] = $invoice->invoice_id;

        $appointmData['name'] = $data['patient_name'];
        $appointmData['email'] = $data['patient_email'];
        $appointmData['serial_series'] = $serial_series;
        $appointmData['serial_number'] = $serial_number;
        $appointmData['price'] = $actual_service_price;
        $appointmData['discounted_price'] = $subtotal;
        $appointmData['clinic_id'] = 1;

        $appointmData['subtotal_price'] = $actual_service_price;
        $appointmData['subtotal_discounted_price'] = $subtotal;
        $appointmData['subtotal_price_after_discount'] = $actual_service_price - $discount;
        $appointmData['subtotal_discounted_price_after_discount'] = $subtotal - $discount;
        $appointmData['paid_amount'] = $paid_amount;
        $appointmData['remaining_amount'] = $subtotal - $discount - $paid_amount;
        list($doctor_id, $patient_id) = $this->createUser($data);
        $appointmData['user_id'] = $patient_id;
        $appointmData['doctor_id'] = $doctor_id;
        $appointmData['is_paid'] = true;
        $appointmData['appointment_status'] = 5;
        $appointmData['phone'] = '+' . $data['patient_phone'];
        $appointmData['whatsapp_number'] = '+' . $data['patient_phone'];
        $appointmData['date'] = Carbon::parse($data['appointment_date'])->format('Y-m-d');
        $appointmData['gender'] = $data['gender'] == 'Female' ? 0 : 1;


        $appointment = Appointment::create($appointmData);
        // dd($appointment);
        $appointment_services = array_map(function ($item) use ($appointment) {
            $item['appointment_id'] =  $appointment->id;
            return $item;
        }, $appointment_services);

        AppointmentService::insert($appointment_services);
        return true;
    }

    function getInvoiceFields()
    {
        $months = config('Invoice');
        $month =  ltrim(date('m'), '0');
        $year  = date('Y');
        $series  =  $months[$month] . $year;
        $serial_number =  (Appointment::where('serial', $series)->max('serial_number') ?? 0) + 1;
        $serial_series = $series . '-' . $serial_number;
        return [$series, $serial_number, $serial_series];
    }

    function addService()
    {

        Category::create();
    }

    function arraytoString($services)
    {
        $str = '';
        if (count($services) > 0) {
            foreach ($services as $service) {
                if (isset($service['service_name']))
                    $str .= $service['service_name'] . '====' . $service['price'] . '(--)';
            }
        }

        return $str;
    }


    function demoEntries()
    {
        $invoices = [
            [
                'appointment_date' => '2024-11-29 15:50:00',
                'email' => 'dr_daha885@hotmail.com',
                'gender' => 'Female',
                'invoice_date' => '2024-11-29 19:39:50',
                'name' => 'Mubashar Daha',
                'patient_email' => 'KALSOM61@GMAIL.COM',
                'patient_name' => 'MISS KALSOOM',
                'patient_phone' => '923348287838',
                'payment_method' => 'Cash Payement',
                'payment_status' => 'Paid',
                'phone' => '923336037272',
                'services' => [
                    [
                        'service_name' => 'Q-SWITCH',
                        'price' => 7000
                    ],
                    [
                        'price' => 0
                    ]
                ],
                'services_price' => 'Q-SWITCH====7000(--)',
                'subtotal' => 7000,
                'tax' => 0,
                'total' => 7000
            ]
        ];

        foreach ($invoices as $invoice) {

                // $this->createUser($invoice);
            ;
            dd($invoice, $this->createService($invoice['services']), $this->createUser($invoice));
            // Create Paitent 
            // Create Doctor
            // Service

            // Prepare appointment data

            // Create Sale and Attach services



        }
    }

    function createService($appointmenServices)
    {

        $services = array();
        if (!empty($appointmenServices)) {

            foreach ($appointmenServices as $service) {

                if (isset($service['service_name'])) {

                    $category =  Category::where(['name' => $service['service_name']])->first();

                    if (is_null($category)) {
                        $services[] =  Category::create(['name' => $service['service_name'], 'price' => $service['price'], 'status' => 1]);
                    } else {
                        $services[]  = $category;
                    }
                }
            }
        }
        return $services;

        dd($appointmenServices, $services);
    }
    function createUser($data)
    {

        $patient = User::where('email', $data['patient_email'])->orwhere('phone', $data['patient_phone'])->whereNotNull('email')->first();
        $doctor = User::where('email', $data['email'])->orwhere('phone', $data['phone'])->whereNotNull('email')->first();

        if (is_null($patient)) {

            $patient = User::create([
                'name' => $data['patient_name'],
                'email' => $data['patient_email'],
                'phone' => $data['patient_phone'],
                'status' => 1,
                'role' => 'patient',
                'pasword' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }

        if (is_null($doctor)) {
            $doctor = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'status' => 1,
                'role' => 'doctor',
                'pasword' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }

        return [$doctor->id, $patient->id];
    }
}
