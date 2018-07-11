<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
            <p><a href="{{ route('assets.show', ['room' => Hashids::encode($row->id)]) }}">{{ $row->number }}</a></p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
            <p><a href="{{ route('assets.show', ['room' => Hashids::encode($row->id)]) }}">{{ $row->description }}</a></p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 visible-md visible-lg">
            <p><a href="{{ route('assets.show', ['room' => Hashids::encode($row->id)]) }}">{{ $row->brand or trans('common.noData') }}</a></p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 visible-md visible-lg">
            <p><a href="{{ route('assets.show', ['room' => Hashids::encode($row->id)]) }}">{{ $row->model or trans('common.noData') }}</a></p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 visible-md visible-lg">
            @include('partials.dropdown-btn', [
                'options' => [
                    [
                        'option' => trans('common.edit'),
                        'url' => route('assets.edit', [
                            'room' => Hashids::encode($row->id)
                        ]),
                    ],
                    [
                        'type' => 'confirm',
                        'option' => trans('common.delete'),
                        'url' => route('assets.destroy', [
                            'room' => Hashids::encode($row->id)
                        ]),
                        'method' => 'DELETE'
                    ],
                ]
            ])
        </div>
    </div>
</div>