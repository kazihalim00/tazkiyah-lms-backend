<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeedPost;
use Illuminate\Support\Facades\Http;

class FeedController extends Controller
{
    public function index()
    {

        // Use query builder instead of unknown model class `FeedPost`
        $posts = \DB::table('feed_posts')->orderBy('created_at', 'desc')->get();


        $weatherData = Http::get("http://api.openweathermap.org/data/2.5/weather?q=Sylhet&appid=YOUR_API_KEY")->json();

        $condition = $weatherData['weather'][0]['main'] ?? 'Clear';
        $message = $this->getDuaByWeather($condition);

        return view('feed', compact('posts', 'message'));
    }


    private function getDuaByWeather($condition)
    {
        if ($condition == 'Rain')
            return "বৃষ্টির সময় আল্লাহর রহমত বর্ষিত হয়। এই দোয়াটি পড়ুন: আল্লাহুম্মা সাইয়্যিবান নাফিয়া।";
        return "আলহামদুলিল্লাহ, আজকের দিনটি বরকতময় হোক!";
    }
}