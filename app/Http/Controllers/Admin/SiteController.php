<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\SetRedisController;
use App\Models\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $config = Site::getconfig('website');
        return view('admin.site.index', compact('config'));
    }

    public function attachment()
    {
        $config = Site::getconfig('attachment');

        $config['file_type'] = !empty($config['file_type']) ? explode('|', $config['file_type']) : ["mp3", "flv", "txt", "rar"];
        $config['image_type'] = !empty($config['image_type']) ? explode('|', $config['image_type']) : ["png", "jpg", "gif", "jpeg", "bmp"];

        $config['file_size'] = empty($config['file_size']) ? 2 * 1024 : $config['file_size'];
        $config['image_size'] = empty($config['image_size']) ? 2 * 1024 : $config['image_size'];
        $post_max_size = ini_get('post_max_size');
        $upload_max_filesize = ini_get('upload_max_filesize');
        return view('admin.site.attachment', compact('config', 'post_max_size', 'upload_max_filesize'));
    }

    public function optimize()
    {
        $json = file_get_contents(base_path('composer.json'));
        $dependencies = json_decode($json, true)['require'];

        $envs = [
            ['name' => 'PHP version', 'value' => 'PHP/' . PHP_VERSION],
            ['name' => 'Laravel version', 'value' => app()->version()],
            ['name' => 'CGI', 'value' => php_sapi_name()],
            ['name' => 'Uname', 'value' => php_uname()],
            ['name' => 'Server', 'value' => Arr::get($_SERVER, 'SERVER_SOFTWARE')],

            ['name' => 'Cache driver', 'value' => config('cache.default')],
            ['name' => 'Session driver', 'value' => config('session.driver')],
            ['name' => 'Queue driver', 'value' => config('queue.default')],

            ['name' => 'Timezone', 'value' => config('app.timezone')],
            ['name' => 'Locale', 'value' => config('app.locale')],
            ['name' => 'Env', 'value' => config('app.env')],
            ['name' => 'URL', 'value' => config('app.url')],
        ];
        return view('admin.site.optimize', compact('dependencies', 'envs'));
    }

    public function datecache()
    {
        return view('admin.site.datecache');
    }

    public function clearcache(Request $request)
    {
        $type = $request->post('type');
        if (!empty($type)) {

            if (isset($type['cache'])) {
                Artisan::call('cache:clear');
            }
            if (isset($type['view'])) {
                Artisan::call('view:clear');
            }
            if (isset($type['config'])) {
                Artisan::call('config:clear');
            }
            return back()->with(['status' => '??????????????????']);
        }
        return back()->with(['status' => '???????????????????????????']);
    }

    public function connection()
    {
        $config = Site::getconfig('connection');
        return view('admin.site.connection', compact('config'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);
        if (empty($data)) {
            if (request()->ajax()) {
                return response()->json([
                    'status' => 'fail',
                    'message' => '???????????????'
                ]);
            } else {
                return back()->withErrors(['status' => '???????????????']);
            }
        }

        $key = $data['sitekey'];
        Arr::forget($data, ['sitekey']);
        $rels = Site::updatePluginset($key, $data);

        if ($rels) {
            if (request()->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => '????????????'
                ]);
            } else {
                return back()->with(['status' => '????????????']);
            }
        }
        if (request()->ajax()) {
            return response()->json([
                'status' => 'fail',
                'message' => '????????????'
            ]);
        } else {
            return redirect()->to(route('admin.user'))->withErrors('????????????');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
