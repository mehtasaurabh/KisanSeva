<?php
/**
* File: FarmerController.php
* Purpose: Calls the FarmerModal to fetch the data required for farmer pages.
* Date: 24/03/2017
* Author: SatyaPriya Baral
*/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FarmerModel;
use App\Http\Requests;
use App\Classes;
use Illuminate\Routing\Controller;

class FarmerController extends Controller
{

    /**
    * Function to sign in to the required user home page.
    *
    * @param 1. Reguest - Contains all data of user for login.
    * @return - Returns to the route of desired user.
    */
    public function index(Request $request)
    {
        $records = FarmerModel::userDetails('User', $request->all());
        if ($records !== false) {
            $request->session()->put('user', $records[0]->getField('___kpn_UserId'));
            $request->session()->put('name', $records[0]->getField('UserName_xt'));
            $request->session()->put('type', $records[0]->getField('__kfn_UserType'));
            if ($records[0]->getField('__kfn_UserType') == 2) {
                return redirect('dealer');
            } elseif ($records[0]->getField('__kfn_UserType') == 3) {
                return redirect('farmer');
            } else {
                return redirect('/');
            }
        }
        return redirect('/');
    }

    /**
    * Function to go to Farmer View.
    *
    * @param 1. Reguest - Contains all session data.
    * @return - Returns to the desired view of desired user.
    */
    public function farmer(Request $request)
    {
        $sessionArray = $request->session()->all();
        if (!$request->session()->has('user') || $sessionArray['type'] == 2) {
            return redirect('/');
        }
        return view('farmer.index', compact('sessionArray'));
    }
    /**
    * Function to get All Farming Tips Data.
    *
    * @param 1. Reguest - Contains all session data.
    * @return - Filemaker results of all Farming Tips found.
    */
    public function findAllTips(Request $request)
    {
        $sessionArray = $request->session()->all();
        if (!$request->session()->has('user') || $sessionArray['type'] == 2) {
            return redirect('/');
        }
        $records = FarmerModel::findAll('Tips');
        return view('farmer.farmingtips', compact('records', 'sessionArray'));
    }

    /**
    * Function to get Specific Farming Tips Data.
    *
    * @param 1. $id - contains record id of specific farming tip to be displayed.
    *        2. Reguest - Contains all session data.
    * @return - Filemaker results of Farming Tips found.
    */
    public function tipDetails(Request $request, $id)
    {
        $sessionArray = $request->session()->all();
        if (!$request->session()->has('user') || $sessionArray['type'] == 2) {
            return redirect('/');
        }
        $records = FarmerModel::find('Tips', $id, '___kpn_TipId');
        return view('farmer.tipsdetails', compact('records', 'sessionArray'));
    }

    /**
    * Function to get all the Category.
    *
    * @param 1. Reguest - Contains all session data.
    * @return - Filemaker results of Category Found.
    */
    public function findAllCategory(Request $request)
    {
        $sessionArray = $request->session()->all();
        if (!$request->session()->has('user') || $sessionArray['type'] == 2) {
            return redirect('/');
        }
        $records = FarmerModel::findAll('Category');
        return view('farmer.addpost', compact('records', 'sessionArray'));
    }

    /**
    * Function to Find all Crops Under Category.
    *
    * @param 1. $request - contains id of the Category and all session data.
    * @return - Filemaker results of Crops Found under Category.
    */
    public function findCrops(Request $request)
    {
        $sessionArray = $request->session()->all();
        if (!$request->session()->has('user') || $sessionArray['type'] == 2) {
            return redirect('/');
        }
        $records = FarmerModel::find('Crop', $request->id, '__kfn_CategoryId');
        $i = 0 ;
        $array = [];
        foreach ($records as $record) {
            $array[$i] = [$record->getField('CropName_xt'), $record->getField('___kpn_CropId')];
            $i = $i+1;
        }
        return response()->json($array);
    }

    /**
    * Function to Find all Crops Under Category.
    *
    * @param 1. $request - contains id of the Category and all session data.
    * @return - Filemaker results of Crops Found under Category.
    */
    public function findCrops(Request $request)
    {
        $sessionArray = $request->session()->all();
        if (!$request->session()->has('user') || $sessionArray['type'] == 2) {
            return redirect('/');
        }
        $records = FarmerModel::find('Crop', $request->id, '__kfn_CategoryId');
        $i = 0 ;
        $array = [];
        foreach ($records as $record) {
            $array[$i] = [$record->getField('CropName_xt'), $record->getField('___kpn_CropId')];
            $i = $i+1;
        }
        return response()->json($array);
    }

    /**
    * Function to Create a Post of the crop.
    *
    * @param 1. $request - contains all data of the post to be created and all session data.
    * @return - Returns to the Crop post page.
    */
    public function createPost(Request $request)
    {
        $sessionArray = $request->session()->all();
        if (!$request->session()->has('user') || $sessionArray['type'] == 2) {
            return redirect('/');
        }
        $return = FarmerModel::addPost('CropPost', $request->all(), $sessionArray['user']);
        if ($return == true) {
            return redirect('viewpost');
        }
        return back();
    }

    /**
    * Function to get all posts.
    *
    * @param 1. Reguest - Contains all session data.
    * @return - Array of all post data.
    */
    public function findAllPosts(Request $request)
    {
        $sessionArray = $request->session()->all();
        if (!$request->session()->has('user') || $sessionArray['type'] == 2) {
            return redirect('/');
        }
        $records = FarmerModel::find('CropPost', $sessionArray['user'], '__kfn_UserId');
        $i=0;
        foreach ($records as $record) {
            $cropRecord = FarmerModel::find('Crop', $record->getField('__kfn_CropId'), '___kpn_CropId');
            $cropDetails[$i] = [ $cropRecord[0]->getField('CropName_xt'), $cropRecord[0]->getField('___kpn_CropId')];
            $categoryRecord = FarmerModel::find('Category', $cropRecord[0]->getField('__kfn_CategoryId'), '___kpn_CategoryId');
            $categoryDetails[$i] = [$categoryRecord[0]->getField('CategoryName_xt')];
            $i = $i + 1;
        }
        $PostRecords = array(
            $records,
            $cropDetails,
            $categoryDetails
        );
        return view('farmer.ViewPost', compact('PostRecords', 'sessionArray'));
    }

    /**
    * Function to signout.
    *
    * @param 1. Reguest - Contains all session data.
    * @return - Returns to login view.
    */
    public function signout(Request $request)
    {
        $request->session()->flush();
        return redirect('/');
    }
}
