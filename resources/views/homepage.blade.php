@extends('layouts.base')

@section('title', 'Currency rates')
@section('body-class', '')

@section('content')
    <div class="container py-5">
        <h1 class="text-center">Currency rates by date</h1>
        <form class="mx-auto" style="max-width: 34rem;" method="POST" action="{{ route('home') }}">
            @csrf
            <div class="input-group mb-3">
                <span class="input-group-text">Select date</span>
                <input
                    class="form-control"
                    type="date"
                    name="day"
                    value="{{ $data['dates']['day'] }}"
                    min="{{ $data['dates']['min'] }}"
                    max="{{ $data['dates']['max'] }}"
                    required
                    pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}"
                >
                <button class="btn btn-primary" type="submit">Get rates</button>
            </div>
        </form>
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>Currency
                    <th>Rate in rubles
            <tbody>
                @forelse ($data['items'] as $item)
                <tr>
                    <td>{{ $item['currency'] }}
                    <td>{{ $item['rate'] }}
                @empty
                @php
                    $message = "Sorry, we not have rates for {$data['dates']['day']} date";
                @endphp
                <tr>
                    <td colspan="2">
                        <x-alert type="info" :message="$message"/>
                @endforelse
        </table>
    </div>
@endsection
