<?php

namespace App\Http\Controllers;

use App\Models\DashboardGroup;
use App\Models\DashboardMain;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Group;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class MainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        //dd($productsByCountry);

        $data = Cache::remember('dashboard_counts', 300, function () {
            $group = DashboardGroup::all();
            $main = DashboardMain::all();



            return [
                'main'  => $main,
                'group' => $group,

            ];
        });
        //dd($data);
        return view('welcome', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
