<ul id="myTab" class="nav nav-tabs" role="tablist">
    @foreach($tabs as $tab)
        <li class="nav-item">
            <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ $tab['id'] }}-tab" href="#{{ $tab['id'] }}" data-toggle="tab" aria-controls="{{ $tab['id'] }}" aria-selected="true">
                {{ $tab['title'] }}
            </a>
        </li>
    @endforeach
</ul>
<div id="myTabContent" class="tab-content">
    @foreach($tabs as $tab)
        <div class="tab-pane fade show {{ $loop->first ? 'active' : '' }}" id="{{ $tab['id'] }}" role="tabpanel" aria-labelledby="{{ $tab['id'] }}-tab">
            @include('partials.list', [
                'data' => $data,
                'listHeading' => isset($tab['listHeading']) ? $tab['listHeading'] : $listHeading,
                'listRow' => isset($tab['listRow']) ? $tab['listRow'] : $listRow,
                'where' => isset($tab['where']) ? $tab['where'] : null,
            ])
        </div>
    @endforeach
</div>