import { http } from "@/utils/request";
import { 
    ApiResponse, 
    SuggestSubmission
} from "@/types/api";

const SUGGEST_API = `/api/v1/suggests/`;

const suggestApi = {
    // 提交用户建议
    create: (data: SuggestSubmission): Promise<ApiResponse<void>> => {
        return http.post(`${SUGGEST_API}`, data);
    },
}

export default suggestApi;