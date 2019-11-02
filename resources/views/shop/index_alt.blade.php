@extends('layouts.template')

@section('title', 'Shop')

@section('main')
    <h1>Shop - alternative listing</h1>
    @foreach($genres as $genre)
        <div>
            <h2>{{$genre->name}}</h2>
            <ul>
            @foreach($records as $record)
                 @if ($record->genre_id == $genre->id)
                        <li><a href="!#" data-id="{{$record->id}}" class="record">{{ $record->artist }} - {{ $record->title }}</a> | Price: € {{ $record->price }} | Stock: {{ $record->stock }}</li>
                 @endif
            @endforeach
            </ul>
        </div>
    @endforeach
@endsection
@section('script_after')
    <script>
        $(function () {
            // Get record id and redirect to the detail page
            $('.record').click(function () {
                record_id = $(this).data('id');
                $(location).attr('href', `/shop/${record_id}`); //OR $(location).attr('href', '/shop/' + record_id);
            });
        })
    </script>
@endsection
