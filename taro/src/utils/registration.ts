import registrationApi from "@/api/registration";

/**
 * 报名状态检查结果
 * @param canRegister - 是否可以报名
 * @param message - 需要向用户显示的消息
 * @param isError - 可选：true 表示状态检查本身遇到错误
 * @param reason - 可选：更详细的状态检查结果原因
 */

interface RegistrationStatusResult {
    canRegister: boolean,
    message: string,
    isError?: boolean,
    reason?: 'approved' | 'pending' | 'cancelled' | 'rejected' | 'api_error' | 'allowed' | 'unknown';
}

const checkRegistrationStatus = async (activityId: string): Promise<RegistrationStatusResult> => {
    if (!activityId) {
        return {
            canRegister: false,
            message: "活动 ID 不能为空",
            isError: true,
            reason: 'api_error'
        };
    }

    const { data } = await registrationApi.status(activityId);

    if (!data || data.value === 'cancelled' || data.value === 'rejected') {
        return {
            canRegister: true,
            message: "",
            isError: false,
            reason: 'allowed'
        }
    }

    if (data.value === 'approved') {
        return {
            canRegister: false,
            message: "您已报名，无需重复报名",
            isError: false,
            reason: 'approved'
        }
    }

    if (data.value === 'pending') {
        return {
            canRegister: false,
            message: "您似乎有尚未付款的报名信息，请去个人中心查看",
            isError: false,
            reason: 'pending'
        }
    }

    return {
        canRegister: false,
        message: "出现位置错误，请稍后再试",
        isError: true,
        reason: 'api_error'
    }
};

export {
    checkRegistrationStatus
}
