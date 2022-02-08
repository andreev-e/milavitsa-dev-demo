<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MailingList;
use App\Models\MailingMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\MailingListCollection;
use App\Http\Resources\MailingListResource;
use App\Http\Resources\MailingStatisticCollection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        if (in_array($mailingList->status, ['sending', 'finished'])) {
            return;
        }
        $mailingList->fill($request->all());

        $from = Carbon::createFromFormat('H:i', $mailingList->allow_send_from);
        $to = Carbon::createFromFormat('H:i', $mailingList->allow_send_to);

        if ($from->lt($to)) {
            $mailingList->allow_send_from = $from->format('H:i');
            $mailingList->allow_send_to = $to->format('H:i');
        } elseif ($from->gt($to)) {
            $mailingList->allow_send_from = $to->format('H:i');
            $mailingList->allow_send_to = $from->format('H:i');
        }
        else {
            $mailingList->allow_send_from = null;
            $mailingList->allow_send_to = null;
        }

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

    public function copy(Request $request)
    {
        $old = MailingList::findOrFail($request->input('id'));
        $new = $old->replicate();
        $new->name = 'Копия ' . $new->name;
        $new->start = null;
        $new->status = 'blueprint';
        $new->save();
        $new->segments()->sync($old->segments);
        return new MailingListResource($new);
    }

    public function statistic($id, Request $request) {

        if ($request->input('data')) {
            if ($request->input('data') === 'opens') {
                $list = MailingMessage::select(
                        'channel',
                        'opened',
                        DB::raw("count(*) as total"),
                    )
                    ->where('opened', '>', 0)
                    ->where('mailing_list_id', $id)
                    ->groupBy('opened');
            }
            if ($request->input('data') === 'links') {
                $list = MailingMessage::select(
                        'channel',
                        'link_counters.opened',
                        DB::raw("count(*) as total"),
                    )
                    ->leftJoin('link_counters', 'mailing_messages.id', '=', 'link_counters.mailing_message_id')
                    ->where('link_counters.opened', '>', 0)
                    ->where('mailing_list_id', $id)
                    ->groupBy('channel', 'link_counters.opened');

            }
        } else {
            $list = MailingMessage::select(
                    'channel',
                    'status',
                    DB::raw("count(*) as total"),
                )
                ->where('mailing_list_id', $id)
                ->groupBy('channel', 'status');
        }
        return MailingStatisticCollection::collection($list->get());

    }


}
