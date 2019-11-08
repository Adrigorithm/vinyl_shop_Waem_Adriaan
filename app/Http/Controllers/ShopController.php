<?php

namespace App\Http\Controllers;

use App\Genre;
use App\Helpers\Json;
use App\Record;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    // Master Page: http://vinyl_shop.test/shop or http://localhost:3000/shop
    public function index(Request $request)
    {
        $genre_id = $request->genre_id ?? '%';
        $artist_title = '%' . $request->artist . '%';
        $records = Record::with('genre')->where(function ($query) use ($artist_title, $genre_id) { $query->where('artist', 'like', $artist_title) ->where('genre_id', 'like', $genre_id); }) ->orWhere(function ($query) use ($artist_title, $genre_id) { $query->where('title', 'like', $artist_title) ->where('genre_id', 'like', $genre_id); })->orderBy('artist')->paginate(12)->appends(['artist' => $request->artist, 'genre_id' => $request->genre_id]);

        foreach($records as $record){
            if(!$record->cover){
                $record->cover = 'https://coverartarchive.org/release/' . $record->title_mbid . '/front-250.jpg';
            }
        }

        $genres = Genre::orderBy('name')->has('records')->withCount('records')->get()->transform(function($item, $key){
            // Set first letter of name to uppercase and add the counter
            $item->name = ucfirst($item->name);
            // Remove all fields that you don't use inside the view
            unset($item->created_at, $item->updated_at);
            return $item;
        });

        $result = compact('genres', 'records');
        \Json::dump($result);
        return view('shop.index', ['records' => $records], ['genres' => $genres]);
    }

    public function index_alt(Request $request){
        $genres = Genre::has('records')->orderBy('name')->get()->transform(function($item, $key){
            $item->name = ucfirst($item->name);
            unset($item->created_at, $item->updated_at, $item->records_count);
            return $item;
        });
        $records = Record::orderBy('artist')->get();
        $result = compact('genres', 'records');
        \Json::dump($result);
        return view('shop.index_alt', ['genres' => $genres, 'records' => $records]);
    }

    // Detail Page: http://vinyl_shop.test/shop/{id} or http://localhost:3000/shop/{id}
    public function show($id)
    {
        $record = Record::with('genre')->findOrFail($id);
        $record->cover = $record->cover ?? "https://coverartarchive.org/release/$record->title_mbid/front-250.jpg";
        $record->title = $record->artist . ' - ' . $record->title;
        $record->artistUrl = 'https://musicbrainz.org/ws/2/artist/' . $record->artist_mbid . '?inc=url-rels&fmt=json';
        $record->recordUrl = 'https://musicbrainz.org/ws/2/release/' . $record->title_mbid . '?inc=recordings+url-rels&fmt=json';
        $record->btnClass = $record->stock > 0 ? 'btn-outline-success' : 'btn-outline-danger';
        $record->genreName = $record->genre->name;
        unset($record->genre_id, $record->artist, $record->created_at, $record->updated_at, $record->artist_mbid, $record->title_mbid, $record->genre);
        $result = compact('record');
        \Json::dump($result);
        return view('shop.show', ['record' => $record]);
    }
}
