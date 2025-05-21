import { useState, useEffect } from 'react'
import { getProvinces, getCities } from '@/api/region'
import { PickerOption } from '@nutui/nutui-react-taro';

const useRegionPicker = () => {
    const [options, setOptions] = useState<PickerOption[][]>([]);
    const [selected, setSelected] = useState<PickerOption[]>([]);
    const [loading, setLoading] = useState(false);

    // 初始化加载省份
    useEffect(() => {
        const loadProvinces = async () => {
            setLoading(true)
            try {
                const res = await getProvinces()
                const provinces = res.data.map(p => ({
                    value: p.code,
                    text: p.name,
                }))
                setOptions([provinces])
            } finally {
                setLoading(false)
            }
        }
        loadProvinces();
    }, []);

    // 加载城市
    useEffect(() => {
        const loadCities = async (provinceCode: string) => {
            setLoading(true)
            try {
                const res = await getCities(provinceCode)
                const cities = res.data.map(c => ({
                    value: c.code,
                    text: c.name,
                }));
                
                // 关键步骤：将城市数据注入对应省份的children
                setOptions(prev => {
                    const newOptions = [...prev]
                    const targetProvince = newOptions[0].find(p => p.value === provinceCode)
                    if (targetProvince) {
                        targetProvince.children = cities
                        newOptions[1] = cities // 同时更新第二级显示
                    }
                    return newOptions;
                })
            } finally {
                setLoading(false)
            }
        }

        if (selected[0]) {
            loadCities(selected[0].value.toString())
        }
    }, [selected[0]]);

    return {
        options,
        selected,
        loading,
        setSelected,
    }
}

export default useRegionPicker