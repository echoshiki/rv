import { http } from "@/utils/request";
import { ArticleList } from "@/types/api";

const ARTICLE_API = `/api/v1/articles/`;

const getArticleList = ({ filter }: { filter: any }): Promise<ArticleList> => {
      return  http.get(`${ARTICLE_API}`);
};

export {
    getArticleList
};