<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Tag;


class AddGameController extends Controller
{
    public function index()
    {
        $tags = Tag::all();

        return view('addgame', ["tags" => $tags]);
    }
    
    public function add(Request $request)
    {
        $request->validate([
            "image" => "required|image|mimes:jpg,png,jpeg,gif,svg|max:2048",
            "title" => "required|unique:products,title|min:1",
            "price" => "required|numeric|min:0",
            "quantity" => "required|numeric|min:0",
            "description" => "required|min:0",
            "more_info" => "required|min:0",
            "system_requirements" => "required|min:0",
            "release_date" => "required",
            "genre" => "required",
            "devs" => "required",
            "platform" => "required",
        ]);
        $image_path = $request->file('image')->store('image', 'public');

        $Product = new Product();
        $Product->title = $request->input("title");
        $Product->image = $image_path;
        $Product->price = $request->input("price");
        $Product->quantity = $request->input("quantity");
        $Product->description = $request->input("description");
        $Product->more_info = $request->input("more_info");
        $Product->system_requirements = $request->input("system_requirements");
        $Product->release_date = $request->input("release_date");
        $Product->platform = $request["platform"];

        $tag_g = Tag::where("tag", "like", $request["genre"]) -> firstorfail();
        $tag_d = Tag::where("tag", "like",$request["devs"]) ->firstorfail();

        $Product->save();

        $Product->tags()->attach([$tag_g->id, $tag_d->id]);

        if ($request->has("new"))
        {
            $tag_new = Tag::where("tag", "like",$request["new"]) ->firstorfail();
            $Product->tags()->attach([$tag_new->id]);
        }
        if ($request->has("pop"))
        {
            $tag_pop = Tag::where("tag", "like",$request["pop"]) ->firstorfail();
            $Product->tags()->attach([$tag_pop->id]);
        }

        $products = Product::paginate(12);

        return redirect('adminproducts')->with('products', $products);
    }
    
}
