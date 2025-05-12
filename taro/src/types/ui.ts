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
    title: string,
    subtitle?: string,
    list: ArticleItem[]
}

export {
    MenuItem,
    MenuList,
    UserInfo,
    ArticleItem,
    ArticleList
}