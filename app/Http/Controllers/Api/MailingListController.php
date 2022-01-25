<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MailingList;
use Illuminate\Http\Request;
use App\Http\Resources\MailingListCollection;
use App\Http\Resources\MailingListResource;

class MailingListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list = MailingList::select();
        $sort = json_decode($request->input('sort'));
        if ($sort) {
            if ($sort->order === 'descending') {
                $list->orderBy($sort->prop, 'desc');
            } else {
                $list->orderBy($sort->prop, 'asc');
            }
        }
		return MailingListCollection::collection($list->paginate($request->limit));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $mailingList = new MailingList();
        $mailingList->user_id = auth()->user()->id;
        self::update($request, $mailingList);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MailingList  $mailingList
     * @return \Illuminate\Http\Response
     */
    public function show(MailingList $mailingList)
    {
        return new MailingListResource($mailingList);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MailingList  $mailingList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MailingList $mailingList)
    {
        $mailingList->fill($request->all());

        $minTime = min($mailingList->allow_send_from, $mailingList->allow_send_to);
        $maxTime = max($mailingList->allow_send_from, $mailingList->allow_send_to);
        if ($maxTime && $minTime && $maxTime != $minTime) {
            $mailingList->allow_send_from = $minTime;
            $mailingList->allow_send_to = $maxTime;
        } else {
            $mailingList->allow_send_from = null;
            $mailingList->allow_send_to = null;
        }
        $mailingList->channel_order = json_encode($mailingList->channel_order);

        $mailingList->save();
        
        $mailingList->segments()->sync($request->input('segments'));
		return new MailingListResource($mailingList);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MailingList  $mailingList
     * @return \Illuminate\Http\Response
     */
    public function destroy(MailingList $mailingList)
    {
        $mailingList->delete();
    }
}
