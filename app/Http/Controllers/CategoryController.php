<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function allcategories() {
        $categories = Category::all();
        return view('admin.categories.all-categories',compact('categories')) ;
    }
    public function createCategories(){
        return view('admin.categories.create-category');
    }
    public function storeCategory(Request $request){
        $request->validate(['name' => ['required','string','max:255']]);
        $category = new Category();
        $category->name = $request -> name;
        $category -> save();
        return redirect()->route('all-categories');
    }
    public function editCategory($id) {
        $category = Category::find($id);
        return view('admin.categories.edit-category')->with('category',$category);
    }
    public function updateCategory(Request $request, $id){
        $request->validate(['name' => ['required','string','max:255']]);
        $category = Category::find($id);
        $category->name = $request -> name;
        $category -> update();
        return redirect()->route('all-categories');
    }
}
