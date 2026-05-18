<?php

namespace App\Http\Controllers;

use App\Models\ModulePermission;
use App\Http\Requests\StoreModulePermissionRequest;
use App\Http\Requests\UpdateModulePermissionRequest;

class ModulePermissionController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(ModulePermission::class);
        $this->middleware(['auth','avoid-back-history']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreModulePermissionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreModulePermissionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ModulePermission  $modulePermission
     * @return \Illuminate\Http\Response
     */
    public function show(ModulePermission $modulePermission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ModulePermission  $modulePermission
     * @return \Illuminate\Http\Response
     */
    public function edit(ModulePermission $modulePermission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateModulePermissionRequest  $request
     * @param  \App\Models\ModulePermission  $modulePermission
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateModulePermissionRequest $request, ModulePermission $modulePermission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ModulePermission  $modulePermission
     * @return \Illuminate\Http\Response
     */
    public function destroy(ModulePermission $modulePermission)
    {
        //
    }
}
