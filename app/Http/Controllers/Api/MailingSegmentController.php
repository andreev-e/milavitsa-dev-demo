<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MailingSegment;
use App\Http\Resources\MailingSegmentCollection;
use Illuminate\Http\Request;

class MailingSegmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list = MailingSegment::select()->where('type', '<>', 'black');
        return MailingSegmentCollection::collection($list->paginate($request->limit));
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MailingSegment  $mailingSegment
     * @return \Illuminate\Http\Response
     */
    public function show(MailingSegment $mailingSegment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MailingSegment  $mailingSegment
     * @return \Illuminate\Http\Response
     */
    public function edit(MailingSegment $mailingSegment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MailingSegment  $mailingSegment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MailingSegment $mailingSegment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MailingSegment  $mailingSegment
     * @return \Illuminate\Http\Response
     */
    public function destroy(MailingSegment $mailingSegment)
    {
        //
    }
}
