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
    cover?: string
}

interface ArticleList {
    list: ArticleItem[]
}

interface ArticleDetail {
    id: string,
    title: string,
    content: string,
    cover?: string,
    date: string,
    category: any
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
    ArticleDetail
}