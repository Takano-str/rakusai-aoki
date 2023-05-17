<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConsumerRequest;
use App\Http\Requests\UpdateConsumerRequest;
use App\Models\Consumer;

class ConsumerController extends Controller
{
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
     * @param  \App\Http\Requests\StoreConsumerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreConsumerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Consumer  $consumer
     * @return \Illuminate\Http\Response
     */
    public function show(Consumer $consumer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Consumer  $consumer
     * @return \Illuminate\Http\Response
     */
    public function edit(Consumer $consumer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateConsumerRequest  $request
     * @param  \App\Models\Consumer  $consumer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateConsumerRequest $request, Consumer $consumer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Consumer  $consumer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Consumer $consumer)
    {
        //
    }
}
