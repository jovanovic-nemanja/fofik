<?php
namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;

class PagesController extends Controller
{
    protected $historyService;
    public function __construct()
    {   

    }
    /**
     * Dashobard view
     * @return mixed
     */
    public function dashboard()
    {
        $today = today();
        $startDate = today()->subdays(14);
        $period = CarbonPeriod::create($startDate, $today);
        $datasheet = [];
        // Iterate over the period
        // foreach ($period as $date) {
        //     $datasheet[$date->format(carbonDate())] = [];
        //     $datasheet[$date->format(carbonDate())]["monthly"] = [];
        //     $datasheet[$date->format(carbonDate())]["monthly"]["tasks"] = 0;
        //     $datasheet[$date->format(carbonDate())]["monthly"]["leads"] = 0;
        // }
        // print_r("gotcha");exit();
        $tasks = [];
        $leads = [];
        foreach ($tasks as $task) {
            $datasheet[$task->created_at->format(carbonDate())]["monthly"]["tasks"]++;
        }

        foreach ($leads as $lead) {
            $datasheet[$lead->created_at->format(carbonDate())]["monthly"]["leads"]++;
        }
        $absences = [];

        return view('pages.dashboard')
            ->withUsers(User::all())
            ->withDatasheet($datasheet)
            ->withAbsencesToday($absences);
    }
    public function test()
    {
        echo "Yahoo";
    }
}
