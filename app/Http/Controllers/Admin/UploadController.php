<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ads;

class UploadController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $file_data = $_FILES['file'];
        $file['name'][] = $file_data['name'];
        $file['type'][] = $file_data['type'];
        $file['tmp_name'][] = $file_data['tmp_name'];
        $file['error'][] = $file_data['error'];
        $file['size'][] = $file_data['size'];
        return json_encode(upload_file($file));

    }

    /**
     * 删除图片
     * @param Request $request
     */
    public function DelFile(Request $request)
    {
        $file_name = $request->file_name;
        //echo json_encode($ads_pic);exit;
        $all_file_name = Ads::where('id', $request->id)->first(['ads_pic']);
        if ($request->id <> "" && $request->model == 'ads' && $all_file_name['ads_pic'] <> "") {

            $ads_pic_arr = explode(',', $all_file_name['ads_pic']);
            foreach ($ads_pic_arr as $key => $value) {
                if ($file_name == $value) {
                    unset($ads_pic_arr[$key]);
                }
            }
            $ads_pic = implode(',', $ads_pic_arr);
            Ads::where('id', $request->id)->update(['ads_pic' => $ads_pic]);
            //echo json_encode($ads_pic);exit;
        }
        echo curl_del_file(WebSites()['img_domain'], $file_name);
    }


    public function index(Request $request)
    {
        //$file=$_FILES['file'];
        return view('admin.upload');
    }


}
