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
        $sort = json_decode($request->input('sort'));
        $list = MailingList::select();
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
        $mailingList = MailingList::create();
        $mailingList->fill($request->all());
        $mailingList->save();
		return new MailingListResource($mailingList);
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
        $mailingList->save();
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
