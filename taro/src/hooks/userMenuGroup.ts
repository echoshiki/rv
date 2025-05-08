import { useState, useEffect } from "react";
import { MenuItem } from "@/types/api";
import { getMenuGroup } from "@/api/menu";

const useMenuGroup = (code: string) => {
    const [menuGroup, setMenuGroup] =  useState<MenuItem[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    const fetchMenuGroup = async () => {
        // 重置状态
        setLoading(true);
        setError(null);

        try {
            // 通过菜单表示获取菜单数据
            const data = await getMenuGroup(code);
            setMenuGroup(data || []);
        } catch (e) {
            setError(e.message || 'Failed to fetch menu group.');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchMenuGroup();
    }, []);

    return {
        menuGroup,
        loading,
        error,
        refetch: fetchMenuGroup
    }
}

export {
    useMenuGroup
}