<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Group;


class MainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items_count = Product::count();
        $groups_count = Group::count();
        $asl_count = Product::where('label', 1)->count();
        $gtin_count = Product::where('gtin', '!=', null)->count();
        $data = [
            'items_count' => $items_count,
            'groups_count' => $groups_count,
            'asl_count' => $asl_count,
            'gtin_count' => $gtin_count,
        ];
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
