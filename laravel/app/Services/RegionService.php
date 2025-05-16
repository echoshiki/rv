<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class RegionService
{
    /**
     * 原始省市区数据
     */
    protected $pcaData;
    
    /**
     * 省份索引 [code => 数据]
     */
    protected $provinceIndex = [];
    
    /**
     * 城市索引 [code => 数据]
     */
    protected $cityIndex = [];
    
    /**
     * 区县索引 [code => 数据]
     */
    protected $districtIndex = [];
    
    /**
     * 名称索引 [code => name]
     */
    protected $nameIndex = [];
    
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->loadAndIndexData();
    }
    
    /**
     * 加载数据并建立索引
     */
    protected function loadAndIndexData()
    {
        $data = Cache::remember('pca_code_data', now()->addDay(), function () {
            $path = resource_path('json/pca-code.json');
            if (!file_exists($path)) {
                return [];
            }
            
            $content = file_get_contents($path);
            if (empty($content)) {
                return [];
            }
            
            return json_decode($content, true) ?: [];
        });
        
        $this->pcaData = $data;
        
        // 如果数据为空，直接返回
        if (empty($this->pcaData)) {
            return;
        }
        
        // 建立索引
        $this->buildIndexes();
    }
    
    /**
     * 构建数据索引
     */
    protected function buildIndexes()
    {
        foreach ($this->pcaData as $province) {
            $provinceCode = $province['code'];
            
            // 索引省份
            $this->provinceIndex[$provinceCode] = [
                'code' => $provinceCode,
                'name' => $province['name']
            ];
            
            // 索引省份名称
            $this->nameIndex[$provinceCode] = $province['name'];
            
            // 处理城市
            if (isset($province['children']) && is_array($province['children'])) {
                foreach ($province['children'] as $city) {
                    $cityCode = $city['code'];
                    
                    // 索引城市
                    $this->cityIndex[$cityCode] = [
                        'code' => $cityCode,
                        'name' => $city['name'],
                        'province_code' => $provinceCode
                    ];
                    
                    // 索引城市名称
                    $this->nameIndex[$cityCode] = $city['name'];
                    
                    // 处理区县
                    if (isset($city['children']) && is_array($city['children'])) {
                        foreach ($city['children'] as $district) {
                            $districtCode = $district['code'];
                            
                            // 索引区县
                            $this->districtIndex[$districtCode] = [
                                'code' => $districtCode,
                                'name' => $district['name'],
                                'city_code' => $cityCode,
                                'province_code' => $provinceCode
                            ];
                            
                            // 索引区县名称
                            $this->nameIndex[$districtCode] = $district['name'];
                        }
                    }
                }
            }
        }
    }
    
    /**
     * 获取所有省份
     */
    public function getProvinces()
    {
        return array_values($this->provinceIndex);
    }
    
    /**
     * 获取指定省份的城市
     */
    public function getCities($provinceCode)
    {
        $result = [];
        
        foreach ($this->cityIndex as $cityCode => $city) {
            if ($city['province_code'] === $provinceCode) {
                $result[] = [
                    'code' => $cityCode,
                    'name' => $city['name']
                ];
            }
        }
        
        return $result;
    }
    
    /**
     * 获取指定城市的区县
     */
    public function getDistricts($cityCode)
    {
        $result = [];
        
        foreach ($this->districtIndex as $districtCode => $district) {
            if ($district['city_code'] === $cityCode) {
                $result[] = [
                    'code' => $districtCode,
                    'name' => $district['name']
                ];
            }
        }
        
        return $result;
    }
    
    /**
     * 根据代码获取区域名称
     */
    public function getRegionNameByCode($code)
    {
        return $this->nameIndex[$code] ?? null;
    }
    
    /**
     * 获取完整地址字符串
     */
    public function getFullAddressByCode($provinceCode, $cityCode = null, $districtCode = null)
    {
        $address = '';
        
        if ($provinceCode && isset($this->nameIndex[$provinceCode])) {
            $address .= $this->nameIndex[$provinceCode];
        }
        
        if ($cityCode && isset($this->nameIndex[$cityCode])) {
            $address .= $this->nameIndex[$cityCode];
        }
        
        if ($districtCode && isset($this->nameIndex[$districtCode])) {
            $address .= $this->nameIndex[$districtCode];
        }
        
        return $address;
    }
    
    /**
     * 调试方法
     */
    public function debug()
    {
        return [
            'provinces_count' => count($this->provinceIndex),
            'cities_count' => count($this->cityIndex),
            'districts_count' => count($this->districtIndex),
            'name_index_count' => count($this->nameIndex),
            'sample_province' => array_slice($this->provinceIndex, 0, 2),
            'sample_city' => array_slice($this->cityIndex, 0, 2),
            'sample_district' => array_slice($this->districtIndex, 0, 2)
        ];
    }
}
