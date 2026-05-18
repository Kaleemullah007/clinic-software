<?php

namespace App\Http\Controllers;

use App\Services\DataScrapingService;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Response;
// Route::get('download-csv',[ScrappingController::class,'exportUsersToCsv'])->name('exportUsersToCsv');

class ScrappingController extends Controller
{
    protected $service;
    function __construct(DataScrapingService $service)
    {
        $this->service = $service;
    }
    function index(){
        
        // dd(User::get()->count());
        // dd("dd");
        info("New Sale ".date('Y-m-d H"i:s'));
        $this->service->scrapeAllPagesData();
       
        return true;
        
    }
    function AddSales(){
       info("Create Sale ".date('Y-m-d H"i:s'));
        $this->service->CreateSale();
        return true;
        
    }
    
 public function exportUsersToCsv()
{
    $filename = "users_export.csv";
    $users = User::select('name', 'phone')->get(); // changed here

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['First Name', 'Last Name', 'Phone Number'];

    $callback = function() use ($users, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($users as $user) {
            $nameParts = preg_split('/\s+/', trim($user->name));

            if (count($nameParts) === 1) {
                $firstName = $nameParts[0];
                $lastName = '';
            } elseif (count($nameParts) === 2) {
                [$firstName, $lastName] = $nameParts;
            } else {
                $firstName = $nameParts[0] . ' ' . $nameParts[1];
                $lastName = implode(' ', array_slice($nameParts, 2));
            }

            fputcsv($file, [
                $firstName,
                $lastName,
                "\t" . $user->phone 
            ]);
        }

        fclose($file);
    };

    return Response::stream($callback, 200, $headers);
}
    
    
    
}
