import { useState, useEffect, useCallback } from "react";
import { MenuItem } from "@/types/api";
import { getMenuGroup } from "@/api/menu";

const useMenu = (code: string) => {
    const [menuItems, setMenuItems] =  useState<MenuItem[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    const fetchMenuGroup = useCallback(async () => {
        // 重置状态
        setLoading(true);
        setError(null);

        try {
            // 通过菜单表示获取菜单数据
            const { data } = await getMenuGroup(code);
            setMenuItems(data || []);
        } catch (e) {
            setError(e.message || '获取菜单数据时出现问题');
        } finally {
            setLoading(false);
        }
    }, [code]);

    useEffect(() => {
        if (code) {
            // 只有 code 存在时才请求菜单数据
            fetchMenuGroup();
        } else {
            // 如果 code 不存在，清空菜单数据
            setMenuItems([]);   
            setLoading(false);
        }
    }, [fetchMenuGroup, code]);

    return {
        // 原始菜单数据
        rawMenuItems: menuItems,
        loading,
        error,
        refetch: fetchMenuGroup
    }
}

export {
    useMenu
}