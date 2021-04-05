<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Brand;
use Image;
use File;

class BrandsController extends Controller
{
  public function index()
  {
    $brands = Brand::orderBy('id', 'desc')->get();
    return view('backend.pages.brands.index', compact('brands'));
  }

  public function create()
  {
    
    return view('backend.pages.brands.create', compact('main_brands'));
  }

  public function store(Request $request)
  {
    $this->validate($request, [
      'name'  => 'required',
      'image'  => 'nullable|image',
    ],
    [
      'name.required'  => 'Please provide a Brand name',
      'image.image'  => 'Please provide a valid image with .jpg, .png, .gif, .jpeg exrension..',
    ]);

    $Brand = new Brand();
    $Brand->name = $request->name;
    $Brand->description = $request->description;
   
    //insert images also
    
    // if (is_countable($request->image) > 0) {
    //    // $image = $request->file('image');
    //     $img = time() . '.'. $image->getClientOriginalExtension();
    //     $location = public_path('images/products/' .$img);
    //     Image::make($image)->save($location);
    
    //     $Brand->image = $img;
     
    // }
    if(request()->hasFile('image')){
      $imageName = time().'.'.$request->image->extension();
      $request->image->storeAs('/Brands',$imageName,'public');
      $Brand->image=$imageName;
  }
    $Brand->save();

    session()->flash('success', 'A new Brand has added successfully !!');
    return redirect()->route('admin.brands');

  }

  public function edit($id)
  {
   
    $Brand= Brand::find($id);
    if (!is_null($Brand)) {
      return view('backend.pages.brands.edit', compact('Brand', 'main_brands'));
    }else {
      return resirect()->route('admin.brands');
    }
  }


    public function update(Request $request, $id)
    {
      $this->validate($request, [
        'name'  => 'required',
        'image'  => 'nullable|image',
      ],
      [
        'name.required'  => 'Please provide a Brand name',
        'image.image'  => 'Please provide a valid image with .jpg, .png, .gif, .jpeg exrension..',
      ]);

      $Brand = Brand::find($id);
      $Brand->name = $request->name;
      $Brand->description = $request->description;
     
      //insert images also
      if (is_countable($request->image) > 0) {
        //Delete the old image from folder

          if (File::exists('images/brands/'.$Brand->image)) {
            File::delete('images/brands/'.$Brand->image);
          }

          $image = $request->file('image');
          $img = time() . '.'. $image->getClientOriginalExtension();
          $location = public_path('storage/brands/' .$img);
          Image::make($image)->save($location);
          $Brand->image = $img;
      }
      $Brand->save();

      session()->flash('success', 'Brand has updated successfully !!');
      return redirect()->route('admin.brands');

    }

    public function delete($id)
    {
      $brand = Brand::find($id);
      if (!is_null($brand)) {
        // Delete brand image
        if (File::exists('images/brands/'.$brand->image)) {
          File::delete('images/brands/'.$brand->image);
        }
        $brand->delete();
      }
      session()->flash('success', 'Brand has deleted successfully !!');
      return back();

    }
}
 
