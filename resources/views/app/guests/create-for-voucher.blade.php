@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('voucher', $voucher) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('vouchers.title'),
            'url' => route('vouchers.index'),
            'options' => [
                [
                    'option' => 'Buscar huéspedes',
                    'url' => route('vouchers.guests.search', ['id' => Hashids::encode($voucher->id)])
                ],
                [
                    'option' => 'Agregar empresa',
                    'url' => route('vouchers.companies.search', [
                        'id' => Hashids::encode($voucher->id)
                    ])
                ],
                [
                    'option' => 'Volver al recibo',
                    'url' => route('vouchers.show', [
                        'id' => Hashids::encode($voucher->id)
                    ])
                ]
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.form', [
                    'title' => [
                        'title' => trans('common.creationOf') . ' ' . trans('guests.title'),
                        'align' => 'text-center'
                    ],
                    'url' => route('vouchers.guests.store', ['id' => Hashids::encode($voucher->id)]),
                    'fields' => [
                        'app.guests.create-fields',
                        'app.vouchers.guests.create-fields'
                    ],
                    'btn' => trans('common.create')
                ])
            </div>
        </div>

        @include('partials.spacer', ['size' => 'md'])
    </div>

@endsection