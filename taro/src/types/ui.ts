interface UserInfo {
    id: string,
    name: string,
    phone: string,
    avatar: string
}

interface MenuItem {
    title: string,
    icon: string,
    description?: string,
    link: string,
    onClick?: (item: MenuItem) => void,
}

interface MenuList {
    menuList: MenuItem[]
}

interface ArticleItem {
    id: string,
    title: string,
    date: string,
    cover: string | null,
    category: ArticleCategory
}

interface ArticleList {
    list: ArticleItem[]
}

interface ArticleDetail {
    id: string,
    title: string,
    content: string,
    cover: string | null,
    date: string,
    category: ArticleCategory | null
}

interface ArticleCategory {
    id: string,
    title: string,
    description: string,
    code: string
}

interface SectionTitle {
    title: string,
    subtitle?: string,
    link?: string,
}

export {
    SectionTitle,
    MenuItem,
    MenuList,
    UserInfo,
    ArticleItem,
    ArticleList,
    ArticleDetail,
    ArticleCategory
}