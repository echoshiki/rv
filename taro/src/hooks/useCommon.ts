import { checkRegistrationStatus } from "@/api/registration";
import { useCallback, useEffect, useState } from "react";
import { RegistrationStatus } from "@/types/ui";

const useCheckRegistrationStatus = (activityId: string | undefined) => {
    const [status, setStatus] = useState<RegistrationStatus | null>(null);
    const checkStatus = useCallback(async (activityId: string | undefined) => {
        if (!activityId) return;
        try {
            const { data: responseData } = await checkRegistrationStatus(activityId);
            setStatus(responseData);
        } catch (e) {
            console.log('检查报名状态失败', e);
        }
    }, []);

    useEffect(() => {
        if (activityId) {
            checkStatus(activityId);
        }
    }, [activityId, checkStatus]);

    return {
        status,
        checkStatus
    }
}

export {
    useCheckRegistrationStatus
}