<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Site extends Model
{
    protected $table = 'sites';
    protected $guarded = ['id'];

    //获取配置
    protected function getconfig($key = null)
    {
        if ($key) {
            $data = Cache::get($key);
            if ($data) {
                $config = json_decode($data, true);
            } else {
                $res = $this->where('key', $key)->first();
                Cache::put($key, $res['value']);
                $config = json_decode($res['value'], true);
            }

            return $config;
        }
        return null;
    }

    //更新配置
    protected function updatePluginset($key = null, $data = [])
    {
        if (empty($key)) {
            return false;
        }
        $config = $this->where('key', $key)->first();

        if (empty($config)) {
            $this->create([
                'key' => $key,
                'value' => json_encode($data)
            ]);
        } else {

            $config->update(['value' => json_encode($data)]);
        }
        Cache::put($key, json_encode($data));
        return true;
    }
}
