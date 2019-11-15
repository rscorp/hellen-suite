<?php

namespace App\Http\Controllers;

use App\Exports\GuestsReport;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Vinkla\Hashids\Facades\Hashids;
use App\Helpers\{Id, Input, Fields};
use App\Http\Requests\StoreGuest;
use App\Http\Requests\UpdateGuest;
use App\Welkome\{Country, Guest, IdentificationType};
use Maatwebsite\Excel\Facades\Excel;

class GuestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $guests = Guest::paginate(
            config('welkome.paginate'),
            Fields::get('guests')
        );

        return view('app.guests.index', compact('guests'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = IdentificationType::all(['id', 'type']);
        $countries = Country::all(['id', 'name']);

        return view('app.guests.create', compact('types', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGuest $request)
    {
        $guest = new Guest();
        $guest->name = $request->name;
        $guest->last_name = $request->last_name;
        $guest->dni = $request->dni;
        $guest->email = $request->get('email', null);
        $guest->gender = $request->get('gender', null);
        $guest->birthdate = $request->get('birthdate', null);
        $guest->profession = $request->get('profession', null);
        $guest->status = false; # Not in hotel
        $guest->identificationType()->associate(Id::get($request->type));
        $guest->user()->associate(Id::parent());
        $guest->country()->associate(Id::get($request->nationality));

        if ($guest->save()) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('guests.show', [
                'id' => Hashids::encode($guest->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        flash('Característica en proceso de construcción')->info();

        return redirect()->route('guests.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $guest = Guest::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->first(Fields::get('guests'));

        if (empty($guest)) {
            abort(404);
        }

        $guest->load([
            'identificationType' => function ($query)
            {
                $query->select(['id', 'type']);
            },
            'country' => function ($query)
            {
                $query->select(['id', 'name']);
            }
        ]);

        $types = IdentificationType::all(['id', 'type']);
        $countries = Country::all(['id', 'name']);

        return view('app.guests.edit', compact('guest', 'types', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGuest $request, $id)
    {
        $guest = Guest::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->first(Fields::get('guests'));

        if (empty($guest)) {
            abort(404);
        }

        $guest->name = $request->name;
        $guest->last_name = $request->last_name;
        $guest->dni = $request->dni;
        $guest->email = $request->get('email', null);
        $guest->gender = $request->get('gender', null);
        $guest->birthdate = $request->get('birthdate', null);
        $guest->profession = $request->get('profession', null);

        if (!empty($request->type)) {
            $guest->identificationType()->associate(Id::get($request->type));
        }

        if (!empty($request->nationality)) {
            $guest->country()->associate(Id::get($request->nationality));
        }

        if ($guest->save()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('guests.show', [
                'id' => Hashids::encode($guest->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('guests.show', [
            'id' => Hashids::encode($guest->id)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $guest = Guest::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->first(Fields::get('guests'));

        if (empty($guest)) {
            abort(404);
        }

        $guest->load([
            'invoices' => function ($query)
            {
                $query->select(['id']);
            }
        ]);

        if ($guest->invoices->isNotEmpty()) {
            flash(trans('common.notRemovable'))->info();

            return redirect()->route('guests.show', [
                'guest' => Hashids::encode($guest->id)
            ]);
        } else {
            if ($guest->delete()) {
                flash(trans('common.deletedSuccessfully'))->success();

                return redirect()->route('guests.index');
            }
        }

        flash(trans('common.error'))->error();

        return redirect()->route('guests.index');
    }

    /**
     * Display a listing of searched records.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = Input::clean($request->get('query', null));

        if (empty($query)) {
            return back();
        }

        $guests = Guest::where('user_id', Id::parent())
            ->whereLike(['name', 'last_name', 'dni', 'email'], $query)
            ->get(Fields::get('guests'));

        return view('app.guests.search', compact('guests', 'query'));
    }

    /**
     * Display a JSON list with searched records.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function searchUnregistered(Request $request)
    {
        if ($request->ajax()) {
            $query = Input::clean($request->get('query', null));

            $guests = Guest::where('user_id', Id::parent())
                ->where('status', false)
                ->whereLike(['name', 'last_name', 'dni', 'email'], $query)
                ->get(Fields::get('guests'));

            return response()->json([
                'guests' => $this->renderToTemplate(
                    $guests,
                    'app.invoices.guests.search',
                    $request->invoice
                    )
            ]);
        }

        abort(403);
    }

    /**
     * Render data collection in a view.
     *
     * @param Illuminate\Support\Collection  $results
     * @return array
     */
    private function renderToTemplate(Collection $results, $template, $invoice)
    {
        $rendered = collect();

        $results->each(function ($guest, $index) use (&$rendered, $template, $invoice) {
            $render = view($template, [
                'guest' => $guest,
                'invoice' => $invoice
            ])->render();

            $rendered->push($render);
        });

        return $rendered->toArray();
    }

    /**
     * Export a listing of guests in excel format.
     *
     * @return \Maatwebsite\Excel\Excel
     */
    public function export()
    {
        $guests = Guest::where('user_id', Id::parent())
            ->with([
                'identificationType' => function ($query)
                {
                    $query->select(['id', 'type']);
                },
                'country' => function ($query)
                {
                    $query->select(['id', 'name']);
                }
            ])
            ->get(Fields::get('guests'));

        if ($guests->isEmpty()) {
            flash(trans('common.noRecords'))->info();

            return redirect()->route('guests.index');
        }

        return Excel::download(new GuestsReport($guests), trans('guests.title') . '.xlsx');
    }
}
